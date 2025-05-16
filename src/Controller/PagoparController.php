<?php
// src/Controller/PagoparController.php
namespace App\Controller;

use Cake\Http\Client;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Controller\Component\RequestHandlerComponent;
use Cake\Http\Exception\BadRequestException;

class PagoparController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        // Permitir acceso sin login a esta acción
        $this->Auth->allow(['notificar', 'resultado', 'obtenerEstadoPedido']);
        // $this->Auth->allow(['notificar']);
        // $this->Auth->allow(['resultado']);
    }
    public function checkout($mp_id = null)
    {
        if (!$mp_id) {
            $this->Flash->error(__('ID de pago no proporcionado'));
            return $this->redirect(['controller' => 'MembershipPayment', 'action' => 'paymentList']);
        }

        // Obtener datos del pago y la membresía
        $membershipPaymentTable = TableRegistry::getTableLocator()->get('MembershipPayment');
        try {
            $payment = $membershipPaymentTable->get($mp_id, [
                'contain' => ['GymMember', 'Membership']
            ]);
        } catch (\Exception $e) {
            $this->Flash->error(__('Pago no encontrado'));
            return $this->redirect(['controller' => 'MembershipPayment', 'action' => 'paymentList']);
        }

        // Obtener el último registro de pago pendiente
        $historyTable = TableRegistry::getTableLocator()->get('MembershipPaymentHistory');
        $lastPayment = $historyTable->find()
            ->where([
                'mp_id' => $mp_id,
                'payment_method' => 'Pago Online',
                'payment_confirmation_status' => 'Pending'
            ])
            ->order(['payment_history_id' => 'DESC'])
            ->first();

        if (!$lastPayment) {
            $this->Flash->error(__('No se encontró un pago pendiente'));
            return $this->redirect(['controller' => 'MembershipPayment', 'action' => 'paymentList']);
        }

        // Obtener datos de la sucursal correspondiente a la membresía
        $branchTable = TableRegistry::getTableLocator()->get('GymBranch');
        $branch = null;

        if (!empty($payment->membership->branch_id)) {
            try {
                $branch = $branchTable->get($payment->membership->branch_id);
                \Cake\Log\Log::write('debug', 'Sucursal encontrada: ' . json_encode($branch));
            } catch (\Exception $e) {
                \Cake\Log\Log::write('error', 'Error al obtener la sucursal: ' . $e->getMessage());
            }
        }

        // Claves proporcionadas por Pagopar
        $publicKey = '99e72c277c5a2e5f2d9a52efc47afe03';
        $privateKey = 'da0bce1371c6d3304f88d91e32c1fb61';

        // Datos del pedido con información real
        $idPedido = 'MP' . $mp_id . '_' . time(); // Identificador único
        $montoTotal = $lastPayment->amount;

        // Generar token
        $token = sha1($privateKey . $idPedido . strval(floatval($montoTotal)));

        // Fecha máxima de pago (2 días)
        $fechaMaximaPago = (new Time())->addDays(2)->format('Y-m-d H:i:s');

        // Obtener datos del miembro
        $member = $payment->gym_member;
        $membership = $payment->membership;

        // Datos de la sucursal (con valores predeterminados si no se encuentra)
        $branchName = $branch ? $branch->name : 'Sucursal Principal';
        $branchAddress = $branch && !empty($branch->address) ? $branch->address : 'Dirección no disponible';
        $branchPhone = $branch && !empty($branch->phone) ? $branch->phone : '+595972000000';
        $branchEmail = $branch && !empty($branch->email) ? $branch->email : 'info@gtccrossfit.com';

        $data = [
            'token' => $token,
            'comprador' => [
                'ruc' => !empty($member->ruc) ? $member->ruc : '',
                'email' => !empty($member->email) ? $member->email : 'cliente@example.com',
                'ciudad' => '1',
                'nombre' => $member->first_name . ' ' . $member->last_name,
                'telefono' => !empty($member->mobile) ? $member->mobile : '',
                'direccion' => !empty($member->address) ? $member->address : '',
                'documento' => !empty($member->member_id) ? $member->member_id : '',
                'coordenadas' => '',
                'razon_social' => $member->first_name . ' ' . $member->last_name,
                'tipo_documento' => 'CI',
                'direccion_referencia' => ''
            ],
            'public_key' => $publicKey,
            'monto_total' => $montoTotal,
            'tipo_pedido' => 'VENTA-COMERCIO',
            'compras_items' => [
                [
                    'ciudad' => '1',
                    'nombre' => 'Pago de Membresía: ' . $membership->membership_label,
                    'cantidad' => 1,
                    'categoria' => '909',
                    'public_key' => $publicKey,
                    'url_imagen' => '',
                    'descripcion' => 'Pago de Membresía desde: ' . date('d/m/Y', strtotime($payment->start_date)) . ' hasta: ' . date('d/m/Y', strtotime($payment->end_date)),
                    'id_producto' => $mp_id,
                    'precio_total' => '1000',
                    // Usar los datos de la sucursal correspondiente
                    'vendedor_telefono' => $branchPhone,
                    'vendedor_direccion' => $branchAddress,
                    'vendedor_direccion_referencia' => 'Sucursal: ' . $branchName,
                    'vendedor_direccion_coordenadas' => ''
                ]
            ],
            'fecha_maxima_pago' => $fechaMaximaPago,
            'id_pedido_comercio' => $idPedido,
            'descripcion_resumen' => 'Pago de Membresía: ' . $membership->membership_label . ' (Sucursal: ' . $branchName . ')',
            'forma_pago' => 9
        ];

        // Registrar datos que se enviarán a Pagopar
        \Cake\Log\Log::write('debug', 'Datos enviados a Pagopar: ' . json_encode($data));

        // Hacer la petición a Pagopar
        $http = new Client();
        $response = $http->post('https://api.pagopar.com/api/comercios/2.0/iniciar-transaccion', json_encode($data), [
            'type' => 'json'
        ]);

        $body = $response->getJson();
        \Cake\Log\Log::write('debug', 'Respuesta de Pagopar: ' . json_encode($body));

        if (isset($body['respuesta']) && $body['respuesta'] === true) {
            $hashPedido = $body['resultado'][0]['data'];

            // Actualizar el registro de pago con el hash de Pagopar
            $lastPayment->trasaction_id = $hashPedido;
            $historyTable->save($lastPayment);

            // Guardar referencia en la tabla de pedidos si existe
            try {
                $pedidosTable = TableRegistry::getTableLocator()->get('Pedidos');

                // Crear un objeto JSON con información detallada
                $additionalData = [
                    // Datos del miembro
                    'member' => [
                        'id' => $member->id,
                        'name' => $member->first_name . ' ' . $member->last_name,
                        'email' => $member->email,
                        'mobile' => $member->mobile,
                        'address' => $member->address,
                        'member_id' => $member->member_id
                    ],
                    // Datos de la membresía
                    'membership' => [
                        'id' => $membership->id,
                        'label' => $membership->membership_label,
                        'amount' => $membership->membership_amount,
                        'period' => $membership->membership_period,
                        'start_date' => $payment->start_date,
                        'end_date' => $payment->end_date
                    ],
                    // Datos de la sucursal
                    'branch' => [
                        'id' => $branch ? $branch->id : null,
                        'name' => $branchName,
                        'address' => $branchAddress,
                        'phone' => $branchPhone,
                        'email' => $branchEmail
                    ],
                    // Datos del pago
                    'payment' => [
                        'amount' => $montoTotal,
                        'payment_method' => 'Pago Online',
                        'payment_date' => date('Y-m-d H:i:s'),
                        'history_id' => $lastPayment->payment_history_id,
                        'transaction_details' => [
                            'id_pedido_comercio' => $idPedido,
                            'hash_pedido' => $hashPedido,
                            'fecha_maxima_pago' => $fechaMaximaPago
                        ]
                    ],
                    // Datos de la respuesta de Pagopar
                    'pagopar_response' => $body['resultado'][0]
                ];

                $pedido = $pedidosTable->newEntity([
                    'id_comercio' => $idPedido,
                    'hash_pagopar' => $hashPedido,
                    'estado_pago' => 'pendiente',
                    'mp_id' => $mp_id,
                    'branch_id' => $payment->membership->branch_id,
                    'additional_data' => json_encode($additionalData)
                ]);

                $pedidosTable->save($pedido);
                \Cake\Log\Log::write('debug', 'Pedido guardado con datos adicionales');
            } catch (\Exception $e) {
                \Cake\Log\Log::write('error', 'Error al guardar en tabla Pedidos: ' . $e->getMessage());
            }

            // Redireccionar al checkout de Pagopar
            return $this->redirect("https://www.pagopar.com/pagos/{$hashPedido}");
        } else {
            // Manejar el error
            $errorMsg = isset($body['resultado']) ? json_encode($body['resultado']) : 'Error desconocido';
            \Cake\Log\Log::write('error', 'Error al iniciar transacción: ' . $errorMsg);
            $this->Flash->error(__('Error al iniciar transacción: ' . $errorMsg));
            return $this->redirect(['controller' => 'MembershipPayment', 'action' => 'paymentList']);
        }
    }
    // public function checkout($mp_id = null)
    // {
    //     // Claves proporcionadas por Pagopar
    //     $publicKey = '99e72c277c5a2e5f2d9a52efc47afe03';
    //     $privateKey = 'da0bce1371c6d3304f88d91e32c1fb61';

    //     // Datos del pedido
    //     $idPedido = '113344';
    //     $montoTotal = 1000;

    //     // Generar token
    //     $token = sha1($privateKey . $idPedido . strval(floatval($montoTotal)));

    //     // Fecha máxima de pago
    //     $fechaMaximaPago = (new Time())->addDays(2)->format('Y-m-d H:i:s');

    //     $data = [
    //         'token' => $token,
    //         'comprador' => [
    //             'ruc' => '4247903-7',
    //             'email' => 'fernandogoetz@gmail.com',
    //             'ciudad' => '1',
    //             'nombre' => 'Rudolph Goetz',
    //             'telefono' => '+595972200046',
    //             'direccion' => '',
    //             'documento' => '4247903',
    //             'coordenadas' => '',
    //             'razon_social' => 'Rudolph Goetz',
    //             'tipo_documento' => 'CI',
    //             'direccion_referencia' => ''
    //         ],
    //         'public_key' => $publicKey,
    //         'monto_total' => $montoTotal,
    //         'tipo_pedido' => 'VENTA-COMERCIO',
    //         'compras_items' => [
    //             [
    //                 'ciudad' => '1',
    //                 'nombre' => 'Ticket virtual a evento Ejemplo 2017',
    //                 'cantidad' => 1,
    //                 'categoria' => '909',
    //                 'public_key' => $publicKey,
    //                 'url_imagen' => 'http://www.example.com/ticket.png',
    //                 'descripcion' => 'Ticket virtual a evento Ejemplo 2017',
    //                 'id_producto' => 895,
    //                 'precio_total' => $montoTotal,
    //                 'vendedor_telefono' => '',
    //                 'vendedor_direccion' => '',
    //                 'vendedor_direccion_referencia' => '',
    //                 'vendedor_direccion_coordenadas' => ''
    //             ]
    //         ],
    //         'fecha_maxima_pago' => $fechaMaximaPago,
    //         'id_pedido_comercio' => $idPedido,
    //         'descripcion_resumen' => 'Ticket virtual a evento Ejemplo 2017',
    //         'forma_pago' => 9
    //     ];

    //     // Hacer la petición a Pagopar
    //     $http = new Client();
    //     $response = $http->post('https://api.pagopar.com/api/comercios/2.0/iniciar-transaccion', json_encode($data), [
    //         'type' => 'json'
    //     ]);

    //     $body = $response->getJson();

    //     if ($body['respuesta'] === true) {
    //         $hashPedido = $body['resultado'][0]['data'];
    //         $pedidosTable = TableRegistry::getTableLocator()->get('Pedidos');
    //         $pedido = $pedidosTable->newEntity([
    //             'id_comercio' => $idPedido,
    //             'hash_pagopar' => $hashPedido,
    //             'estado_pago' => 'pendiente'
    //         ]);

    //         $pedidosTable->save($pedido);
    //         // Guarda este hash en tu base de datos para futuras referencias
    //         // Ej: $this->Pedidos->save(['id_comercio' => $idPedido, 'hash_pagopar' => $hashPedido]);

    //         // Redireccionar al checkout de Pagopar
    //         return $this->redirect("https://www.pagopar.com/pagos/{$hashPedido}");
    //     } else {
    //         // Manejar el error
    //         $this->Flash->error(__('Error al iniciar transacción: ' . $body['resultado']));
    //         return $this->redirect($this->referer());
    //     }
    // }
    // En tu método notificar():
    public function notificar()
    {
        $this->autoRender = false;

        // Obtener el JSON completo
        $rawInput = file_get_contents('php://input');
        $json_pagopar = json_decode($rawInput, true);

        // Verificar que exista la información necesaria
        if (!isset($json_pagopar['resultado'][0]['hash_pedido']) || !isset($json_pagopar['resultado'][0]['token'])) {
            return $this->response->withType('application/json')
                ->withStatus(400)
                ->withStringBody(json_encode(['error' => 'Datos incompletos']));
        }

        // Clave privada de Pagopar
        $privateKey = 'da0bce1371c6d3304f88d91e32c1fb61';

        // Generar token para validar
        $hashPedido = $json_pagopar['resultado'][0]['hash_pedido'];
        $tokenGenerado = sha1($privateKey . $hashPedido);

        // Validar token
        if ($tokenGenerado !== $json_pagopar['resultado'][0]['token']) {
            return $this->response->withType('application/json')
                ->withStatus(403)
                ->withStringBody(json_encode(['error' => 'Token no coincide']));
        }

        // Si el token es válido, procesar el pago
        if ($json_pagopar['resultado'][0]['pagado'] === true) {
            // Buscar pedido por hash
            $this->loadModel('Pedidos');
            $pedido = $this->Pedidos->find()->where(['hash_pagopar' => $hashPedido])->first();

            if ($pedido) {
                $pedido->estado_pago = 'pagado';
                $this->Pedidos->save($pedido);
            }
        } elseif ($json_pagopar['resultado'][0]['pagado'] === false) {
            // Manejar reversión de pago si es necesario
            $this->loadModel('Pedidos');
            $pedido = $this->Pedidos->find()->where(['hash_pagopar' => $hashPedido])->first();

            if ($pedido) {
                $pedido->estado_pago = 'reversado';
                $this->Pedidos->save($pedido);
            }
        }

        // Devolver la respuesta esperada por Pagopar
        return $this->response->withType('application/json')
            ->withStringBody(json_encode($json_pagopar['resultado']));
    }
    // public function notificar()
    // {
    //     $this->autoRender = false;

    //     $request = $this->request->getData();
    //     $idPedido = $request['id_pedido_comercio'] ?? null;
    //     $estado = $request['estado'] ?? null;

    //     if ($idPedido && $estado) {
    //         $this->loadModel('Pedidos');
    //         $pedido = $this->Pedidos->find()->where(['id_comercio' => $idPedido])->first();

    //         if ($pedido) {
    //             $pedido->estado_pago = $estado;
    //             $this->Pedidos->save($pedido);
    //         }
    //     }

    //     $this->response = $this->response->withType('application/json')
    //         ->withStringBody(json_encode(['respuesta' => true]));
    //     return $this->response;
    // }


    // En src/Controller/PagoparController.php
    // public function resultado()
    // {
    //     // Obtener el hash del pedido de la URL
    //     $hashPedido = $this->request->getQuery('hash_pedido');

    //     if (!$hashPedido) {
    //         $this->Flash->error('No se recibió el hash del pedido');
    //         return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    //     }

    //     // Claves de Pagopar
    //     $publicKey = '99e72c277c5a2e5f2d9a52efc47afe03';
    //     $privateKey = 'da0bce1371c6d3304f88d91e32c1fb61';

    //     // Generar token
    //     $token = sha1($privateKey . "CONSULTA");

    //     // Datos para consultar estado
    //     $data = [
    //         'hash_pedido' => $hashPedido,
    //         'token' => $token,
    //         'token_publico' => $publicKey
    //     ];

    //     // Consultar estado en Pagopar
    //     $http = new Client();
    //     $response = $http->post(
    //         'https://api.pagopar.com/api/pedidos/1.1/traer',
    //         json_encode($data),
    //         ['type' => 'json']
    //     );

    //     $body = $response->getJson();

    //     if ($body['respuesta'] === true && isset($body['resultado'][0])) {
    //         $resultado = $body['resultado'][0];

    //         // Buscar pedido en la base de datos
    //         $this->loadModel('Pedidos');
    //         $pedido = $this->Pedidos->find()->where(['hash_pagopar' => $hashPedido])->first();

    //         if ($pedido) {
    //             // Actualizar estado según la respuesta
    //             $pedido->estado_pago = $resultado['pagado'] ? 'pagado' : 'pendiente';
    //             $this->Pedidos->save($pedido);

    //             // Pasar información a la vista
    //             $this->set('pedido', $pedido);
    //             $this->set('resultado', $resultado);

    //             if (isset($resultado['mensaje_resultado_pago'])) {
    //                 $this->set('mensaje', $resultado['mensaje_resultado_pago']);
    //             }
    //         } else {
    //             $this->Flash->error('No se encontró el pedido en el sistema');
    //         }
    //     } else {
    //         $this->Flash->error('Error al consultar el estado del pedido');
    //     }

    //     $this->render('resultado');
    // }
    public function resultado()
    {
        $this->autoRender = false;

        // Obtener los datos enviados por Pagopar
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!isset($data['resultado'][0]['hash_pedido']) || !isset($data['resultado'][0]['token'])) {
            return $this->response->withType('application/json')
                ->withStatus(400)
                ->withStringBody(json_encode(['error' => 'Datos incompletos']));
        }

        $hashPedido = $data['resultado'][0]['hash_pedido'];
        $token = $data['resultado'][0]['token'];
        $privateKey = 'da0bce1371c6d3304f88d91e32c1fb61';

        // Validar el token
        $tokenGenerado = sha1($privateKey . $hashPedido);
        if ($token !== $tokenGenerado) {
            return $this->response->withType('application/json')
                ->withStatus(403)
                ->withStringBody(json_encode(['error' => 'Token no válido']));
        }

        // Actualizar el estado del pedido
        $this->loadModel('Pedidos');
        $pedido = $this->Pedidos->find()->where(['hash_pagopar' => $hashPedido])->first();

        if ($pedido) {
            $pedido->estado_pago = $data['resultado'][0]['pagado'] ? 'pagado' : 'pendiente';
            $this->Pedidos->save($pedido);
        }

        // Responder con el mismo JSON recibido
        return $this->response->withType('application/json')
            ->withStringBody(json_encode($data['resultado']));
    }


    public function obtenerEstadoPedido($hashPedido)
    {
        $this->autoRender = false;

        // Claves proporcionadas por Pagopar
        $privateKey = 'da0bce1371c6d3304f88d91e32c1fb61';
        $publicKey = '99e72c277c5a2e5f2d9a52efc47afe03';

        // Generar el token de seguridad
        $token = sha1($privateKey . 'CONSULTA');

        // Datos para la solicitud
        $data = [
            'hash_pedido' => $hashPedido,
            'token' => $token,
            'token_publico' => $publicKey
        ];

        // Realizar la solicitud a Pagopar
        $http = new Client();
        $response = $http->post('https://api.pagopar.com/api/pedidos/1.1/traer', json_encode($data), [
            'type' => 'json'
        ]);

        $body = $response->getJson();

        // Verificar si la respuesta es válida
        if (isset($body['respuesta']) && $body['respuesta'] === true && isset($body['resultado'][0])) {
            $resultado = $body['resultado'][0];

            // Actualizar el estado del pedido en la base de datos
            $this->loadModel('Pedidos');
            $pedido = $this->Pedidos->find()->where(['hash_pagopar' => $hashPedido])->first();

            if ($pedido) {
                $pedido->estado_pago = $resultado['pagado'] ? 'pagado' : ($resultado['cancelado'] ? 'reversado' : 'pendiente');
                $this->Pedidos->save($pedido);
            }

            // Pasar los datos a la vista
            $this->set('resultado', $resultado);

            // Renderizar la vista
            $this->render('mostrar_resultado');
        } else {
            // Manejar errores en la respuesta
            $this->Flash->error(__('Error al obtener el estado del pedido.'));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }
    }



    public function holamundo()
    {
        $this->autoRender = false;
        $this->response->body('Hola mundo');
        return $this->response;
    }
    // public function checkout($mp_id)
    // {
    //     $this->autoRender = false;

    //     $paymentTable = TableRegistry::get('MembershipPayment');
    //     $payment = $paymentTable->get($mp_id);

    //     // ⚠️ Llaves ficticias, debes cambiarlas por las reales
    //     $public_key = '99e72c277c5a2e5f2d9a52efc47afe03';
    //     $private_key = 'da0bce1371c6d3304f88d91e32c1fb61';

    //     $amount = $payment->amount;

    //     $client = new Client();
    //     $response = $client->post('https://api.pagopar.com/api/1.1/transaction/create', [
    //         'public_key' => $public_key,
    //         'token' => hash('sha1', $private_key . $payment->id),
    //         'order_id' => $payment->id,
    //         'amount' => $amount,
    //         'description' => 'Pago de membresía',
    //         'return_url' => 'http://localhost',
    //         'cancel_url' => 'http://localhost',
    //     ], ['type' => 'json']);

    //     $result = $response->getJson();

    //     if (isset($result['respuesta']) && $result['respuesta'] === 'OK') {
    //         return $this->redirect($result['resultado']['checkout_url']);
    //     } else {
    //         $this->Flash->error(__('Error al iniciar pago en PagoPar.'));
    //         return $this->redirect($this->referer());
    //     }
    // }
}
