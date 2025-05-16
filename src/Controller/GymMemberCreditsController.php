<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Handles member credit operations and usage
 */
class GymMemberCreditsController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('GYMFunction');
        $this->loadComponent('Csrf');
        $this->loadComponent('RequestHandler');

        $this->loadModel('GymMemberCredits');
        $this->loadModel('CreditUsage');
        $this->loadModel('ClassSchedule');
        $this->loadModel('GymBranch');
        $this->loadModel('GymMemberMemberships');
    }
    /**
     * List all members for credit management
     */
    public function index()
    {
        $session = $this->request->session()->read("User");

        // Solo permitir acceso a administradores
        if ($session['role_name'] !== 'administrator' && $session['role_name'] !== 'staff_member') {
            $this->Flash->error(__('You are not authorized to access this page'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        $gymMemberTable = TableRegistry::getTableLocator()->get('GymMember');

        // Get members with active memberships
        $members = $gymMemberTable->find()
            ->select(['id', 'first_name', 'last_name', 'email', 'mobile', 'image'])
            ->contain([
                'GymMemberMemberships' => function ($q) {
                    return $q->where([
                        'status' => 'active',
                        'start_date <=' => date('Y-m-d'),
                        'end_date >=' => date('Y-m-d')
                    ]);
                }
            ])
            ->where(['role_name' => 'member'])
            ->order(['first_name' => 'ASC'])
            ->toArray();

        $this->set(compact('members'));
    }
    /**
     * View member's credit balance and usage history
     */
    public function viewCredits($memberId = null)
    {
        $session = $this->request->session()->read("User");
        if (empty($memberId)) {
            $memberId = $session['id'];
        }
    
        // Verificar permisos de acceso
        if ($session['role_name'] !== 'administrator' && $session['id'] !== (int)$memberId) {
            $this->Flash->error(__('Unauthorized access'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    
        // Obtener créditos disponibles por sucursal
        $branchCredits = $this->GymMemberCredits->find()
            ->select([
                'GymMemberCredits.credits_remaining', // Créditos restantes
                'GymBranch.name',                    // Nombre de la sucursal
                'GymMemberCredits.updated_at',       // Última actualización
            ])
            ->contain(['GymBranch']) // Relación con sucursales
            ->where([
                'GymMemberCredits.gym_member_membership_id IN' => $this->GymMemberMemberships->find()
                    ->select(['id'])
                    ->where(['member_id' => $memberId, 'status' => 'active'])
            ])
            ->all();
    
        // Obtener historial de uso de créditos
        $creditUsage = $this->CreditUsage->find()
            ->select([
                'CreditUsage.used_at',               // Fecha de uso
                'ClassSchedule.class_name',          // Nombre de la clase
                'GymBranch.name',                    // Nombre de la sucursal
            ])
            ->contain(['ClassSchedule', 'GymBranch']) // Relación con clases y sucursales
            ->where([
                'CreditUsage.gym_member_credit_id IN' => $this->GymMemberCredits->find()
                    ->select(['id'])
                    ->where([
                        'gym_member_membership_id IN' => $this->GymMemberMemberships->find()
                            ->select(['id'])
                            ->where(['member_id' => $memberId, 'status' => 'active'])
                    ])
            ])
            ->order(['CreditUsage.used_at' => 'DESC'])
            ->all();
    
        $this->set(compact('branchCredits', 'creditUsage'));
    }
    // public function viewCredits($memberId = null)
    // {
    //     $session = $this->request->session()->read("User");
    //     if (empty($memberId)) {
    //         $memberId = $session['id'];
    //     }

    //     // Check access permissions
    //     if ($session['role_name'] !== 'administrator' && $session['id'] !== (int)$memberId) {
    //         $this->Flash->error(__('Unauthorized access'));
    //         return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    //     }

    //     $credits = $this->GymMemberCredits->find()
    //         ->contain(['GymMemberMemberships', 'CreditUsage' => function($q) {
    //             return $q->contain(['ClassSchedule', 'GymBranch'])
    //                 ->order(['CreditUsage.used_at' => 'DESC']);
    //         }])
    //         ->where(['GymMemberMemberships.member_id' => $memberId])
    //         ->first();

    //     $this->set(compact('credits'));
    // }

    /**
     * Use credits for a class
     */
    public function useCredits($memberCreditId, $classId, $branchId, $creditsUsed)
{
    $creditUsage = TableRegistry::getTableLocator()->get('CreditUsage');
    $gymMemberCredits = TableRegistry::getTableLocator()->get('GymMemberCredits');

    $memberCredit = $gymMemberCredits->get($memberCreditId);

    if ($memberCredit->credits_remaining < $creditsUsed) {
        return ['status' => false, 'message' => __('Insufficient credits')];
    }

    $creditUsage->save($creditUsage->newEntity([
        'gym_member_credit_id' => $memberCreditId,
        'class_schedule_id' => $classId,
        'branch_id' => $branchId,
        'credits_used' => $creditsUsed
    ]));

    $memberCredit->credits_remaining -= $creditsUsed;
    $gymMemberCredits->save($memberCredit);

    return ['status' => true, 'message' => __('Credits used successfully')];
}
    // public function useCredits()
    // {
    //     $this->autoRender = false;

    //     if (!$this->request->is('ajax')) {
    //         return $this->response->withStatus(400, 'Bad Request');
    //     }

    //     $data = $this->request->getData();
    //     $memberCreditId = $data['member_credit_id'] ?? null;
    //     $classId = $data['class_id'] ?? null;
    //     $branchId = $data['branch_id'] ?? null;

    //     if (!$memberCreditId || !$classId || !$branchId) {
    //         return json_encode(['status' => 'error', 'message' => __('Missing required parameters')]);
    //     }

    //     // Start transaction
    //     $connection = $this->GymMemberCredits->getConnection();
    //     $connection->begin();

    //     try {
    //         // Get member credits with lock
    //         $memberCredits = $this->GymMemberCredits->find()
    //             ->where(['id' => $memberCreditId])
    //             ->epilog('FOR UPDATE')
    //             ->first();

    //         if (!$memberCredits || $memberCredits->credits_remaining < 1) {
    //             throw new \Exception(__('Insufficient credits'));
    //         }

    //         // Create credit usage record
    //         $usage = $this->CreditUsage->newEntity([
    //             'gym_member_credit_id' => $memberCreditId,
    //             'class_schedule_id' => $classId,
    //             'branch_id' => $branchId,
    //             'used_at' => date('Y-m-d H:i:s')
    //         ]);

    //         if (!$this->CreditUsage->save($usage)) {
    //             throw new \Exception(__('Failed to record credit usage'));
    //         }

    //         // Decrease available credits
    //         $memberCredits->credits_remaining--;
    //         if (!$this->GymMemberCredits->save($memberCredits)) {
    //             throw new \Exception(__('Failed to update credit balance'));
    //         }

    //         $connection->commit();
    //         return json_encode(['status' => 'success', 'credits_remaining' => $memberCredits->credits_remaining]);
    //     } catch (\Exception $e) {
    //         $connection->rollback();
    //         return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    //     }
    // }

    /**
     * Check if member has credits available for a specific branch
     */
    public function checkCredits($memberId, $branchId)
    {
        $this->autoRender = false;

        if (!$this->request->is('ajax')) {
            return $this->response->withStatus(400, 'Bad Request');
        }

        $credits = $this->GymMemberCredits->find()
            ->contain(['GymMemberMemberships.Membership.MembershipCredits.MembershipCreditBranches'])
            ->where([
                'GymMemberMemberships.member_id' => $memberId,
                'GymMemberCredits.credits_remaining >' => 0,
                'MembershipCreditBranches.branch_id' => $branchId
            ])
            ->first();

        return json_encode([
            'status' => 'success',
            'has_credits' => !empty($credits),
            'credits_remaining' => $credits ? $credits->credits_remaining : 0
        ]);
    }




    //nuevas funcionalidades para creditos
    /**
     * Asignar créditos a un miembro cuando se activa su membresía
     */

     public function assignCreditsToMember($memberId)
{
    $gymMemberMemberships = TableRegistry::getTableLocator()->get('GymMemberMemberships');
    $membershipCredits = TableRegistry::getTableLocator()->get('MembershipCredits');
    $membershipCreditBranches = TableRegistry::getTableLocator()->get('MembershipCreditBranches');
    $gymMemberCredits = TableRegistry::getTableLocator()->get('GymMemberCredits');

    $memberMembership = $gymMemberMemberships->find()
        ->where(['member_id' => $memberId, 'status' => 'active'])
        ->contain(['Membership'])
        ->first();

    if (!$memberMembership) {
        return ['status' => false, 'message' => __('No active membership found')];
    }

    $credits = $membershipCredits->find()
        ->where(['membership_id' => $memberMembership->membership_id])
        ->first();

    $branches = $membershipCreditBranches->find()
        ->where(['membership_credit_id' => $credits->id])
        ->all();

    foreach ($branches as $branch) {
        $gymMemberCredits->save($gymMemberCredits->newEntity([
            'gym_member_membership_id' => $memberMembership->id,
            'branch_id' => $branch->branch_id,
            'credits_remaining' => $credits->credits
        ]));
    }

    return ['status' => true, 'message' => __('Credits assigned successfully')];
}


    // public function assignCreditsToMember($memberMembershipId)
    // {
    //     // Cargar modelos
    //     $gymMemberMemberships = TableRegistry::getTableLocator()->get('GymMemberMemberships');
    //     $membershipCredits = TableRegistry::getTableLocator()->get('MembershipCredits');
    //     $gymMemberCredits = TableRegistry::getTableLocator()->get('GymMemberCredits');

    //     // Obtener información de la membresía del miembro
    //     $memberMembership = $gymMemberMemberships->get($memberMembershipId, [
    //         'contain' => ['Membership']
    //     ]);

    //     if (!$memberMembership) {
    //         return [
    //             'status' => false,
    //             'message' => __('Member membership not found')
    //         ];
    //     }

    //     // Buscar los créditos definidos para esta membresía
    //     $creditsInfo = $membershipCredits->find()
    //         ->where(['membership_id' => $memberMembership->membership_id])
    //         ->first();

    //     if (!$creditsInfo) {
    //         return [
    //             'status' => false,
    //             'message' => __('No credits defined for this membership type')
    //         ];
    //     }

    //     // Verificar si ya existen créditos para esta membresía
    //     $existingCredits = $gymMemberCredits->find()
    //         ->where(['gym_member_membership_id' => $memberMembershipId])
    //         ->first();

    //     if ($existingCredits) {
    //         // Actualizar créditos existentes
    //         $existingCredits->credits_remaining += $creditsInfo->credits;

    //         if ($gymMemberCredits->save($existingCredits)) {
    //             return [
    //                 'status' => true,
    //                 'message' => __('Credits updated successfully'),
    //                 'credits' => $existingCredits->credits_remaining
    //             ];
    //         } else {
    //             return [
    //                 'status' => false,
    //                 'message' => __('Failed to update credits')
    //             ];
    //         }
    //     } else {
    //         // Crear un nuevo registro de créditos
    //         $newCredits = $gymMemberCredits->newEntity([
    //             'gym_member_membership_id' => $memberMembershipId,
    //             'credits_remaining' => $creditsInfo->credits
    //         ]);

    //         if ($gymMemberCredits->save($newCredits)) {
    //             return [
    //                 'status' => true,
    //                 'message' => __('Credits assigned successfully'),
    //                 'credits' => $newCredits->credits_remaining
    //             ];
    //         } else {
    //             return [
    //                 'status' => false,
    //                 'message' => __('Failed to assign credits')
    //             ];
    //         }
    //     }
    // }

    /**
     * Usar créditos para una clase en otra sucursal
     */
    public function useCreditsForClass($memberId, $classId, $branchId)
    {
        // Cargar modelos
        $gymMemberMemberships = TableRegistry::getTableLocator()->get('GymMemberMemberships');
        $gymMemberCredits = TableRegistry::getTableLocator()->get('GymMemberCredits');
        $creditUsage = TableRegistry::getTableLocator()->get('CreditUsage');
        $classSchedule = TableRegistry::getTableLocator()->get('ClassSchedule');

        // Obtener la membresía activa del miembro
        $memberMembership = $gymMemberMemberships->find()
            ->where([
                'member_id' => $memberId,
                'status' => 'active',
                'payment_status' => 'paid',
                'start_date <=' => date('Y-m-d'),
                'end_date >=' => date('Y-m-d')
            ])
            ->contain(['Membership'])
            ->first();

        if (!$memberMembership) {
            return [
                'status' => false,
                'message' => __('No active membership found')
            ];
        }

        // Obtener los créditos disponibles para el miembro
        $memberCredits = $gymMemberCredits->find()
            ->where(['gym_member_membership_id' => $memberMembership->id])
            ->first();

        if (!$memberCredits || $memberCredits->credits_remaining <= 0) {
            return [
                'status' => false,
                'message' => __('Insufficient credits')
            ];
        }

        // Verificar si la clase pertenece a otra sucursal
        $class = $classSchedule->get($classId, [
            'contain' => []
        ]);

        // Si la clase está en la misma sucursal que la membresía, no usar créditos
        if ($memberMembership->membership->branch_id == $class->branch_id) {
            return [
                'status' => false,
                'message' => __('Credits not needed for this branch')
            ];
        }

        // Iniciar transacción
        $connection = $gymMemberCredits->getConnection();
        $connection->begin();

        try {
            // Crear registro de uso de créditos
            $usage = $creditUsage->newEntity([
                'gym_member_credit_id' => $memberCredits->id,
                'class_schedule_id' => $classId,
                'branch_id' => $branchId,
                'used_at' => date('Y-m-d H:i:s')
            ]);

            if (!$creditUsage->save($usage)) {
                throw new \Exception(__('Failed to record credit usage'));
            }

            // Actualizar saldo de créditos
            $memberCredits->credits_remaining--;

            if (!$gymMemberCredits->save($memberCredits)) {
                throw new \Exception(__('Failed to update credit balance'));
            }

            $connection->commit();

            return [
                'status' => true,
                'message' => __('Credit used successfully'),
                'credits_remaining' => $memberCredits->credits_remaining
            ];
        } catch (\Exception $e) {
            $connection->rollback();

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar saldo de créditos para un miembro
     */
    public function checkMemberCredits($memberId)
    {
        // Cargar modelos
        $gymMemberMemberships = TableRegistry::getTableLocator()->get('GymMemberMemberships');
        $gymMemberCredits = TableRegistry::getTableLocator()->get('GymMemberCredits');

        // Obtener membresía activa
        $memberMembership = $gymMemberMemberships->find()
            ->where([
                'member_id' => $memberId,
                'status' => 'active',
                'payment_status' => 'paid',
                'start_date <=' => date('Y-m-d'),
                'end_date >=' => date('Y-m-d')
            ])
            ->first();

        if (!$memberMembership) {
            return [
                'status' => false,
                'message' => __('No active membership found'),
                'credits' => 0
            ];
        }

        // Obtener créditos disponibles
        $memberCredits = $gymMemberCredits->find()
            ->where(['gym_member_membership_id' => $memberMembership->id])
            ->first();

        if (!$memberCredits) {
            return [
                'status' => false,
                'message' => __('No credits found'),
                'credits' => 0
            ];
        }

        return [
            'status' => true,
            'message' => __('Credits found'),
            'credits' => $memberCredits->credits_remaining
        ];
    }


    // interfas para manejar creditos

    public function manageCredits($memberId = null)
    {
        $this->set('title', __('Manage Member Credits'));

        if (!$memberId) {
            $this->Flash->error(__('Member ID is required'));
            return $this->redirect(['action' => 'index']);
        }

        // Cargar tablas
        $gymMemberTable = TableRegistry::getTableLocator()->get('GymMember');
        $membershipTable = TableRegistry::getTableLocator()->get('Membership');
        $gymMemberMembershipsTable = TableRegistry::getTableLocator()->get('GymMemberMemberships');
        $gymMemberCreditsTable = TableRegistry::getTableLocator()->get('GymMemberCredits');

        // Obtener datos del miembro
        $member = $gymMemberTable->get($memberId, [
            'contain' => []
        ]);

        if (!$member) {
            $this->Flash->error(__('Member not found'));
            return $this->redirect(['action' => 'index']);
        }

        // Obtener membresía activa
        $activeMembership = $gymMemberMembershipsTable->find()
            ->where([
                'member_id' => $memberId,
                'status' => 'active',
                'start_date <=' => date('Y-m-d'),
                'end_date >=' => date('Y-m-d')
            ])
            ->contain(['Membership'])
            ->first();

        // Obtener créditos actuales
        $memberCredits = null;
        if ($activeMembership) {
            $memberCredits = $gymMemberCreditsTable->find()
                ->where(['gym_member_membership_id' => $activeMembership->id])
                ->first();
        }

        // Procesar formulario para ajustar créditos
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (!$activeMembership) {
                $this->Flash->error(__('Member does not have an active membership'));
                return $this->redirect(['action' => 'manageCredits', $memberId]);
            }

            if (!$memberCredits) {
                // Crear registro de créditos si no existe
                $memberCredits = $gymMemberCreditsTable->newEntity([
                    'gym_member_membership_id' => $activeMembership->id,
                    'credits_remaining' => (int)$data['credits']
                ]);
            } else {
                // Actualizar créditos existentes
                if (isset($data['credits_adjustment']) && $data['credits_adjustment'] !== '') {
                    $adjustment = (int)$data['credits_adjustment'];

                    // Ajuste relativo (sumar o restar)
                    $memberCredits->credits_remaining += $adjustment;

                    // Asegurar que los créditos no sean negativos
                    if ($memberCredits->credits_remaining < 0) {
                        $memberCredits->credits_remaining = 0;
                    }
                } else if (isset($data['credits']) && $data['credits'] !== '') {
                    // Establecer valor absoluto
                    $memberCredits->credits_remaining = (int)$data['credits'];
                }
            }

            if ($gymMemberCreditsTable->save($memberCredits)) {
                $this->Flash->success(__('Credits updated successfully'));
            } else {
                $this->Flash->error(__('Failed to update credits'));
            }

            return $this->redirect(['action' => 'manageCredits', $memberId]);
        }

        $this->set(compact('member', 'activeMembership', 'memberCredits'));
    }
}
