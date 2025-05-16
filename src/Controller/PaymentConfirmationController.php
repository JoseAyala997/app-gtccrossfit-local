<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Client;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Payment Confirmation Controller
 * 
 * Handles payment confirmation functionality for manual payment methods
 */
class PaymentConfirmationController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('GYMFunction');
        $this->loadComponent('PaymentConfirmation');
    }
    
    /**
     * Index method - List pending payments
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $session = $this->request->session()->read("User");
        
        // Only allow admins to view pending payments
        if ($session["role_name"] !== "administrator" && $session["role_name"] !== "staff_member") {
            $this->Flash->error(__('You do not have permission to access this page'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
           // Obtener el ID de sucursal del parámetro GET o usar uno predeterminado
        $branchId = null;
        if ($this->request->is('get') && isset($this->request->query['branch_id'])) {
            $branchId = $this->request->query['branch_id'];
        } else if (isset($session['branch_id'])) {
            // Usar la sucursal del usuario como predeterminada si existe
            $branchId = $session['branch_id'];
        }
        
        // Obtener lista de sucursales para el selector
        $branchTable = TableRegistry::get('GymBranch');
        $branches = $branchTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();
        
        $this->set('branches', $branches);
        $this->set('currentBranchId', $branchId);
        // Get pending payments
        $pendingPayments = $this->PaymentConfirmation->getPendingPayments($branchId);
        
        // Get member information for each payment
        $memberTable = TableRegistry::get('GymMember');
        foreach ($pendingPayments as $payment) {
            $memberId = $payment->membership_payment->member_id;
            $member = $memberTable->get($memberId);
            $payment->member = $member;
        }
        
        $this->set('pendingPayments', $pendingPayments);
    }
    
    /**
     * Confirm or reject a payment
     * 
     * @return \Cake\Http\Response|null
     */
    public function updateStatus()
    {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $session = $this->request->session()->read("User");
            
            // Only allow admins to confirm payments
            if ($session["role_name"] !== "administrator" && $session["role_name"] !== "staff_member") {
                echo json_encode(['status' => false, 'message' => __('You do not have permission to confirm payments')]);
                return;
            }
            
            // Get payment history ID and confirmation status
            $paymentHistoryId = $this->request->data['payment_history_id'];
            $confirmationStatus = $this->request->data['confirmation_status']; // 'Confirmed' or 'Rejected'
            $confirmationNote = !empty($this->request->data['confirmation_note']) ? $this->request->data['confirmation_note'] : null;
            
            // Use the PaymentConfirmation component to update the payment status
            $result = $this->PaymentConfirmation->updatePaymentStatus(
                $paymentHistoryId,
                $confirmationStatus,
                $session['id'],
                $confirmationNote
            );
              // If the payment is confirmed, update membership history and related fields
            if ($confirmationStatus === 'Confirmed' && $result['status']) {
                $this->updateMembershipFields($paymentHistoryId);
            }
            echo json_encode($result);
        }
    }

    //funcion para calcular el tiempo estimado de la membresia 
    private function updateMembershipFields($paymentHistoryId)
    {
        $membershipPaymentHistoryTable = TableRegistry::get('MembershipPaymentHistory');
        $membershipPaymentTable = TableRegistry::get('MembershipPayment');
        $membershipHistoryTable = TableRegistry::get('MembershipHistory');
    
        // Obtener el registro de historial de pagos
        $paymentHistory = $membershipPaymentHistoryTable->get($paymentHistoryId);
    
        // Obtener el registro de pago de membresía relacionado
        $membershipPayment = $membershipPaymentTable->get($paymentHistory->mp_id);
    
        // Obtener la membresía del miembro
        $membership = TableRegistry::get('Membership')->get($membershipPayment->membership_id);
    
        // Usar la fecha de hoy como punto de partida
        $today = new \DateTime('now');
        $membershipValidFrom = clone $today;
        Log::write('debug', 'Usando fecha actual como punto de partida: ' . $membershipValidFrom->format('Y-m-d'));
        
        // Verificar que membershipLengthDays sea un número
        $membershipLengthDays = intval($membership->membership_length);
        Log::write('debug', 'Membership Length Days (int): ' . $membershipLengthDays);
        
        // Calcular la fecha de vencimiento
        $interval = new \DateInterval("P{$membershipLengthDays}D");
        $membershipValidTo = clone $membershipValidFrom;
        $membershipValidTo->add($interval);
        
        Log::write('debug', 'Fecha final después de sumar ' . $membershipLengthDays . ' días a la fecha actual: ' . $membershipValidTo->format('Y-m-d'));
    
        // Calcular la fecha del primer pago (un mes desde hoy)
        $firstPayDate = clone $today;
        $firstPayDate->modify('+1 month');
        $firstPayDateFormatted = $firstPayDate->format('Y-m-d');
        
        Log::write('debug', 'Nuevo First Pay Date calculado: ' . $firstPayDateFormatted);
    
        // Actualizar la tabla membership_history
        $membershipHistory = $membershipHistoryTable->find()
            ->where(['member_id' => $membershipPayment->member_id])
            ->order(['id' => 'DESC'])
            ->first();
    
        if ($membershipHistory) {
            $membershipHistory->membership_valid_from = $membershipValidFrom->format('Y-m-d');
            $membershipHistory->membership_valid_to = $membershipValidTo->format('Y-m-d');
            $membershipHistory->first_pay_date = $firstPayDateFormatted; // Actualizar first_pay_date en membership_history
            
            if ($membershipHistoryTable->save($membershipHistory)) {
                Log::write('debug', 'Fechas actualizadas en MembershipHistory, incluyendo first_pay_date: ' . $firstPayDateFormatted);
            } else {
                Log::write('error', 'Error al guardar en MembershipHistory: ' . json_encode($membershipHistory->getErrors()));
            }
            
            // MÉTODO ADICIONAL: Actualizar directamente membership_history con SQL
            try {
                $connection = \Cake\Datasource\ConnectionManager::get('default');
                $result = $connection->execute(
                    "UPDATE membership_history SET 
                        membership_valid_from = :valid_from,
                        membership_valid_to = :valid_to,
                        first_pay_date = :first_pay_date
                     WHERE id = :id",
                    [
                        'valid_from' => $membershipValidFrom->format('Y-m-d'),
                        'valid_to' => $membershipValidTo->format('Y-m-d'),
                        'first_pay_date' => $firstPayDateFormatted,
                        'id' => $membershipHistory->id
                    ]
                );
                
                Log::write('debug', 'Actualización directa de membership_history, filas afectadas: ' . $result->rowCount());
                
            } catch (\Exception $e) {
                Log::write('error', 'Error en actualización SQL de membership_history: ' . $e->getMessage());
            }
        }
    
        // Actualizar la tabla membership_payment
        $membershipPayment->start_date = $membershipValidFrom->format('Y-m-d');
        $membershipPayment->end_date = $membershipValidTo->format('Y-m-d');
        $membershipPayment->first_pay_date = $firstPayDateFormatted;
        
        // Verificar el campo first_pay_date antes de guardar
        Log::write('debug', 'Valor de first_pay_date antes de guardar en MembershipPayment: ' . $membershipPayment->first_pay_date);
        
        if ($membershipPaymentTable->save($membershipPayment)) {
            Log::write('debug', 'MembershipPayment guardado correctamente usando ORM');
        } else {
            Log::write('error', 'Error al guardar con ORM en MembershipPayment: ' . json_encode($membershipPayment->getErrors()));
        }
        
        // Actualizar directamente membership_payment con SQL
        try {
            $connection = \Cake\Datasource\ConnectionManager::get('default');
            $result = $connection->execute(
                "UPDATE membership_payment SET 
                    start_date = :start_date,
                    end_date = :end_date,
                    first_pay_date = :first_pay_date
                 WHERE id = :id",
                [
                    'start_date' => $membershipValidFrom->format('Y-m-d'),
                    'end_date' => $membershipValidTo->format('Y-m-d'),
                    'first_pay_date' => $firstPayDateFormatted,
                    'id' => $membershipPayment->id
                ]
            );
            
            Log::write('debug', 'Actualización SQL directa de membership_payment, filas afectadas: ' . $result->rowCount());
            
            // Verificar que el valor se haya guardado correctamente
            $updatedPayment = $membershipPaymentTable->get($membershipPayment->id);
            Log::write('debug', 'Valor de first_pay_date después de actualización directa en MembershipPayment: ' . $updatedPayment->first_pay_date);
            
        } catch (\Exception $e) {
            Log::write('error', 'Error en actualización SQL directa de membership_payment: ' . $e->getMessage());
        }
        
        // Verificar el valor final en membership_history
        $updatedHistory = $membershipHistoryTable->get($membershipHistory->id);
        Log::write('debug', 'Valor final de first_pay_date en MembershipHistory: ' . $updatedHistory->first_pay_date);
    }


    // funcion para generar nueva factura 
//     public function generatePayments()
// {
//     $this->autoRender = false; // No renderizar una vista
//     $this->request->allowMethod(['get']); // Permitir solo solicitudes POST

//     $membershipHistoryTable = TableRegistry::get('MembershipHistory');
//     $membershipPaymentHistoryTable = TableRegistry::get('MembershipPaymentHistory');
//     $today = new \DateTime('now');

//     // Buscar miembros cuyo first_pay_date sea hoy
//     $membershipsToUpdate = $membershipHistoryTable->find()
//         ->where(['first_pay_date' => $today->format('Y-m-d')])
//         ->all();

//     if ($membershipsToUpdate->isEmpty()) {
//         return $this->response->withType('application/json')
//             ->withStringBody(json_encode(['status' => 'success', 'message' => 'No hay pagos pendientes para hoy.']));
//     }

//     foreach ($membershipsToUpdate as $membership) {
//         // Crear un nuevo registro en membership_payment_history
//         $newPayment = $membershipPaymentHistoryTable->newEntity([
//             'mp_id' => $membership->membership_payment_id,
//             'member_id' => $membership->member_id,
//             'amount' => $membership->membership_fee, // Ajusta según tu lógica
//             'payment_status' => 'Pending',
//             'payment_confirmation_status' => 'Pending',
//             'paid_by_date' => $today->modify('+1 month')->format('Y-m-d'), // Fecha límite de pago
//             'created_at' => $today->format('Y-m-d'),
//         ]);

//         if ($membershipPaymentHistoryTable->save($newPayment)) {
//             // Actualizar el first_pay_date en membership_history
//             $membership->first_pay_date = $today->modify('+1 month')->format('Y-m-d');
//             $membershipHistoryTable->save($membership);
//         }
//     }

//     return $this->response->withType('application/json')
//         ->withStringBody(json_encode(['status' => 'success', 'message' => 'Pagos generados correctamente.']));
// }
public function generatePayments()
{ 
    $this->Auth->allow('generatePayments');
     Log::write('debug', 'Ejecutandooo...');
    $this->autoRender = false; // No renderizar una vista
    
    // Verificar si se está ejecutando desde la consola
    $isConsole = PHP_SAPI === 'cli';
    
    // Solo verificar el método si no estamos en consola
    if (!$isConsole) {
        $this->request->allowMethod(['get']); // Permitir solo solicitudes GET
    }

    $membershipHistoryTable = TableRegistry::get('MembershipHistory');
    $membershipTable = TableRegistry::get('Membership'); // Tabla de membresías
    $membershipPaymentTable = TableRegistry::get('MembershipPayment');
    $today = new \DateTime('now');

    // Buscar miembros cuyo first_pay_date sea hoy
    $membershipsToUpdate = $membershipHistoryTable->find()
        ->where(['first_pay_date' => $today->format('Y-m-d')])
        ->all();

    if ($membershipsToUpdate->isEmpty()) {
        Log::write('debug', 'No hay pagos pendientes para hoy.');
        
        // Retornar respuesta JSON solo si no es consola
        if (!$isConsole) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['status' => 'success', 'message' => 'No hay pagos pendientes para hoy.']));
        }
        return true;
    }

    $procesados = 0;
    foreach ($membershipsToUpdate as $membership) {
        // Obtener la información de la membresía desde la tabla Membership
        $membershipDetails = $membershipTable->find()
            ->where(['id' => $membership->selected_membership])
            ->first();

        if (!$membershipDetails) {
            Log::write('error', 'No se encontró la membresía para el ID: ' . $membership->selected_membership);
            continue; // Saltar este registro si no se encuentra la membresía
        }
        Log::write('debug', 'Detalles de la membresía: ' . json_encode($membershipDetails->toArray()));
        
        // Crear un nuevo registro en membership_payment
        $newPayment = $membershipPaymentTable->newEntity([
            'member_id' => $membership->member_id,
            'membership_id' => $membership->selected_membership, // Usar selected_membership como membership_id
            'branch_id' => $membership->branch_id,
            'membership_amount' => $membershipDetails->membership_amount, // Obtener el fee desde la tabla Membership
            'paid_amount' => 0, // Inicialmente no pagado
            'start_date' => $today->format('Y-m-d'),
            'end_date' => $today->modify('+1 month')->format('Y-m-d'), // Fecha de vencimiento
            'membership_status' => 'Continue', // Estado inicial
            'payment_status' => '0', // Estado de pago inicial
            'created_date' => $today->format('Y-m-d'),
            'created_by' => 1, // ID del administrador o sistema que genera el pago
        ]);

        if ($membershipPaymentTable->save($newPayment)) {
            // Actualizar el first_pay_date en membership_history
            $membership->first_pay_date = $today->modify('+1 month')->format('Y-m-d');
            $membershipHistoryTable->save($membership);
            $procesados++;
            Log::write('debug', 'Pago generado para el miembro ID: ' . $membership->member_id);
        } else {
            Log::write('error', 'Error al guardar el pago para el miembro ID: ' . $membership->member_id);
        }
    }

    Log::write('debug', 'Pagos generados: ' . $procesados);
    
    // Retornar respuesta JSON solo si no es consola
    if (!$isConsole) {
        return $this->response->withType('application/json')
            ->withStringBody(json_encode(['status' => 'success', 'message' => 'Pagos generados correctamente en membership_payment.']));
    }
    return true;
}
    /**
     * View receipt photo
     * 
     * @param int $paymentHistoryId Payment history ID
     * @return \Cake\Http\Response|null
     */

    public function viewReceipt($paymentHistoryId)
    {
        $session = $this->request->session()->read("User");
        
        // Only allow admins to view receipts
        if ($session["role_name"] !== "administrator" && $session["role_name"] !== "staff_member") {
            $this->Flash->error(__('You do not have permission to access this page'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
        
        // Get payment history record
        $membershipPaymentHistoryTable = TableRegistry::get('MembershipPaymentHistory');
        $paymentHistory = $membershipPaymentHistoryTable->get($paymentHistoryId);
        
        // Get member information
        $membershipPaymentTable = TableRegistry::get('MembershipPayment');
        $membershipPayment = $membershipPaymentTable->get($paymentHistory->mp_id);
        
        $memberTable = TableRegistry::get('GymMember');
        $member = $memberTable->get($membershipPayment->member_id);
        
        $this->set('paymentHistory', $paymentHistory);
        $this->set('member', $member);
    }

    // funcion para el historial de pagos
    /**
 * View payment history
 * 
 * @return \Cake\Http\Response|null
 */
public function paymentHistory()
{
    $session = $this->request->session()->read("User");
    
    // Only allow admins and staff to view payment history
    if ($session["role_name"] !== "administrator" && $session["role_name"] !== "staff_member") {
        $this->Flash->error(__('You do not have permission to access this page'));
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }
    
    // Obtener el ID de sucursal del parámetro GET o usar uno predeterminado
    $branchId = null;
    if ($this->request->is('get') && isset($this->request->query['branch_id'])) {
        $branchId = $this->request->query['branch_id'];
    } else if (isset($session['branch_id'])) {
        // Usar la sucursal del usuario como predeterminada si existe
        $branchId = $session['branch_id'];
    }
    
    // Obtener lista de sucursales para el selector
    $branchTable = TableRegistry::get('GymBranch');
    $branches = $branchTable->find('list', [
        'keyField' => 'id',
        'valueField' => 'name'
    ])->toArray();
    
    $this->set('branches', $branches);
    $this->set('currentBranchId', $branchId);
    
    // Get payment history
    $paymentHistory = $this->PaymentConfirmation->getPaymentHistory($branchId);
    
    $this->set('paymentHistory', $paymentHistory);
    $this->viewBuilder()->setTemplate('../MembershipPayment/payment_history');
}

// Metodos para pagopar 
public function checkout($mp_id)
    {
        $this->autoRender = false;

        $paymentTable = TableRegistry::get('MembershipPayment');
        $payment = $paymentTable->get($mp_id);

        // ⚠️ Llaves ficticias, debes cambiarlas por las reales
        $public_key = '99e72c277c5a2e5f2d9a52efc47afe03';
        $private_key = 'da0bce1371c6d3304f88d91e32c1fb61';

        $amount = $payment->amount;

        $client = new Client();
        $response = $client->post('https://api.pagopar.com/api/1.1/transaction/create', [
            'public_key' => $public_key,
            'token' => hash('sha1', $private_key . $payment->id),
            'order_id' => $payment->id,
            'amount' => $amount,
            'description' => 'Pago de membresía',
            'return_url' => 'http://localhost',
            'cancel_url' => 'http://localhost',
        ], ['type' => 'json']);

        $result = $response->getJson();

        if (isset($result['respuesta']) && $result['respuesta'] === 'OK') {
            return $this->redirect($result['resultado']['checkout_url']);
        } else {
            $this->Flash->error(__('Error al iniciar pago en PagoPar.'));
            return $this->redirect($this->referer());
        }
    }

    public function retorno()
{
    $this->autoRender = false;
    $orderId = $this->request->getQuery('order_id');

    $paymentTable = TableRegistry::get('MembershipPaymentHistory');
    $payment = $paymentTable->find()->where(['mp_id' => $orderId])->first();

    if ($payment) {
        $payment->payment_confirmation_status = 'Confirmed';
        $paymentTable->save($payment);
        $this->Flash->success(__('Pago confirmado exitosamente.'));
    } else {
        $this->Flash->error(__('No se encontró el pago.'));
    }

    return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
}
}
