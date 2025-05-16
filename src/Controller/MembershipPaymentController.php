<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Gmgt_paypal_class;
use Cake\Datasource\ConnectionManager;

class MembershipPaymentController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		require_once(ROOT . DS . 'vendor' . DS  . 'paypal' . DS . 'paypal_class.php');
		$this->loadComponent("GYMFunction");
	}

	public function paymentList()
	{
		$new_session = $this->request->session();
		$session = $this->request->session()->read("User");

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
		// Iniciar una consulta base con los campos que necesitamos
		$query = $this->MembershipPayment->find("all")
			->contain([
				'GymMember' => function ($q) {
					return $q->select(['id', 'first_name', 'last_name', 'is_maintenance_mode']);
				},
				'Membership' => function ($q) {
					return $q->select(['id', 'membership_label', 'membership_amount', 'maintenance_fee', 'branch_id']);
				}
			]);
		// Aplicar filtros según el rol del usuario
		if ($session["role_name"] == "member") {
			$query->where(["GymMember.id" => $session["id"]]);
		} else if ($branchId) {
			// Filtrar por branch_id para administradores y staff cuando se selecciona una sucursal
			$query->where(["Membership.branch_id" => $branchId]);
		}
		//  // Consulta base según el rol del usuario
		//  if($session["role_name"] == "member") {			
		// 	 $query = $this->MembershipPayment->find("all")
		// 			 ->contain(["Membership","GymMember"])
		// 			 ->where(["GymMember.id" => $session["id"]]);
		//  } else {
		// 	 // Consulta con filtro de sucursal basado en Membership.branch_id
		// 	 $query = $this->MembershipPayment->find("all")
		// 			 ->contain(["Membership","GymMember"]);

		// 	 // Aplicar filtro de sucursal solo para administradores y staff
		// 	 if ($branchId) {
		// 		 // Filtrar por branch_id en la tabla Membership en lugar de GymMember
		// 		 $query->where(["Membership.branch_id" => $branchId]);
		// 	 }
		//  }

		$data = $query->hydrate(false)->toArray();
		$this->set("data", $data);

		// if($session["role_name"] == "member") {			
		// 	$data = $this->MembershipPayment->find("all")->contain(["Membership","GymMember"])->where(["GymMember.id"=>$session["id"]])->hydrate(false)->toArray();
		// }else {
		// 	$data = $this->MembershipPayment->find("all")->contain(["Membership","GymMember"])->hydrate(false)->toArray();
		// }
		// $this->set("data",$data);
		if ($this->request->is("post")) {
			$mp_id = $this->request->data["mp_id"];
			$row = $this->MembershipPayment->get($mp_id);
			if ($this->request->data["payment_method"] == "Stripe" && $session["role_name"] == "member") {
				if (!empty($this->request->data['stripeToken'])) {
					$token = $this->request->data['stripeToken'];
					$email = $this->request->data['stripeEmail'];

					$mem_id = $this->request->data['created_by'];
					$member = $this->MembershipPayment->GymMember->find()->where(["id" => $mem_id])->hydrate(false)->toArray();

					require_once(ROOT . DS . 'vendor' . DS  . 'stripe-php' . DS . 'init.php');
					require_once(ROOT . DS . 'vendor' . DS  . 'stripe-php' . DS . 'stripe-key' . DS . 'stripe-key.php');

					$stripe = array(
						"secret_key" => $this->GYMFunction->getSettings('stripe_secret_key'),
						"publishable_key" => $this->GYMFunction->getSettings('stripe_publishable_key')
					);

					\Stripe\Stripe::setApiKey($stripe['secret_key']);

					$name = $member[0]['first_name'] . '' . $member[0]['last_name'];
					$city = $member[0]['city'];
					$address = $member[0]['address'];
					$zipcode = $member[0]['zipcode'];
					$state = $member[0]['state'];
					$country = $this->GYMFunction->getSettings('country');

					$customer = \Stripe\Customer::create([
						'name' => $name,
						'description' => 'test payment',
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
					$price = $this->request->data['amount'] * 100;
					//$customer_id.'_'.$this->request->data['mp_id']

					$charge = \Stripe\Charge::create([
						'customer' => $customer->id,
						'amount'   => $price,
						'currency' => $currency,
						'description' => $customer_id . '_' . $this->request->data['mp_id'],
					]);

					$chargeJson = $charge->jsonSerialize();

					if (
						$chargeJson['amount_refunded'] == 0 &&
						empty($chargeJson['failure_code']) &&
						$chargeJson['paid'] == 1 &&
						$chargeJson['captured'] == 1
					) {

						$row->paid_amount = $row->paid_amount + $this->request->data["amount"];
						$this->MembershipPayment->save($row);

						$hrow = $this->MembershipPayment->MembershipPaymentHistory->newEntity();
						$data['mp_id'] = $this->request->data['mp_id'];
						$data['amount'] = $chargeJson['amount'] / 100;
						$data['payment_method'] = $this->request->data["payment_method"];
						$data['paid_by_date'] = date("Y-m-d");
						$data['created_by'] = $session["id"];
						$data['trasaction_id'] = $chargeJson['balance_transaction'];

						$hrow = $this->MembershipPayment->MembershipPaymentHistory->patchEntity($hrow, $data);
						if ($this->MembershipPayment->MembershipPaymentHistory->save($hrow)) {
							$this->Flash->success(__("Success! Payment Added Successfully."));
						}
						//debug($chargeJson);die;
					}
				}
			} else if ($this->request->data["payment_method"] == "Paypal" && $session["role_name"] == "member") {
				$mp_id = $this->request->data["mp_id"];
				$user_id = $row->member_id;
				$membership_id = $row->membership_id;
				$custom_var = $mp_id;
				$user_info = $this->MembershipPayment->GymMember->get($user_id);

				$new_session->write("Payment.mp_id", $mp_id);
				$new_session->write("Payment.amount", $this->request->data["amount"]);

				require_once(ROOT . DS . 'vendor' . DS  . 'paypal' . DS . 'paypal_process.php');
			} else {
				$row->paid_amount = $row->paid_amount + $this->request->data["amount"];
				$this->MembershipPayment->save($row);

				$hrow = $this->MembershipPayment->MembershipPaymentHistory->newEntity();
				$data['mp_id'] = $mp_id;
				$data['amount'] = $this->request->data["amount"];
				$data['payment_method'] = $this->request->data["payment_method"];
				$data['paid_by_date'] = date("Y-m-d");
				$data['created_by'] = $session["id"];
				$data['trasaction_id'] = "";

				if (!empty($this->request->data['receipt_photo']['name'])) {
					$file = $this->request->data['receipt_photo'];
					$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
					$valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

					if (in_array(strtolower($ext), $valid_extensions)) {
						$receipts_dir = WWW_ROOT . 'upload' . DS . 'receipts';
						if (!file_exists($receipts_dir)) {
							mkdir($receipts_dir, 0777, true);
						}

						$filename = 'receipt_' . $mp_id . '_' . time() . '.' . $ext;
						$filepath = $receipts_dir . DS . $filename;

						if (move_uploaded_file($file['tmp_name'], $filepath)) {
							\Cake\Log\Log::write('debug', 'Archivo movido correctamente: ' . $filepath);
						} else {
							\Cake\Log\Log::write('error', 'Error al mover el archivo: ' . $file['tmp_name'] . ' a ' . $filepath);
						}
					} else {
						\Cake\Log\Log::write('error', 'Extensión de archivo no válida: ' . $ext);
					}
				}
				// Guardar en la base de datos


				$connection = ConnectionManager::get('default');
				$query = "INSERT INTO membership_payment_history (mp_id, amount, payment_method, paid_by_date, created_by, trasaction_id, receipt_photo, payment_confirmation_status)
						   VALUES (:mp_id, :amount, :payment_method, :paid_by_date, :created_by, :trasaction_id, :receipt_photo, :payment_confirmation_status)";

				$params = [
					'mp_id' => $mp_id,
					'amount' => $this->request->data['amount'],
					'payment_method' => $this->request->data['payment_method'],
					'paid_by_date' => date('Y-m-d'),
					'created_by' => $session['id'],
					'trasaction_id' => '',
					'receipt_photo' => isset($filename) ? 'upload/receipts/' . $filename : null,
					'payment_confirmation_status' => 'Pending',
				];


				try {
					\Cake\Log\Log::write('debug', 'Datos que se intentan guardar: ' . json_encode($params));
					$connection->execute($query, $params);

					// Verificar si el método de pago es "Pago Online" y redirigir a Pagopar
					if ($this->request->data["payment_method"] == "Pago Online") {
						try {
							// Obtener el ID del último registro insertado
							$result = $connection->execute("SELECT LAST_INSERT_ID() as id")->fetchAll('assoc');

							if (empty($result) || !isset($result[0]['id'])) {
								throw new \Exception("No se pudo obtener el ID del registro insertado");
							}

							$last_id = $result[0]['id'];
							\Cake\Log\Log::write('debug', 'ID del último registro insertado: ' . $last_id);

							// Actualizar el registro con un estado específico para pagos online
							$updateResult = $connection->execute(
								"UPDATE membership_payment_history SET payment_confirmation_status = 'Pending' WHERE payment_history_id = :id",
								['id' => $last_id]
							);

							\Cake\Log\Log::write('debug', 'Registro actualizado para pago online. Filas afectadas: ' . $updateResult->rowCount());

							// Mostrar mensaje de éxito antes de redirigir
							$this->Flash->success(__("Success! Redirecting to payment gateway..."));

							// Redirigir a Pagopar con el ID del pago
							return $this->redirect([
								'controller' => 'Pagopar',
								'action' => 'checkout',
								$mp_id
							]);
						} catch (\Exception $e) {
							\Cake\Log\Log::write('error', 'Error al procesar pago online: ' . $e->getMessage());
							$this->Flash->error(__("Error processing online payment: ") . $e->getMessage());
							return $this->redirect(["action" => "paymentList"]);
						}
					} else {
						// Para otros métodos de pago, solo mostrar mensaje de éxito
						$this->Flash->success(__("Success! Payment Added Successfully."));
					}
				} catch (\Exception $e) {
					\Cake\Log\Log::write('error', 'Error al guardar en la base de datos: ' . $e->getMessage());
					$this->Flash->error(__("Error! Could not save payment."));
				}



				//  try {
				// 	\Cake\Log\Log::write('debug', 'Datos que se intentan guardar: ' . json_encode($params));
				// 	$connection->execute($query, $params);
				// 	$this->Flash->success(__("Success! Payment Added Successfully."));

				// } catch (\Exception $e) {
				// 	\Cake\Log\Log::write('error', 'Error al guardar en la base de datos: ' . $e->getMessage());
				// 	$this->Flash->error(__("Error! Could not save payment."));
				// }
				// Handle receipt photo upload
				// if (!empty($this->request->data['receipt_photo']['name'])) {
				// 	$file = $this->request->data['receipt_photo'];
				// 	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
				// 	$valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];

				// 	// Validate file extension
				// 	if (in_array(strtolower($ext), $valid_extensions)) {
				// 		// Create receipts directory if it doesn't exist
				// 		$receipts_dir = WWW_ROOT . 'upload' . DS . 'receipts';
				// 		if (!file_exists($receipts_dir)) {
				// 			mkdir($receipts_dir, 0777, true);
				// 		}

				// 		// Generate unique filename
				// 		$filename = 'receipt_' . $mp_id . '_' . time() . '.' . $ext;
				// 		$filepath = $receipts_dir . DS . $filename;

				// 		// Move uploaded file
				// 		if (move_uploaded_file($file['tmp_name'], $filepath)) {
				// 			// Save file path in database
				// 			$data['receipt_photo'] = 'upload/receipts/' . $filename;
				// 			$data['payment_confirmation_status'] = 'Pending';
				// 		}
				// 	}
				// }

				// $hrow = $this->MembershipPayment->MembershipPaymentHistory->patchEntity($hrow,$data);						
				// if($this->MembershipPayment->MembershipPaymentHistory->save($hrow))
				// {
				// 	$this->Flash->success(__("Success! Payment Added Successfully."));
				// }
			}
			return $this->redirect(["action" => "paymentList"]);
		}
	}

	public function generatePaymentInvoice()
	{
		$this->set("edit", false);
		$members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "member"]);
		$members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
		$this->set("members", $members);

		$membership = $this->MembershipPayment->Membership->find("list", ["keyField" => "id", "valueField" => "membership_label"]);
		$this->set("membership", $membership);

		if ($this->request->is('post')) {
			$mid = $this->request->data["user_id"];

			$start_date = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_from']);

			$end_date = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_to']);
			$row = $this->MembershipPayment->newEntity();
			$pdata["member_id"] = $mid;
			$pdata["membership_id"] = $this->request->data["membership_id"];
			$pdata["membership_amount"] = $this->request->data["membership_amount"];
			$pdata["paid_amount"] = 0;
			$pdata["start_date"] = $start_date;
			$pdata["end_date"] = $end_date;
			$pdata["membership_status"] = "Continue";
			$pdata["payment_status"] = 0;
			$pdata["created_date"] = date("Y-m-d");
			$row = $this->MembershipPayment->patchEntity($row, $pdata);
			$this->MembershipPayment->save($row);
			################## MEMBER's Current Membership Change ##################
			$member_data = $this->MembershipPayment->GymMember->get($mid);
			$member_data->selected_membership = $this->request->data["membership_id"];
			$member_data->membership_valid_from = $start_date;
			$member_data->membership_valid_to = $end_date;
			$this->MembershipPayment->GymMember->save($member_data);
			#####################Add Membership History #############################
			$mem_histoty = $this->MembershipPayment->MembershipHistory->newEntity();
			$hdata["member_id"] = $mid;
			$hdata["selected_membership"] = $this->request->data["membership_id"];
			$hdata["membership_valid_from"] = $start_date;
			$hdata["membership_valid_to"] = $end_date;
			$hdata["created_date"] = date("Y-m-d");
			$hdata = $this->MembershipPayment->MembershipHistory->patchEntity($mem_histoty, $hdata);
			if ($this->MembershipPayment->MembershipHistory->save($mem_histoty)) {
				$this->Flash->success(__("Success! Payment Added Successfully."));
				return $this->redirect(["action" => "paymentList"]);
			}
		}
	}

	public function membershipEdit($eid)
	{
		$this->set("edit", true);
		$members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "member"]);
		$members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
		$this->set("members", $members);

		$membership = $this->MembershipPayment->Membership->find("list", ["keyField" => "id", "valueField" => "membership_label"]);
		$this->set("membership", $membership);

		$data = $this->MembershipPayment->get($eid);
		$this->set("data", $data->toArray());

		if ($this->request->is("post")) {
			$mid = $this->request->data["user_id"];
			//$start_date = date("Y-m-d",strtotime($this->request->data["membership_valid_from"]));
			$start_date = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_from']);
			$end_date = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_to']);
			//$end_date = date("Y-m-d",strtotime($this->request->data["membership_valid_to"]));

			$row = $this->MembershipPayment->get($eid);
			$row->member_id = $mid;
			$row->membership_id = $this->request->data["membership_id"];
			$row->membership_amount = $this->request->data["membership_amount"];
			$row->paid_amount = 0;
			$row->start_date = $start_date;
			$row->end_date = $end_date;
			$row->membership_status = "Continue";
			$this->MembershipPayment->save($row);
			###############################################################
			$member_data = $this->MembershipPayment->GymMember->get($mid);
			$member_data->selected_membership = $this->request->data["membership_id"];
			$member_data->membership_valid_from = $start_date;
			$member_data->membership_valid_to = $end_date;
			$this->MembershipPayment->GymMember->save($member_data);
			###########################################################
			$this->Flash->success(__("Success! Record Updated Successfully."));
			return $this->redirect(["action" => "paymentList"]);
		}
		$this->render("generatePaymentInvoice");
	}

	public function deletePayment($mp_id)
	{
		$row = $this->MembershipPayment->get($mp_id);
		if ($this->MembershipPayment->delete($row)) {
			$this->Flash->success(__("Success! Payment Record Deleted Successfully."));
			return $this->redirect(["action" => "paymentList"]);
		}
	}

	public function incomeList()
	{
		$data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["GymMember"])->where(["invoice_type" => "income"])->hydrate(false)->toArray();


		$this->set("data", $data);
	}

	public function addIncome()
	{
		$session = $this->request->session()->read("User");
		$this->set("edit", false);
		$members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "member"]);
		$members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
		$this->set("members", $members);

		if ($this->request->is("post")) {
			$row = $this->MembershipPayment->GymIncomeExpense->newEntity();
			$data = $this->request->data;

			$total_amount = null;
			foreach ($data["income_amount"] as $amount) {
				$total_amount += $amount;
			}
			$data["total_amount"] = $total_amount;
			$data["entry"] = $this->get_entry_records($data);
			$data["receiver_id"] = $session["id"]; //current userid;			
			$data["invoice_date"] = date("Y-m-d");
			$data["invoice_date"] = $this->GYMFunction->get_db_format_date($data['invoice_date']);

			$row = $this->MembershipPayment->GymIncomeExpense->patchEntity($row, $data);
			if ($this->MembershipPayment->GymIncomeExpense->save($row)) {
				$this->Flash->success(__("Success! Record Saved Successfully."));
				return $this->redirect(["action" => "incomeList"]);
			}
		}
	}

	public function get_entry_records($data)
	{
		$all_income_entry = $data['income_entry'];
		$all_income_amount = $data['income_amount'];

		$entry_data = array();
		$i = 0;
		foreach ($all_income_entry as $one_entry) {
			$entry_data[] = array(
				'entry' => $one_entry,
				'amount' => $all_income_amount[$i]
			);
			$i++;
		}
		return json_encode($entry_data);
	}

	public function incomeEdit($eid)
	{
		$this->set("edit", true);
		$members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "member"]);
		$members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
		$this->set("members", $members);

		$row = $this->MembershipPayment->GymIncomeExpense->get($eid);
		$this->set("data", $row->toArray());

		if ($this->request->is("post")) {
			$data = $this->request->data;
			$total_amount = null;
			foreach ($data["income_amount"] as $amount) {
				$total_amount += $amount;
			}
			$data["total_amount"] = $total_amount;
			$data["entry"] = $this->get_entry_records($data);
			$data["invoice_date"] = date("Y-m-d");
			$data["invoice_date"] = $this->GYMFunction->get_db_format_date($data['invoice_date']);

			$row = $this->MembershipPayment->GymIncomeExpense->patchEntity($row, $data);
			if ($this->MembershipPayment->GymIncomeExpense->save($row)) {
				$this->Flash->success(__("Success! Record Updated Successfully."));
				return $this->redirect(["action" => "incomeList"]);
			}
		}
		$this->render("addIncome");
	}

	public function deleteIncome($did)
	{
		$row = $this->MembershipPayment->GymIncomeExpense->get($did);
		if ($this->MembershipPayment->GymIncomeExpense->delete($row)) {
			$this->Flash->success(__("Success! Record Deleted Successfully."));
			return $this->redirect($this->referer());
		}
	}

	public function printInvoice()
	{
		$this->loadComponent("GYMFunction");

		$id = $this->request->params["pass"][0];

		$invoice_type = $this->request->params["pass"][1];

		$in_ex_table = TableRegistry::get("GymIncomeExpense");
		$setting_tbl = TableRegistry::get("GeneralSetting");
		$pay_history_tbl = TableRegistry::get("MembershipPaymentHistory");

		$income_data = array();
		$expense_data = array();
		$invoice_data = array();

		$sys_data = $setting_tbl->find()->select(["name", "address", "gym_logo", "date_format", "office_number", "country"])->hydrate(false)->toArray();

		if ($invoice_type == "invoice") {
			$invoice_data = $this->MembershipPayment->find("all")->contain(["GymMember", "Membership"])->where(["mp_id" => $id])->hydrate(false)->toArray();

			$history_data = $pay_history_tbl->find("all")->where(["mp_id" => $id])->hydrate(false)->toArray();
			// $invoice_no = $pay_history_tbl->find("all")->last()->toArray();

			//debug($invoice_no['payment_history_id']);
			$session = $this->request->session();
			$float_l = ($session->read("User.is_rtl") == "1") ? "right" : "left";
			$float_r = ($session->read("User.is_rtl") == "1") ? "left" : "right";

			//$this->set("invoice_no",$invoice_no['payment_history_id']);
			$this->set("invoice_no", $id);
			$this->set("invoice_data", $invoice_data[0]);
			$this->set("history_data", $history_data);
		} else if ($invoice_type == "income") {
			$income_data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["GymMember"])->where(["GymIncomeExpense.id" => $id])->hydrate(false)->toArray();
			$this->set("income_data", $income_data[0]);
			$this->set("expense_data", $expense_data);
			$this->set("invoice_data", $invoice_data);
		} else if ($invoice_type == "expense") {
			$expense_data = $this->MembershipPayment->GymIncomeExpense->find("all")->where(["GymIncomeExpense.id" => $id])->select($this->MembershipPayment->GymIncomeExpense);
			$expense_data = $expense_data->leftjoin(
				["GymMember" => "gym_member"],
				["GymIncomeExpense.receiver_id = GymMember.id"]
			)->select($this->MembershipPayment->GymMember)->hydrate(false)->toArray();
			$expense_data[0]["gym_member"] = $expense_data[0]["GymMember"];
			unset($expense_data[0]["GymMember"]);
			$this->set("income_data", $income_data);
			$this->set("expense_data", $expense_data[0]);
			$this->set("invoice_data", $invoice_data);
		}

		$this->set("sys_data", $sys_data[0]);
	}

	public function expenseList()
	{
		$data = $this->MembershipPayment->GymIncomeExpense->find("all")->where(["invoice_type" => "expense"])->hydrate(false)->toArray();
		$this->set("data", $data);
	}

	public function addExpense()
	{
		$this->set("edit", false);
		$session = $this->request->session()->read("User");

		if ($this->request->is("post")) {
			$row = $this->MembershipPayment->GymIncomeExpense->newEntity();
			$data = $this->request->data;
			$total_amount = null;
			foreach ($data["income_amount"] as $amount) {
				$total_amount += $amount;
			}
			$data["total_amount"] = $total_amount;
			$data["entry"] = $this->get_entry_records($data);
			$data["receiver_id"] = $session["id"]; //current userid;			
			//$data["invoice_date"] = date("Y-m-d",strtotime($data["invoice_date"]));	
			$data["invoice_date"] = $this->GYMFunction->get_db_format_date($this->request->data['invoice_date']);
			$row = $this->MembershipPayment->GymIncomeExpense->patchEntity($row, $data);
			if ($this->MembershipPayment->GymIncomeExpense->save($row)) {
				$this->Flash->success(__("Success! Record Saved Successfully."));
				return $this->redirect(["action" => "expenseList"]);
			}
		}
	}

	public function expenseEdit($eid)
	{
		$this->set("edit", true);

		$row = $this->MembershipPayment->GymIncomeExpense->get($eid);
		$this->set("data", $row->toArray());

		if ($this->request->is("post")) {
			$data = $this->request->data;
			$total_amount = null;
			foreach ($data["income_amount"] as $amount) {
				$total_amount += $amount;
			}
			$data["total_amount"] = $total_amount;
			$data["entry"] = $this->get_entry_records($data);
			$data["invoice_date"] = $this->GYMFunction->get_db_format_date($this->request->data['invoice_date']);

			$row = $this->MembershipPayment->GymIncomeExpense->patchEntity($row, $data);
			if ($this->MembershipPayment->GymIncomeExpense->save($row)) {
				$this->Flash->success(__("Success! Record Updated Successfully."));
				return $this->redirect(["action" => "expenseList"]);
			}
		}
		$this->render("addExpense");
	}

	public function deleteAccountant($id)
	{
		$row = $this->GymAccountant->GymMember->get($id);
		if ($this->GymAccountant->GymMember->delete($row)) {
			$this->Flash->success(__("Success! Accountant Deleted Successfully."));
			return $this->redirect($this->referer());
		}
	}

	public function paymentSuccess()
	{
		$payment_data = $this->request->session()->read("Payment");
		$session = $this->request->session()->read("User");
		$feedata['mp_id'] = $payment_data["mp_id"];
		$feedata['amount'] = $payment_data['amount'];
		$feedata['payment_method'] = 'Paypal';
		$feedata['paid_by_date'] = date("Y-m-d");
		$feedata['created_by'] = $session["id"];
		$row = $this->MembershipPayment->MembershipPaymentHistory->newEntity();
		$row = $this->MembershipPayment->MembershipPaymentHistory->patchEntity($row, $feedata);
		if ($this->MembershipPayment->MembershipPaymentHistory->save($row)) {
			$row = $this->MembershipPayment->get($payment_data["mp_id"]);
			$row->paid_amount = $row->paid_amount + $payment_data['amount'];
			$this->MembershipPayment->save($row);
		}

		$session = $this->request->session();
		$session->delete('Payment');

		$this->Flash->success(__("Success! Payment Successfully Completed."));
		return $this->redirect(["action" => "paymentList"]);
	}

	public function ipnFunction()
	{
		if ($this->request->is("post")) {
			$trasaction_id  = $_POST["txn_id"];
			$custom_array = explode("_", $_POST['custom']);
			$feedata['mp_id'] = $custom_array[1];
			$feedata['amount'] = $_POST['mc_gross_1'];
			$feedata['payment_method'] = 'Paypal';
			$feedata['trasaction_id'] = $trasaction_id;
			$feedata['created_by'] = $custom_array[0];
			//$log_array		= print_r($feedata, TRUE);
			//wp_mail( 'bhaskar@dasinfomedia.com', 'gympaypal', $log_array);
			$row = $this->MembershipPayment->MembershipPaymentHistory->newEntity();
			$row = $this->MembershipPayment->MembershipPaymentHistory->patchEntity($row, $feedata);
			if ($this->MembershipPayment->MembershipPaymentHistory->save($row)) {
				$this->Flash->success(__("Success! Payment Successfully Completed."));
			} else {
				$this->Flash->error(__("Paypal Payment IPN save failed to DB."));
			}
			return $this->redirect(["action" => "paymentList"]);
			//require_once SMS_PLUGIN_DIR. '/lib/paypal/paypal_ipn.php';
		}
	}

	public function isAuthorized($user)
	{
		$role_name = $user["role_name"];
		$curr_action = $this->request->action;
		$members_actions = ["paymentList", "paymentSuccess", "ipnFunction", "printInvoice"];
		$staff_actions = ["paymentList", "addIncome", "incomeList", "expenseList", "addExpense", "incomeEdit", "expenseEdit"];
		$acc_actions = ["paymentList", "addIncome", "incomeList", "expenseList", "addExpense", "incomeEdit", "expenseEdit", "printInvoice", "deleteIncome"];
		switch ($role_name) {
			case "member":
				if (in_array($curr_action, $members_actions)) {
					return true;
				} else {
					return false;
				}
				break;

			/*CASE "staff_member":
				if(in_array($curr_action,$staff_actions))
				{return true;}else{ return false;}
			break;*/

			case "accountant":
				if (in_array($curr_action, $acc_actions)) {
					return true;
				} else {
					return false;
				}
				break;
		}
		return parent::isAuthorized($user);
	}
}
