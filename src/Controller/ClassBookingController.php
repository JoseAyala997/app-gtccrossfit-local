<?php
namespace App\Controller;
use App\Controller\AppController;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;

Class ClassBookingController  extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Csrf');
		$this->loadComponent("GYMFunction");
		$this->loadComponent("ClassBookingValidation");
		require_once(ROOT . DS .'vendor' . DS  . 'paypal' . DS . 'paypal_class.php');	
	}
	
	public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);       
        $this->Auth->allow(['index','getClassFees','getClassDay','paymentSuccess']);
		if (in_array($this->request->action, ['getClassFees','getClassDay'])) {
			$this->eventManager()->off($this->Csrf);
		}
		 
    }
	
	public function index()
	{		
		$this->viewBuilder()->layout('login');

		$class_schedule = TableRegistry::get('class_schedule');
		$classes = $class_schedule->find("list",["keyField"=>"id","valueField"=>"class_name"])->hydrate(false)->toArray();

		$this->set('class',$classes);
		$this->set("edit",false);

		if($this->request->is('post')){

			$data = $this->request->getData();
			
			// If the form data is nested under 'addgroup', extract it
			if (isset($data['addgroup'])) {
				$data = $data['addgroup'];
			}
			
			if($data['full_name'] != '' && $data['mobile_no'] != '' && $data['email'] != '' && $data['class_id'] != '' && $data['booking_amount'] != '' 
			 && $data['booking_date'] != '')
			{
				if($data['payment_by'] == 'Stripe'){

					if(!empty($this->request->data['stripeToken'])) {
					
						$token = $this->request->data['stripeToken'];
						$email = $this->request->data['stripeEmail'];

						//debug($data);die;
						require_once(ROOT . DS .'vendor' . DS  . 'stripe-php' . DS . 'init.php');

						$stripe = array(
								"secret_key" => $this->GYMFunction->getSettings('stripe_secret_key'),
								"publishable_key" => $this->GYMFunction->getSettings('stripe_publishable_key') 
							);

						\Stripe\Stripe::setApiKey($stripe['secret_key']);

						 $city = $this->request->data['city'];
						 $address = $this->request->data['address'];
						 $zipcode = $this->request->data['zipcode'];
						 $state = $this->request->data['state'];
						$country = $this->GYMFunction->getSettings('country');

						$customer = \Stripe\Customer::create([
									'name'=>$this->request->data['full_name'],
							        //'description' => 'test payment', 
							        'email' => $email,
								    'source'  => $token,
								  	"address" => [
								  	"city" => $city, 
								  	"country" => $country, 
								  	"line1" => $address, 
								  	"line2" => "", 
								  	"postal_code" => $zipcode, 
								  	"state" => $state
								  ]
							  ]);

						$currency = $this->GYMFunction->getSettings("currency");
						$customer_id = $customer->sources->data[0]->customer;
						$price = ($this->request->data['booking_amount']) * 100;

						$charge = \Stripe\Charge::create([
							      'customer' => $customer->id,
							      'amount'   => $price,
							      'currency' => $currency,
							      'description' => 'Paid class booking'
							  ]);

						$chargeJson = $charge->jsonSerialize();

						if($chargeJson['amount_refunded'] == 0 && 
						  		empty($chargeJson['failure_code']) &&
						  		$chargeJson['paid'] == 1 &&
						  		$chargeJson['captured'] == 1
						  	) 
						{
							$row = $this->ClassBooking->newEntity();

							$data['full_name'] = $this->request->data['full_name'];
							$data['gender'] = $this->request->data['gender'];
							$data['mobile_no'] = $this->request->data['mobile_no'];
							$data['email'] = $this->request->data['email'];
							$data['address'] = $this->request->data['address'];
							$data['city'] = $this->request->data['city'];
							$data['zipcode'] = $this->request->data['zipcode'];
							$data['class_id'] = $this->request->data['class_id'];
							$data['booking_date'] = $this->GYMFunction->get_db_format_date($this->request->data['booking_date']);
							$data['booking_type'] = $this->request->data['booking_type'];
							$data['booking_amount'] = $chargeJson['amount'] / 100;
							$data['transaction_id'] = $chargeJson['balance_transaction'];
							$data['payment_by'] = $chargeJson['calculated_statement_descriptor'];
							$data['status'] = 'Paid';
							$data['created_at'] = date('Y-m-d');

							$row = $this->ClassBooking->patchEntity($row,$data);
							if($this->ClassBooking->save($row))
							{
								$this->Flash->success(__("Success! Record Saved Successfully."));
								return $this->redirect(["action"=>"index"]);
							} 
						}else{
							$this->Flash->error(__("Error! Something are wrong."));
							return $this->redirect(["action"=>"index"]);
						}
					}
				}else if($data['payment_by'] == 'Paypal'){
					
					$user_info = $this->request->data;
					
					$new_session = $this->request->session();
					//$new_session->write("Payment.user",$mp_id);
					$new_session->write("Payment",$this->request->data);

					require_once(ROOT . DS .'vendor' . DS  . 'paypal' . DS . 'class_booking_paypal_process.php');
					//echo 'hello';die;
				}else{

					$email = $this->request->data['email'];
					$class_id = $this->request->data['class_id'];

					$booking = $this->ClassBooking->find()->where(['email'=>$email,'class_id'=>$class_id,'booking_type'=>'Demo'])->hydrate(false)->toArray();
					//debug($booking);die;
					if($booking != NULL){
						$this->Flash->error(__("Error! This class is already enjoy by you, now you can not book this class as demo."));
						return $this->redirect(["action"=>"index"]);
					}else{
						$row = $this->ClassBooking->newEntity();

						$this->request->data['booking_date'] = $this->GYMFunction->get_db_format_date($this->request->data['booking_date']);
						$this->request->data['created_at'] = date('Y-m-d');

						if($this->request->data['booking_type'] == 'Demo')
						{
							$this->request->data['booking_amount'] = '0';
						}
						$row = $this->ClassBooking->patchEntity($row,$this->request->data);
						if($this->ClassBooking->save($row))
						{
							$this->Flash->success(__("Success! Record Saved Successfully."));
							return $this->redirect(["action"=>"index"]);
						} 
					}
				}
			}else{
				$this->Flash->error(__("Error! Please Fill All Information."));
						return $this->redirect(["action"=>"index"]);
			}
		}
	}

	/**
	 * Add a new class booking
	 * 
	 * Allows members to book classes based on date selection and availability
	 * 
	 * @return \Cake\Http\Response|null
	 */
	public function addBooking()
	{
		$session = $this->request->session()->read("User");
		
		if ($session["role_name"] != "member" && $session["role_name"] != "staff_member") {
			$this->Flash->error(__("You are not authorized to book classes"));
			return $this->redirect(['action' => 'bookingList']);
		}
		 // Verificar si el usuario está en modo de mantenimiento
		$gymMemberTable = TableRegistry::get('gym_member');
		$member = $gymMemberTable->find()
			->select(['is_maintenance_mode'])
			->where(['id' => $session['id']])
			->first();

		if ($member && $member->is_maintenance_mode == 1) {
			$this->Flash->error(__("No puedes agendar una clase mientras estás en modo de mantenimiento."));
			return $this->redirect(['action' => 'bookingList']);
		}

		$this->set("title", __("Reservar una clase"));

		// Load necessary tables
		$classScheduleTable = TableRegistry::get('class_schedule');
		$classBookingTable = TableRegistry::get('class_booking');
		$membershipHistoryTable = TableRegistry::get('membership_history');
		$membershipPaymentHistoryTable = TableRegistry::get('membership_payment_history');
		$membershipPaymentTable = TableRegistry::get('membership_payment');
		$userId = $session['id'];

		// Get user's active membership and branch
		$activeMembership = $membershipPaymentTable->find()
		->where(['member_id' => $userId])
		->order(['mp_id' => 'DESC']) // Get the latest record
		->first();

		if (!$activeMembership) {
			$this->Flash->error(__("No tienes una membresía activa"));
			return $this->redirect(['action' => 'bookingList']);
		}
		  // Obtener el registro más reciente de la membresía del usuario
		  $membershipHistory = $membershipHistoryTable->find()
		  ->where(['member_id' =>  $session['id']])
		  ->order(['id' => 'DESC'])
		  ->first();
  
		  if ($membershipHistory) {
			$today = new \DateTime('now');
			Log::write('debug', 'First Pay Date (formateadoSS): ' . $membershipHistory->first_pay_date);
		
			// Convertir first_pay_date al formato correcto
			$firstPayDateRaw = $membershipHistory->first_pay_date;
		
			// Detectar y convertir el formato d/m/y a Y-m-d
			if (strpos($firstPayDateRaw, '/') !== false) {
				$dateParts = explode('/', $firstPayDateRaw);
				if (count($dateParts) === 3) {
					$firstPayDateFormatted = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0]; // Convertir a Y-m-d
				} else {
					Log::write('error', 'Formato de fecha inválido en first_pay_date: ' . $firstPayDateRaw);
					$this->Flash->error(__("Error al procesar la fecha de pago. Por favor, contacta al administrador."));
					return $this->redirect(['action' => 'bookingList']);
				}
			} else {
				$firstPayDateFormatted = $firstPayDateRaw; // Asumir que ya está en formato Y-m-d
			}
		
			try {
				$firstPayDate = new \DateTime($firstPayDateFormatted);
				Log::write('debug', 'First Pay Date (formateado): ' . $firstPayDate->format('Y-m-d'));
			} catch (\Exception $e) {
				Log::write('error', 'Error al crear objeto DateTime para first_pay_date: ' . $e->getMessage());
				$this->Flash->error(__("Error al procesar la fecha de pago. Por favor, contacta al administrador."));
				return $this->redirect(['action' => 'bookingList']);
			}
		
			// Verificar si la fecha actual ha pasado el first_pay_date
			if ($today > $firstPayDate) {
				// Mostrar mensaje de que la membresía no está activa
				$this->Flash->error(__("No tienes una membresía activa. Por favor, realiza el pago correspondiente."));
				return $this->redirect(['action' => 'bookingList']);
			}
		} else {
			// Si no hay historial de membresía, mostrar mensaje
			$this->Flash->error(__("No tienes una membresía activa. Por favor, contacta al administrador."));
			return $this->redirect(['action' => 'bookingList']);
		}

		// Get the mp_id from the latest membership payment
		$mpId = $activeMembership->mp_id;

		// Check the payment confirmation status in membership_payment_history
		$paymentHistory = $membershipPaymentHistoryTable->find()
			->where(['mp_id' => $mpId, 'payment_confirmation_status' => 'Confirmed'])
			->first();

		if (!$paymentHistory) {
			$this->Flash->error(__("No tienes una membresía activa"));
			return $this->redirect(['action' => 'bookingList']);
		}
		

			// Obtener branch_id correcto para filtrar clases
		$memberBranchId = null; // Valor predeterminado en caso de fallo

		// Obtener el branch_id a través del membership_id (usando la tabla membership)
		if ($activeMembership && isset($activeMembership->membership_id)) {
			try {
				// Cargar la tabla membership si aún no está disponible
				if (!isset($membershipTable)) {
					$membershipTable = TableRegistry::get('membership');
				}
				
				// Buscar la membresía asociada para obtener el branch_id
				$membershipInfo = $membershipTable->find()
					->select(['branch_id'])
					->where(['id' => $activeMembership->membership_id])
					->first();
				
				if ($membershipInfo && !empty($membershipInfo->branch_id)) {
					$memberBranchId = $membershipInfo->branch_id;
					// $this->log('Branch ID obtenido de la tabla membership: ' . $memberBranchId, 'debug');
				} else {
					$this->log('No se pudo obtener branch_id de membership. Usando valor predeterminado: ' . $memberBranchId, 'debug');
				}
			} catch (\Exception $e) {
				$this->log('Error al obtener branch_id: ' . $e->getMessage(), 'error');
			}
		} else {
			$this->log('No hay membership_id disponible. Usando branch_id predeterminado: ' . $memberBranchId, 'debug');
		}

		// Get membership limit status and branch credits
		// se usa para mostrar el límite de membresía
		$membershipLimitStatus = $this->ClassBookingValidation->getMembershipLimitStatus($session['id'], date('Y-m-d'));
		$branchCredits = $this->ClassBookingValidation->getBranchCredits($session['id']);

		// Get selected date from query parameter or use today's date
		$selectedDate = $this->request->getQuery('date') ? $this->request->getQuery('date') : date('Y-m-d');
		
		// Ensure selectedDate is in YYYY-MM-DD format
		if (strpos($selectedDate, '/') !== false) {
			$dateParts = explode('/', $selectedDate);
			if (count($dateParts) === 3) {
				$selectedDate = $dateParts[2] . '-' . $dateParts[0] . '-' . $dateParts[1];
			}
		}
		
		// Get available classes for the member's branch that are scheduled for the selected day of week
		// $classes = $classScheduleTable->getClassesByDate($selectedDate, $memberBranchId)->toArray(); // Using fixed branch_id for now
		$classes = $classScheduleTable->getClassesByDate($selectedDate)->toArray();
		// Calculate availability for each class on the selected date
		foreach ($classes as $class) {
			// Get current bookings for this class on the selected date
			$bookedCount = $classBookingTable->find()
				->where([
					'class_id' => $class->id,
					'booking_date' => $selectedDate,
					'status !=' => 'Cancelled'
				])
				->count();
			
			// Calculate available spots
			$totalSpots = (int)$class->max_quota;
			$availableSpots = max(0, $totalSpots - $bookedCount);
			
			// Add availability info to class object
			$class->available_spots = $availableSpots;
			$class->booked_spots = $bookedCount;
		}

		if ($this->request->is('post')) {
			$data = $this->request->getData();
			
			// If the form data is nested under 'addgroup', extract it
			if (isset($data['addgroup'])) {
				$data = $data['addgroup'];
			}
			
			// Validate required fields
			if (empty($data['class_id']) || empty($data['booking_date'])) {
				$this->Flash->error(__("Please select a class and date"));
				$redirectParams = ['action' => 'addBooking'];
				$redirectParams['?'] = ['date' => $selectedDate];
				return $this->redirect($redirectParams);
			}
			
			// Format booking date - convert from MM/DD/YYYY to YYYY-MM-DD
			if (strpos($data['booking_date'], '/') !== false) {
				$dateParts = explode('/', $data['booking_date']);
				if (count($dateParts) === 3) {
					$bookingDate = $dateParts[2] . '-' . $dateParts[0] . '-' . $dateParts[1];
				} else {
					$bookingDate = date('Y-m-d'); // Fallback to today if format is unexpected
				}
			} else {
				// Use GYMFunction for other formats
				$bookingDate = $this->GYMFunction->get_db_format_date($data['booking_date']);
			}
			
			// Check if class has available spots
			// Get current bookings for this class on the booking date
			$bookedCount = $classBookingTable->find()
				->where([
					'class_id' => $data['class_id'],
					'booking_date' => $bookingDate,
					'status !=' => 'Cancelled'
				])
				->count();
			
			$selectedClass = $classScheduleTable->get($data['class_id'], [
				'fields' => [
					'id',
					'class_name',
					'assign_staff_mem',
					'location',
					'class_fees',
					'days',
					'start_time',
					'end_time',
					'max_quota',
					'created_by',
					'created_date',
					'branch_id',
				]
			]);
			$totalSpots = (int)$selectedClass->max_quota;
			
			if ($bookedCount >= $totalSpots) {
				$this->Flash->error(__("Lo sentimos, la clase está llena"));
				$redirectParams = ['action' => 'addBooking'];
				$redirectParams['?'] = ['date' => $data['booking_date']];
				return $this->redirect($redirectParams);
			}
			
			// Check if user already booked this class on this date
			$existingBooking = $classBookingTable->find()
				->where([
					'member_id' => $session['id'],
					'class_id' => $data['class_id'],
					'booking_date' => $bookingDate,
					'status !=' => 'Cancelled'
				])
				->first();
				Log::write('debug', 'Branch ID obtenido: ' . $selectedClass);
			if ($existingBooking) {
				$this->Flash->error(__("Ya has reservado esta clase"));
				$redirectParams = ['action' => 'addBooking'];
				$redirectParams['?'] = ['date' => $data['booking_date']];
				return $this->redirect($redirectParams);
			}
			$hasCredits = $this->ClassBookingValidation->deductCredit(
				$session['id'],
				$data['class_id'],
				$selectedClass->branch_id
			);
		
			if (!$hasCredits) {
				$this->Flash->error(__("No tienes suficientes créditos para reservar esta clase."));
				$redirectParams = ['action' => 'addBooking'];
				$redirectParams['?'] = ['date' => $data['booking_date']];
				return $this->redirect($redirectParams);
			}

			// Validate booking according to membership rules
			$validationResult = $this->ClassBookingValidation->validateBooking(
				$session['id'],
				$data['class_id'],
				$bookingDate
			);
			
			if (!$validationResult['status']) {
				$this->Flash->error($validationResult['message']);
				$redirectParams = ['action' => 'addBooking'];
				$redirectParams['?'] = ['date' => $data['booking_date']];
				return $this->redirect($redirectParams);
			}
			try {
				$gymMemberTable = TableRegistry::get('gym_member');
				$memberDetails = $gymMemberTable->get($session['id'])->toArray();
				
				// Logging para debug
				 //$this->log('Member details from DB: ' . json_encode($memberDetails), 'debug');
				
				// Crear booking record con datos seguros
				$booking = $classBookingTable->newEntity();
				$bookingData = [
					'member_id' => $session['id'],
					'class_id' => $data['class_id'],
					'booking_date' => $bookingDate,
					'status' => 'Confirmed',
					'created_at' => date('Y-m-d')
				];
				
				// Añadir datos del miembro si están disponibles
				if (isset($memberDetails['first_name']) && isset($memberDetails['last_name'])) {
					$bookingData['full_name'] = $memberDetails['first_name'] . ' ' . $memberDetails['last_name'];
				} else {
					$bookingData['full_name'] = 'Member #' . $session['id']; // Valor por defecto
				}
				
				// Añadir el resto de campos con operador de fusión de null
				$bookingData['gender'] = $memberDetails['gender'] ?? '';
				$bookingData['mobile_no'] = $memberDetails['mobile'] ?? '';
				$bookingData['email'] = $memberDetails['email'] ?? '';
				$bookingData['address'] = $memberDetails['address'] ?? '';
				$bookingData['city'] = $memberDetails['city'] ?? '';
				$bookingData['state'] = $memberDetails['state'] ?? '';
				$bookingData['zipcode'] = $memberDetails['zipcode'] ?? '';
				
			} catch (\Exception $e) {
				// Si hay un error al obtener datos del miembro, usar datos básicos
			 //	$this->log('Error fetching member details: ' . $e->getMessage(), 'error');
				
				$booking = $classBookingTable->newEntity();
				$bookingData = [
					'member_id' => $session['id'],
					'class_id' => $data['class_id'],
					'booking_date' => $bookingDate,
					'status' => 'Confirmed',
					'created_at' => date('Y-m-d'),
					'full_name' => 'Member #' . $session['id'],
					'gender' => '',
					'mobile_no' => '',
					'email' => '',
					'address' => '',
					'city' => '',
					'state' => '',
					'zipcode' => ''
				];
			}
			
		
			
			$booking = $classBookingTable->patchEntity($booking, $bookingData);
			
			
			// Explicitly set member_id
			$booking->member_id = $session['id'];
			 //$this->log('After explicit set, member_id = ' . $booking->member_id, 'debug');
			
			// Save the entity
			$saveResult = $classBookingTable->save($booking);
			
			if ($saveResult) {
				// Debug save result
				 //$this->log('Save successful. Entity after save: ' . json_encode($saveResult), 'debug');
				
				// If booking is for another branch, deduct a credit
				// $this->ClassBookingValidation->deductCredit(
				// 	$session['id'],
				// 	$data['class_id'],
				// 	$selectedClass->branch_id
				// );
				
				$this->Flash->success(__("Class booked successfully"));
				return $this->redirect(['action' => 'bookingList']);
			} else {
				// Debug save error
			 //	$this->log('Save failed. Validation errors: ' . json_encode($booking->getErrors()), 'debug');
				$this->Flash->error(__("Unable to book class. Please try again."));
			}
		}

		$this->set(compact('classes', 'selectedDate', 'membershipLimitStatus', 'branchCredits'));
	}


	// public function addBooking()
	// {
	// 	$session = $this->request->session()->read("User");
	// 	// Debug session structure
	//  //	$this->log('Session structure: ' . json_encode($session), 'debug');
		
	// 	// Check if session contains user ID
	// 	// if (isset($session['id'])) {
	// 	// 	$this->log('Session contains user ID: ' . $session['id'], 'debug');
	// 	// } else {
	// 	// 	$this->log('Session does not contain user ID!', 'debug');
	// 	// 	// Check what keys are available in the session
	// 	// 	 //$this->log('Available session keys: ' . implode(', ', array_keys($session)), 'debug');
	// 	// }
		
	// 	if ($session["role_name"] != "member" && $session["role_name"] != "staff_member") {
	// 		$this->Flash->error(__("You are not authorized to book classes"));
	// 		return $this->redirect(['action' => 'bookingList']);
	// 	}
	// 	 // Verificar si el usuario está en modo de mantenimiento
	// 	$gymMemberTable = TableRegistry::get('gym_member');
	// 	$member = $gymMemberTable->find()
	// 		->select(['is_maintenance_mode'])
	// 		->where(['id' => $session['id']])
	// 		->first();

	// 	if ($member && $member->is_maintenance_mode == 1) {
	// 		$this->Flash->error(__("No puedes agendar una clase mientras estás en modo de mantenimiento."));
	// 		return $this->redirect(['action' => 'bookingList']);
	// 	}

	// 	$this->set("title", __("Reservar una clase"));

	// 	// Load necessary tables
	// 	$classScheduleTable = TableRegistry::get('class_schedule');
	// 	$classBookingTable = TableRegistry::get('class_booking');
	// 	$membershipHistoryTable = TableRegistry::get('membership_history');
	// 	$membershipPaymentHistoryTable = TableRegistry::get('membership_payment_history');
	// 	$membershipPaymentTable = TableRegistry::get('membership_payment');
	// 	$userId = $session['id'];

	// 	// Get user's active membership and branch
	// 	$activeMembership = $membershipPaymentTable->find()
	// 	->where(['member_id' => $userId])
	// 	->order(['mp_id' => 'DESC']) // Get the latest record
	// 	->first();

	// 	if (!$activeMembership) {
	// 		$this->Flash->error(__("No tienes una membresía activa"));
	// 		return $this->redirect(['action' => 'bookingList']);
	// 	}

	// 	// Get the mp_id from the latest membership payment
	// 	$mpId = $activeMembership->mp_id;

	// 	// Check the payment confirmation status in membership_payment_history
	// 	$paymentHistory = $membershipPaymentHistoryTable->find()
	// 		->where(['mp_id' => $mpId, 'payment_confirmation_status' => 'Confirmed'])
	// 		->first();

	// 	if (!$paymentHistory) {
	// 		$this->Flash->error(__("No tienes una membresía activa"));
	// 		return $this->redirect(['action' => 'bookingList']);
	// 	}
	// 	// if (!$activeMembership) {
	// 	// 	$this->Flash->error(__("No tienes una membresía activa"));
	// 	// 	return $this->redirect(['action' => 'bookingList']);
	// 	// }
	// 			// $activeMembership = $membershipPaymentHistoryTable->find()
	// 	// 	->where([
	// 	// 		'member_id' => $session['id'],
	// 	// 		'payment_status' => 1,
	// 	// 		'end_date >=' => date('Y-m-d'),
	// 	// 		'start_date <=' => date('Y-m-d'),
	// 	// 	])
	// 	// 	->order(['member_id' => 'DESC'])
	// 	// 	->first();

	// 	// $activeMembership = $membershipHistoryTable->find()
	// 	// ->where([
	// 	// 	'member_id' => $session['id'],
	// 	// 	'membership_valid_to >=' => date('Y-m-d'),
	// 	// 	'membership_valid_from <=' => date('Y-m-d'),

	// 	// ])
	// 	// ->order(['id' => 'DESC'])
	// 	// ->first();
	// 	// 	// Get user's active membership and branch
	// 	// 	$activeMembership = $membershipPaymentTable->find()
	// 	// 	->where([
	// 	// 		'member_id' => $session['id'],
	// 	// 		'payment_status' => 1,
	// 	// 		'end_date >=' => date('Y-m-d'),
	// 	// 		'start_date <=' => date('Y-m-d'),
	// 	// 	])
	// 	// 	->order(['member_id' => 'DESC'])
	// 	// 	->first();

	// 	// if (!$activeMembership) {
	// 	// 	$this->Flash->error(__("No tienes una membresía activa"));
	// 	// 	return $this->redirect(['action' => 'bookingList']);
	// 	// }

	// 		// Obtener branch_id correcto para filtrar clases
	// 	$memberBranchId = null; // Valor predeterminado en caso de fallo

	// 	// Obtener el branch_id a través del membership_id (usando la tabla membership)
	// 	if ($activeMembership && isset($activeMembership->membership_id)) {
	// 		try {
	// 			// Cargar la tabla membership si aún no está disponible
	// 			if (!isset($membershipTable)) {
	// 				$membershipTable = TableRegistry::get('membership');
	// 			}
				
	// 			// Buscar la membresía asociada para obtener el branch_id
	// 			$membershipInfo = $membershipTable->find()
	// 				->select(['branch_id'])
	// 				->where(['id' => $activeMembership->membership_id])
	// 				->first();
				
	// 			if ($membershipInfo && !empty($membershipInfo->branch_id)) {
	// 				$memberBranchId = $membershipInfo->branch_id;
	// 				// $this->log('Branch ID obtenido de la tabla membership: ' . $memberBranchId, 'debug');
	// 			} else {
	// 				$this->log('No se pudo obtener branch_id de membership. Usando valor predeterminado: ' . $memberBranchId, 'debug');
	// 			}
	// 		} catch (\Exception $e) {
	// 			$this->log('Error al obtener branch_id: ' . $e->getMessage(), 'error');
	// 		}
	// 	} else {
	// 		$this->log('No hay membership_id disponible. Usando branch_id predeterminado: ' . $memberBranchId, 'debug');
	// 	}
	// 	// $activeMembership = $membershipHistoryTable->find()
	// 	// 	->where([
	// 	// 		'member_id' => $session['id'],
	// 	// 		'membership_valid_to >=' => date('Y-m-d'),
	// 	// 		'membership_valid_from <=' => date('Y-m-d'),
	// 	// 	])
	// 	// 	->order(['id' => 'DESC'])
	// 	// 	->first();

	// 	// if (!$activeMembership) {
	// 	// 	$this->Flash->error(__("No tienes una membresía activa"));
	// 	// 	return $this->redirect(['action' => 'bookingList']);
	// 	// }
		

	// 	// Get user's active membership and branch
	// 	// $connection = \Cake\Datasource\ConnectionManager::get('default');
	// 	// $result = $connection->execute(
	// 	// 	'SELECT mph.*
	// 	// 	 FROM membership_payment_history mph
	// 	// 	 INNER JOIN membership_payment mp ON mph.mp_id = mp.mp_id
	// 	// 	 WHERE mp.member_id = :member_id
	// 	// 	   AND mph.payment_confirmation_status = :status
	// 	// 	 LIMIT 1',
	// 	// 	[
	// 	// 		'member_id' => $userId,
	// 	// 		'status' => 'Confirmed',
	// 	// 	]
	// 	// )->fetch('assoc');
		
	// 	// // Registrar en los logs para depuración
	// 	// Log::write('debug', 'Confirmed payment result: ' . json_encode($result));
		
	// 	// // Verificar si existe un registro confirmado
	// 	// if (!$result) {
	// 	// 	$this->Flash->error(__("No tienes una membresía Confirmada"));
	// 	// 	return $this->redirect(['action' => 'bookingList']);
	// 	// }

	// 	// Get membership limit status and branch credits
	// 	$membershipLimitStatus = $this->ClassBookingValidation->getMembershipLimitStatus($session['id'], date('Y-m-d'));
	// 	$branchCredits = $this->ClassBookingValidation->getBranchCredits($session['id']);

	// 	// Get selected date from query parameter or use today's date
	// 	$selectedDate = $this->request->getQuery('date') ? $this->request->getQuery('date') : date('Y-m-d');
		
	// 	// Ensure selectedDate is in YYYY-MM-DD format
	// 	if (strpos($selectedDate, '/') !== false) {
	// 		$dateParts = explode('/', $selectedDate);
	// 		if (count($dateParts) === 3) {
	// 			$selectedDate = $dateParts[2] . '-' . $dateParts[0] . '-' . $dateParts[1];
	// 		}
	// 	}
		
	// 	// Get available classes for the member's branch that are scheduled for the selected day of week
	// 	// $classes = $classScheduleTable->getClassesByDate($selectedDate, $memberBranchId)->toArray(); // Using fixed branch_id for now
	// 	$classes = $classScheduleTable->getClassesByDate($selectedDate)->toArray();
	// 	// Calculate availability for each class on the selected date
	// 	foreach ($classes as $class) {
	// 		// Get current bookings for this class on the selected date
	// 		$bookedCount = $classBookingTable->find()
	// 			->where([
	// 				'class_id' => $class->id,
	// 				'booking_date' => $selectedDate,
	// 				'status !=' => 'Cancelled'
	// 			])
	// 			->count();
			
	// 		// Calculate available spots
	// 		$totalSpots = (int)$class->max_quota;
	// 		$availableSpots = max(0, $totalSpots - $bookedCount);
			
	// 		// Add availability info to class object
	// 		$class->available_spots = $availableSpots;
	// 		$class->booked_spots = $bookedCount;
	// 	}

	// 	if ($this->request->is('post')) {
	// 		$data = $this->request->getData();
			
	// 		// If the form data is nested under 'addgroup', extract it
	// 		if (isset($data['addgroup'])) {
	// 			$data = $data['addgroup'];
	// 		}
			
	// 		// Validate required fields
	// 		if (empty($data['class_id']) || empty($data['booking_date'])) {
	// 			$this->Flash->error(__("Please select a class and date"));
	// 			$redirectParams = ['action' => 'addBooking'];
	// 			$redirectParams['?'] = ['date' => $selectedDate];
	// 			return $this->redirect($redirectParams);
	// 		}
			
	// 		// Format booking date - convert from MM/DD/YYYY to YYYY-MM-DD
	// 		if (strpos($data['booking_date'], '/') !== false) {
	// 			$dateParts = explode('/', $data['booking_date']);
	// 			if (count($dateParts) === 3) {
	// 				$bookingDate = $dateParts[2] . '-' . $dateParts[0] . '-' . $dateParts[1];
	// 			} else {
	// 				$bookingDate = date('Y-m-d'); // Fallback to today if format is unexpected
	// 			}
	// 		} else {
	// 			// Use GYMFunction for other formats
	// 			$bookingDate = $this->GYMFunction->get_db_format_date($data['booking_date']);
	// 		}
			
	// 		// Check if class has available spots
	// 		// Get current bookings for this class on the booking date
	// 		$bookedCount = $classBookingTable->find()
	// 			->where([
	// 				'class_id' => $data['class_id'],
	// 				'booking_date' => $bookingDate,
	// 				'status !=' => 'Cancelled'
	// 			])
	// 			->count();
			
	// 		$selectedClass = $classScheduleTable->get($data['class_id'], [
	// 			'fields' => [
	// 				'id',
	// 				'class_name',
	// 				'assign_staff_mem',
	// 				'location',
	// 				'class_fees',
	// 				'days',
	// 				'start_time',
	// 				'end_time',
	// 				'max_quota',
	// 				'created_by',
	// 				'created_date',
	// 			]
	// 		]);
	// 		$totalSpots = (int)$selectedClass->max_quota;
			
	// 		if ($bookedCount >= $totalSpots) {
	// 			$this->Flash->error(__("Lo sentimos, la clase está llena"));
	// 			$redirectParams = ['action' => 'addBooking'];
	// 			$redirectParams['?'] = ['date' => $data['booking_date']];
	// 			return $this->redirect($redirectParams);
	// 		}
			
	// 		// Check if user already booked this class on this date
	// 		$existingBooking = $classBookingTable->find()
	// 			->where([
	// 				'member_id' => $session['id'],
	// 				'class_id' => $data['class_id'],
	// 				'booking_date' => $bookingDate,
	// 				'status !=' => 'Cancelled'
	// 			])
	// 			->first();
				
	// 		if ($existingBooking) {
	// 			$this->Flash->error(__("Ya has reservado esta clase"));
	// 			$redirectParams = ['action' => 'addBooking'];
	// 			$redirectParams['?'] = ['date' => $data['booking_date']];
	// 			return $this->redirect($redirectParams);
	// 		}

	// 		// Validate booking according to membership rules
	// 		$validationResult = $this->ClassBookingValidation->validateBooking(
	// 			$session['id'],
	// 			$data['class_id'],
	// 			$bookingDate
	// 		);
			
	// 		if (!$validationResult['status']) {
	// 			$this->Flash->error($validationResult['message']);
	// 			$redirectParams = ['action' => 'addBooking'];
	// 			$redirectParams['?'] = ['date' => $data['booking_date']];
	// 			return $this->redirect($redirectParams);
	// 		}
	// 		try {
	// 			$gymMemberTable = TableRegistry::get('gym_member');
	// 			$memberDetails = $gymMemberTable->get($session['id'])->toArray();
				
	// 			// Logging para debug
	// 			 //$this->log('Member details from DB: ' . json_encode($memberDetails), 'debug');
				
	// 			// Crear booking record con datos seguros
	// 			$booking = $classBookingTable->newEntity();
	// 			$bookingData = [
	// 				'member_id' => $session['id'],
	// 				'class_id' => $data['class_id'],
	// 				'booking_date' => $bookingDate,
	// 				'status' => 'Confirmed',
	// 				'created_at' => date('Y-m-d')
	// 			];
				
	// 			// Añadir datos del miembro si están disponibles
	// 			if (isset($memberDetails['first_name']) && isset($memberDetails['last_name'])) {
	// 				$bookingData['full_name'] = $memberDetails['first_name'] . ' ' . $memberDetails['last_name'];
	// 			} else {
	// 				$bookingData['full_name'] = 'Member #' . $session['id']; // Valor por defecto
	// 			}
				
	// 			// Añadir el resto de campos con operador de fusión de null
	// 			$bookingData['gender'] = $memberDetails['gender'] ?? '';
	// 			$bookingData['mobile_no'] = $memberDetails['mobile'] ?? '';
	// 			$bookingData['email'] = $memberDetails['email'] ?? '';
	// 			$bookingData['address'] = $memberDetails['address'] ?? '';
	// 			$bookingData['city'] = $memberDetails['city'] ?? '';
	// 			$bookingData['state'] = $memberDetails['state'] ?? '';
	// 			$bookingData['zipcode'] = $memberDetails['zipcode'] ?? '';
				
	// 		} catch (\Exception $e) {
	// 			// Si hay un error al obtener datos del miembro, usar datos básicos
	// 		 //	$this->log('Error fetching member details: ' . $e->getMessage(), 'error');
				
	// 			$booking = $classBookingTable->newEntity();
	// 			$bookingData = [
	// 				'member_id' => $session['id'],
	// 				'class_id' => $data['class_id'],
	// 				'booking_date' => $bookingDate,
	// 				'status' => 'Confirmed',
	// 				'created_at' => date('Y-m-d'),
	// 				'full_name' => 'Member #' . $session['id'],
	// 				'gender' => '',
	// 				'mobile_no' => '',
	// 				'email' => '',
	// 				'address' => '',
	// 				'city' => '',
	// 				'state' => '',
	// 				'zipcode' => ''
	// 			];
	// 		}
			
	// 		// Debug booking data
	// 	 //	$this->log('Booking data before save: ' . json_encode($bookingData), 'debug');
	// 		// // Create booking record
	// 		// $booking = $classBookingTable->newEntity();
	// 		// $bookingData = [
	// 		// 	'member_id' => $session['id'],
	// 		// 	'class_id' => $data['class_id'],
	// 		// 	'booking_date' => $bookingDate,
	// 		// 	'status' => 'Confirmed',
	// 		// 	'created_at' => date('Y-m-d'),
	// 		// 	// Copy member data for record keeping
	// 		// 	'full_name' => $session['first_name'] . ' ' . $session['last_name'],
	// 		// 	'gender' => $session['gender'],
	// 		// 	'mobile_no' => $session['mobile'],
	// 		// 	'email' => $session['email'],
	// 		// 	'address' => $session['address'],
	// 		// 	'city' => $session['city'],
	// 		// 	'state' => $session['state'],
	// 		// 	'zipcode' => $session['zipcode']
	// 		// ];

	// 		// Debug booking data
	// 	 //	$this->log('Booking data before save: ' . json_encode($bookingData), 'debug');
			
	// 		$booking = $classBookingTable->patchEntity($booking, $bookingData);
			
	// 		// Debug entity after patch
	// 		 //$this->log('Booking entity after patch: ' . json_encode($booking), 'debug');
			
	// 		// Check if member_id is accessible
	// 		 //$this->log('Can access member_id: ' . (isset($booking->member_id) ? 'Yes: ' . $booking->member_id : 'No'), 'debug');
			
	// 		// Explicitly set member_id
	// 		$booking->member_id = $session['id'];
	// 		 //$this->log('After explicit set, member_id = ' . $booking->member_id, 'debug');
			
	// 		// Save the entity
	// 		$saveResult = $classBookingTable->save($booking);
			
	// 		if ($saveResult) {
	// 			// Debug save result
	// 			 //$this->log('Save successful. Entity after save: ' . json_encode($saveResult), 'debug');
				
	// 			// If booking is for another branch, deduct a credit
	// 			$this->ClassBookingValidation->deductCredit(
	// 				$session['id'],
	// 				$data['class_id'],
	// 				$selectedClass->branch_id
	// 			);
				
	// 			$this->Flash->success(__("Class booked successfully"));
	// 			return $this->redirect(['action' => 'bookingList']);
	// 		} else {
	// 			// Debug save error
	// 		 //	$this->log('Save failed. Validation errors: ' . json_encode($booking->getErrors()), 'debug');
	// 			$this->Flash->error(__("Unable to book class. Please try again."));
	// 		}
	// 	}

	// 	$this->set(compact('classes', 'selectedDate', 'membershipLimitStatus', 'branchCredits'));
	// }
	public function regComplete()
	{
		$this->autoRender = false;
		echo "<br><p><i><strong>Success!</strong> Registration completed successfully.</i></p>";
		echo "<p><i><a href='{$this->request->base}/Users'>Click Here</a> to Redirect on login page.</i></p>";
	}

	public function getClassFees(){
		$this->autoRender=false;

		if($this->request->is('ajax')){

			$class_schedule = TableRegistry::get('class_schedule');
			$class_id = $this->request->data['class_id'];

			$data = $class_schedule->find('all')->select(['class_fees'])->where(['id'=>$class_id])->hydrate(false)->toArray();

			echo $data[0]['class_fees'];
		}
		
	}

	public function getClassDay(){
		$this->autoRender=false;
		$class_schedule = TableRegistry::get('class_schedule_list');
		
		if($this->request->is('ajax')){
			$class_id = $this->request->data['class_id'];

			$data = $class_schedule->find('all')->select(['days'])->where(['class_id'=>$class_id])->hydrate(false)->toArray();

			/*foreach($data as $key => $value){
				$blocks[] = $value['days'];
				
			}*/
			$book_day = [];

			if(!empty($data)){
				$blocks1 = json_decode($data[0]['days']);

				foreach ($blocks1 as $key => $value) {
					
					$currnt_date = Date('Y-m-d');

					for($i=0;$i<=30;$i++){
						$date = strtotime("+".$i." days", strtotime($currnt_date));

						$day = date('l', $date);

						if($day == $value){
							
							$book_day[] = date('Y-m-d',$date);
						}
					}
					
				}

			}
			echo json_encode($book_day);
		}
	}
	public function bookingList(){
		$booking_tbl = TableRegistry::get('class_booking');
		$session = $this->request->session()->read("User");
		
		// Base query with joins
		$query = $booking_tbl->find()
					->select(['class_booking.booking_id','class_booking.booking_date','class_schedule.class_name','class_schedule.location','class_booking.full_name','class_booking.booking_type','class_booking.booking_amount','class_booking.mobile_no'])
					->join([
						'table' => 'class_schedule',
						'type' => 'inner',
						'conditions' => 'class_booking.class_id = class_schedule.id'
					]);
		
		// Filter by member ID if user is a member
		if($session["role_name"] == "member") {
			$query->where(['class_booking.member_id' => $session["id"]]);
		}
		
		$booking_data = $query->hydrate(false)->toArray();
		
		$this->set('data', $booking_data);
	}

	public function paymentSuccess()
	{
		$payment_data = $this->request->session()->read("Payment");
		//$session = $this->request->session()->read("User");
		//$feedata['mp_id']=$payment_data["mp_id"];

		$row = $this->ClassBooking->newEntity();

		$payment_data['booking_date'] = $this->GYMFunction->get_db_format_date($payment_data['booking_date']);
		$payment_data['status'] = 'Paid';
		$payment_data['created_at'] = date('Y-m-d');

		$row = $this->ClassBooking->patchEntity($row,$payment_data);

		if($this->ClassBooking->save($row))
		{
			$session = $this->request->session();
			$session->delete('Payment');
			$this->Flash->success(__("Success! Record Saved Successfully."));
			return $this->redirect(["action"=>"index"]);
		}
		
	}

	public function ipnFunction()
	{
		if($this->request->is("post"))
		{
			
			// $trasaction_id  = $_POST["txn_id"];
			// $custom_array = explode("_",$_POST['custom']);
			// $feedata['mp_id']=$custom_array[1];
			// $feedata['amount']=$_POST['mc_gross_1'];
			// $feedata['payment_method']='Paypal';	
			// $feedata['trasaction_id']=$trasaction_id ;
			// $feedata['created_by']=$custom_array[0];
			// //$log_array		= print_r($feedata, TRUE);
			// //wp_mail( 'bhaskar@dasinfomedia.com', 'gympaypal', $log_array);
			// $row = $this->MembershipPayment->MembershipPaymentHistory->newEntity();
			// $row = $this->MembershipPayment->MembershipPaymentHistory->patchEntity($row,$feedata);
			// if($this->MembershipPayment->MembershipPaymentHistory->save($row))
			// {
			// 	$this->Flash->success(__("Success! Payment Successfully Completed."));
			// }
			// else{
			// 	$this->Flash->error(__("Paypal Payment IPN save failed to DB."));
			// }
			// return $this->redirect(["action"=>"paymentList"]);
			// //require_once SMS_PLUGIN_DIR. '/lib/paypal/paypal_ipn.php';
		}
	}


// FUNCIONES PARA EL DROPIN
/**
 * Display drop-in booking form and process submissions
 * 
 * @return \Cake\Http\Response|null
 */
public function dropIn()
{
    // Solo permitir a staff_member y administrators usar esta función
    $session = $this->request->session()->read("User");
    if (!isset($session['role_name']) || ($session['role_name'] != "staff_member" && $session['role_name'] != "administrator")) {
        $this->Flash->error(__("No tienes permisos para acceder a esta página"));
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }
    
    $this->set("title", __("Registro Drop-In"));
    
    // Cargar tablas necesarias
    $classScheduleTable = TableRegistry::get('class_schedule');
    $gymMemberTable = TableRegistry::get('gym_member');
    $classBookingTable = TableRegistry::get('class_booking');
    $gymMemberClassTable = TableRegistry::get('gym_member_class');
    
    // Obtener clases disponibles para el staff actual
    if ($session['role_name'] == "staff_member") {
        // Filtrar clases donde este staff es el instructor
        $classes = $classScheduleTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'class_name'
        ])->where(['assign_staff_mem' => $session['id']])->toArray();
    } else {
        // Para administradores, mostrar todas las clases
        $classes = $classScheduleTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'class_name'
        ])->toArray();
    }
    
    // Fecha actual para el datepicker
    $selectedDate = date('Y-m-d');
    
    // Pasar siempre las variables a la vista
    $this->set(compact('classes', 'selectedDate'));
    
    // Procesar el formulario si es POST
    if ($this->request->is('post')) {
        $data = $this->request->getData();
        
        // Verificar el formato de debug para la depuración
       //  $this->log('Datos recibidos del formulario: ' . json_encode($data), 'debug');
        
        // Validar datos mínimos requeridos
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['email']) || empty($data['mobile']) || 
            empty($data['username']) || empty($data['password']) || 
            empty($data['class_id']) || empty($data['booking_date'])) {
            
            $this->Flash->error(__("Por favor complete todos los campos obligatorios"));
            return null;
        }
        
        // Comenzar una transacción para asegurar que todo se guarde o nada
        $connection = \Cake\Datasource\ConnectionManager::get('default');
        $connection->begin();
        
        try {
            // 1. Verificar si el email o username ya existen
            $existingMember = $gymMemberTable->find()
                ->where(['OR' => [
                    'email' => $data['email'],
                    'username' => $data['username']
                ]])
                ->first();
                
            if ($existingMember) {
                $this->Flash->error(__("El email o nombre de usuario ya está registrado"));
                return null;
            }
            
            // 2. Crear el nuevo miembro
            $newMember = $gymMemberTable->newEntity();
            
            // Generar ID de miembro
            $m = date("d");
            $y = date("y");
            $member_count = $gymMemberTable->find()->count() + 1;
            $prefix = "M".$member_count;
            $member_id = $prefix.$m.$y;
            
            // Preparar datos del miembro
            $memberData = [
                'member_id' => $member_id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'gender' => $data['gender'] ?? '',
                'birth_date' => !empty($data['birth_date']) ? $this->GYMFunction->get_db_format_date($data['birth_date']) : null,
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? '',
                'state' => $data['state'] ?? '',
                'zipcode' => $data['zipcode'] ?? '',
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => $data['password'], // CakePHP aplicará el hash automáticamente
                'image' => 'Thumbnail-img.png',
                'created_date' => date('Y-m-d'),
                'created_by' => $session['id'],
                'role_name' => 'member',
                'activated' => 1,
                'member_type' => 'Member',
                'membership_status' => 'Continue'  // Cambiado de 'Drop-In' a 'Continue'
            ];
            
            // Comprobar si existe la columna is_drop_in y asignar valor directamente
            $columns = $connection->execute("SHOW COLUMNS FROM gym_member LIKE 'is_drop_in'")->fetchAll();
            if (!empty($columns)) {
                $this->log('La columna is_drop_in existe en gym_member', 'debug');
                $memberData['is_drop_in'] = 1;
            } else {
                $this->log('La columna is_drop_in NO existe en gym_member', 'debug');
            }
            
            $this->log('Datos de miembro preparados: ' . json_encode($memberData), 'debug');
            
            // Crear entidad con acceso explícito a todos los campos
            $newMember = $gymMemberTable->newEntity($memberData, [
                'accessibleFields' => ['is_drop_in' => true]
            ]);
            
            // Guardar el nuevo miembro
            if (!$gymMemberTable->save($newMember)) {
                throw new \Exception("Error al guardar el nuevo miembro: " . json_encode($newMember->errors()));
            }
            
            $this->log('Miembro guardado con ID: ' . $newMember->id, 'debug');
            
            // Verificar si se guardó correctamente el campo is_drop_in
            if (!empty($columns)) {
                try {
                    $savedMember = $gymMemberTable->get($newMember->id);
                    $this->log('Valor de is_drop_in después de guardar: ' . $savedMember->is_drop_in, 'debug');
                    
                    // Si no se guardó correctamente, forzar la actualización
                    if (empty($savedMember->is_drop_in)) {
                        $connection->execute(
                            "UPDATE gym_member SET is_drop_in = 1 WHERE id = :id",
                            ['id' => $newMember->id]
                        );
                        $this->log('Forzada actualización de is_drop_in para miembro ' . $newMember->id, 'debug');
                    }
                } catch (\Exception $e) {
                    $this->log('Error al verificar is_drop_in: ' . $e->getMessage(), 'error');
                }
            }
            
            // 3. Asignar la clase al miembro
            $memberClass = $gymMemberClassTable->newEntity();
            $memberClassData = [
                'member_id' => $newMember->id,
                'assign_class' => $data['class_id']
            ];
            
            $memberClass = $gymMemberClassTable->patchEntity($memberClass, $memberClassData);
            
            // Guardar la asignación de clase
            if (!$gymMemberClassTable->save($memberClass)) {
                throw new \Exception("Error al asignar la clase al miembro: " . json_encode($memberClass->errors()));
            }
            
            // 4. Crear la reserva para la clase
            $booking = $classBookingTable->newEntity();
            
            // Formatear fecha de reserva
            $bookingDate = $data['booking_date'];
            if (strpos($bookingDate, '/') !== false) {
                $dateParts = explode('/', $bookingDate);
                if (count($dateParts) === 3) {
                    $bookingDate = $dateParts[2] . '-' . $dateParts[0] . '-' . $dateParts[1];
                }
            } else {
                $bookingDate = $this->GYMFunction->get_db_format_date($bookingDate);
            }
            
            // Preparar datos de la reserva
            $bookingData = [
                'member_id' => $newMember->id,
                'class_id' => $data['class_id'],
                'booking_date' => $bookingDate,
                'status' => 'Confirmed',
                'created_at' => date('Y-m-d'),
                'full_name' => $data['first_name'] . ' ' . $data['last_name'],
                'gender' => $data['gender'] ?? '',
                'mobile_no' => $data['mobile'],
                'email' => $data['email'],
                'address' => $data['address'] ?? '',
                'city' => $data['city'] ?? '',
                'state' => $data['state'] ?? '',
                'zipcode' => $data['zipcode'] ?? ''
            ];
            
            // Comprobar si existe la columna is_drop_in y asignar valor directamente
            $columns = $connection->execute("SHOW COLUMNS FROM class_booking LIKE 'is_drop_in'")->fetchAll();
            if (!empty($columns)) {
                $this->log('La columna is_drop_in existe en class_booking', 'debug');
                $bookingData['is_drop_in'] = 1;
            } else {
                $this->log('La columna is_drop_in NO existe en class_booking', 'debug');
            }
            
            // Crear entidad con acceso explícito a todos los campos
            $booking = $classBookingTable->newEntity($bookingData, [
                'accessibleFields' => ['is_drop_in' => true]
            ]);
            
            // Guardar la reserva
            if (!$classBookingTable->save($booking)) {
                throw new \Exception("Error al guardar la reserva: " . json_encode($booking->errors()));
            }
            
            // Verificar si se guardó correctamente el campo is_drop_in en booking
            if (!empty($columns)) {
                try {
                    $savedBooking = $classBookingTable->get($booking->booking_id);
                    $this->log('Valor de is_drop_in después de guardar booking: ' . $savedBooking->is_drop_in, 'debug');
                    
                    // Si no se guardó correctamente, forzar la actualización
                    if (empty($savedBooking->is_drop_in)) {
                        $connection->execute(
                            "UPDATE class_booking SET is_drop_in = 1 WHERE booking_id = :id",
                            ['id' => $booking->booking_id]
                        );
                        $this->log('Forzada actualización de is_drop_in para reserva ' . $booking->booking_id, 'debug');
                    }
                } catch (\Exception $e) {
                    $this->log('Error al verificar is_drop_in en booking: ' . $e->getMessage(), 'error');
                }
            }
            
            // Si todo se guardó correctamente, confirmar la transacción
            $connection->commit();
            
            // Enviar email de confirmación
            $sys_email = $this->GYMFunction->getSettings("email");
            $sys_name = $this->GYMFunction->getSettings("name");
            
            if (!empty($sys_email) && !empty($data['email'])) {
                $headers = "From: {$sys_name} <{$sys_email}>" . "\r\n";
                $message = "Hola {$data['first_name']},\n\nGracias por registrarte como visitante Drop-In.\nTu Usuario: {$data['username']}\nYa puedes iniciar sesión en el sistema con las credenciales proporcionadas.\n\nGracias.";
                @mail($data['email'], __("Registro Drop-In : {$sys_name}"), $message, $headers);
            }
            
            $this->Flash->success(__("El usuario ha sido registrado y la clase reservada exitosamente"));
            return $this->redirect(['action' => 'bookingList']);
            
        } catch (\Exception $e) {
            // Si algo falla, revertir todos los cambios
            $connection->rollback();
            $this->log('Error al registrar usuario drop-in: ' . $e->getMessage(), 'error');
            $this->Flash->error(__("Ha ocurrido un error al procesar el registro: ") . $e->getMessage());
            return null;
        }
    }
}

// dropIn para miembros
/**
 * Permite a un miembro editar su perfil de drop-in y seleccionar una membresía
 * 
 * @return \Cake\Http\Response|null
 */
public function memberDropIn()
{
    // Solo permitir acceso a miembros
    $session = $this->request->session()->read("User");
    if (!isset($session['role_name']) || $session['role_name'] != "member") {
        $this->Flash->error(__("No tienes permisos para acceder a esta página"));
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }
    
    // Verificar si el miembro es un drop-in
    $gymMemberTable = TableRegistry::get('gym_member');
    $membershipTable = TableRegistry::get('membership');
    $membershipPaymentTable = TableRegistry::get('membership_payment');
    $gymMemberClassTable = TableRegistry::get('gym_member_class');
    $classScheduleTable = TableRegistry::get('class_schedule');
    
    // Inicializar la conexión a la base de datos
    $connection = \Cake\Datasource\ConnectionManager::get('default');
    
    $member = $gymMemberTable->get($session['id']);
    
    // Agregar log para depuración antes de la verificación
    $this->log('Member ID: ' . $session['id'] . ', is_drop_in value: ' . var_export($member->is_drop_in, true), 'debug');
    
    // Verificación robusta que utiliza consulta directa a la base de datos
    $isDropIn = false;
    
    // Consulta directa a la base de datos para obtener el valor exacto
    $result = $connection->execute(
        "SELECT is_drop_in FROM gym_member WHERE id = :id",
        ['id' => $session['id']]
    )->fetchAll('assoc');
    
    if (!empty($result) && isset($result[0]['is_drop_in'])) {
        // Comprobar con === para asegurar comparación exacta de tipo y valor
        $isDropIn = ($result[0]['is_drop_in'] === 1 || $result[0]['is_drop_in'] === '1');
        $this->log('Valor exacto en BD: ' . var_export($result[0]['is_drop_in'], true) . ' (tipo: ' . gettype($result[0]['is_drop_in']) . ')', 'debug');
    }
    
    $this->log('Is drop-in check result: ' . ($isDropIn ? 'true' : 'false'), 'debug');
    
    if (!$isDropIn) {
        $this->Flash->error(__("Esta función solo está disponible para miembros drop-in"));
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }
    
    // Obtener las membresías disponibles
    $memberships = $membershipTable->find('list', [
        'keyField' => 'id',
        'valueField' => 'membership_label'
    ])->toArray();
    
    // Obtener las clases disponibles
    $classes = $classScheduleTable->find('list', [
        'keyField' => 'id',
        'valueField' => 'class_name'
    ])->toArray();
    
    // Obtener las clases asignadas al miembro
    $memberClasses = $gymMemberClassTable->find()
        ->where(['member_id' => $session['id']])
        ->toArray();
    
    $assignedClasses = [];
    foreach ($memberClasses as $class) {
        $assignedClasses[] = $class->assign_class;
    }
    
    // Verificar si ya tiene una membresía pendiente
    $pendingMembership = $membershipPaymentTable->find()
        ->where([
            'member_id' => $session['id'],
            'payment_status' => 0 // Pendiente de pago
        ])
        ->order(['created_date' => 'DESC'])
        ->first();
    
    if ($this->request->is(['post', 'put'])) {
        $data = $this->request->getData();
        
        // Comenzar transacción
        $connection->begin();
        
        try {
            // Actualizar datos del miembro
            if (!empty($data['first_name'])) {
                $member->first_name = $data['first_name'];
            }
            if (!empty($data['last_name'])) {
                $member->last_name = $data['last_name'];
            }
            if (!empty($data['gender'])) {
                $member->gender = $data['gender'];
            }
            if (!empty($data['birth_date'])) {
                $member->birth_date = $this->GYMFunction->get_db_format_date($data['birth_date']);
            }
            if (!empty($data['address'])) {
                $member->address = $data['address'];
            }
            if (!empty($data['city'])) {
                $member->city = $data['city'];
            }
            if (!empty($data['state'])) {
                $member->state = $data['state'];
            }
            if (!empty($data['zipcode'])) {
                $member->zipcode = $data['zipcode'];
            }
            if (!empty($data['mobile'])) {
                $member->mobile = $data['mobile'];
            }
            if (!empty($data['email'])) {
                $member->email = $data['email'];
            }
            
            // Guardar los cambios del miembro
            if (!$gymMemberTable->save($member)) {
                throw new \Exception("Error al guardar la información del miembro: " . json_encode($member->errors()));
            }
            
            // Si el miembro ha seleccionado una membresía
            if (!empty($data['selected_membership'])) {
                // Obtener fechas de inicio y fin de la membresía
                $membership = $membershipTable->get($data['selected_membership']);
                $membershipLength = $membership->membership_length;
                
                $startDate = date('Y-m-d'); // Fecha actual como inicio
                if (!empty($data['membership_valid_from'])) {
                    $startDate = $this->GYMFunction->get_db_format_date($data['membership_valid_from']);
                }
                
                $endDate = date('Y-m-d', strtotime($startDate . " + {$membershipLength} days"));
                if (!empty($data['membership_valid_to'])) {
                    $endDate = $this->GYMFunction->get_db_format_date($data['membership_valid_to']);
                }
                
                // Crear un nuevo historial de pago de membresía
                $paymentHistory = $membershipPaymentTable->newEntity();
                
                $paymentData = [
                    'member_id' => $session['id'],
                    'membership_id' => $data['selected_membership'],
                    'membership_amount' => $this->GYMFunction->get_membership_amount($data['selected_membership']),
                    'paid_amount' => 0, // Sin pago inicial
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'payment_status' => 0, // Pendiente
                    'created_date' => date('Y-m-d')
                ];
                
                $paymentHistory = $membershipPaymentTable->patchEntity($paymentHistory, $paymentData);
                
                if (!$membershipPaymentTable->save($paymentHistory)) {
                    throw new \Exception("Error al guardar la solicitud de membresía: " . json_encode($paymentHistory->errors()));
                }
                
                // Agregar a la historia de membresía
                $this->GYMFunction->add_membership_history([
                    'member_id' => $session['id'],
                    'selected_membership' => $data['selected_membership'],
                    'membership_valid_from' => $startDate,
                    'membership_valid_to' => $endDate
                ]);
                
                // Actualizar clases asignadas si se han seleccionado
                if (!empty($data['assign_class'])) {
                    // Eliminar clases actuales
                    $gymMemberClassTable->deleteAll(['member_id' => $session['id']]);
                    
                    // Guardar nuevas clases seleccionadas
                    foreach ($data['assign_class'] as $classId) {
                        $memberClass = $gymMemberClassTable->newEntity();
                        $memberClassData = [
                            'member_id' => $session['id'],
                            'assign_class' => $classId
                        ];
                        
                        $memberClass = $gymMemberClassTable->patchEntity($memberClass, $memberClassData);
                        
                        if (!$gymMemberClassTable->save($memberClass)) {
                            throw new \Exception("Error al asignar la clase: " . json_encode($memberClass->errors()));
                        }
                    }
                }
            }
            
            // Confirmar transacción
            $connection->commit();
            
            $this->Flash->success(__("Tu información ha sido actualizada correctamente"));
            return $this->redirect(['action' => 'memberDropIn']);
            
        } catch (\Exception $e) {
            // Deshacer cambios en caso de error
            $connection->rollback();
            $this->log('Error al actualizar miembro drop-in: ' . $e->getMessage(), 'error');
            $this->Flash->error(__("Error al actualizar: ") . $e->getMessage());
        }
    }
    
    // Preparar datos para la vista
    $this->set('member', $member);
    $this->set('memberships', $memberships);
    $this->set('classes', $classes);
    $this->set('assignedClasses', $assignedClasses);
    $this->set('pendingMembership', $pendingMembership);
    
    // Si hay una membresía pendiente, obtener sus detalles
    if ($pendingMembership) {
        $selectedMembership = $membershipTable->get($pendingMembership->membership_id);
        $this->set('selectedMembership', $selectedMembership);
    }
}
}