<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Network\Session\DatabaseSession;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Connection;
use Cake\Mailer\Email;
use Cake\I18n\Time;
use Cake\Log\Log;

class GymMemberController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('Paginator');
		$this->loadComponent("GYMFunction");
		require_once(ROOT . DS . 'vendor' . DS  . 'chart' . DS . 'GoogleCharts.class.php');
		$session = $this->request->session()->read("User");
		$this->set("session", $session);
	}

	public function memberList()
	{
		$session = $this->request->session()->read("User");
		if ($session["role_name"] == "administrator") {
			$data = $this->GymMember->find("all")->contain(['MemberTags'])->where(["role_name" => "member"])->hydrate(false)->toArray();
			// debug($data);die;
			// dd($data);
		} else if ($session["role_name"] == "member") {
			$uid = intval($session["id"]);
			if ($this->GYMFunction->getSettings("member_can_view_other")) {
				$data = $this->GymMember->find("all")->contain(['MemberTags'])->where(["role_name" => "member"])->hydrate(false)->toArray();
			} else {
				$data = $this->GymMember->find("all")->contain(['MemberTags'])->where(["id" => $uid])->hydrate(false)->toArray();
			}
		} else if ($session["role_name"] == "staff_member") {
			$uid = intval($session["id"]);
			if ($this->GYMFunction->getSettings("staff_can_view_own_member")) {
				$data = $this->GymMember->find("all")->contain(['MemberTags'])->where(["assign_staff_mem" => $uid])->hydrate(false)->toArray();
			} else {
				$data = $this->GymMember->find("all")->contain(['MemberTags'])->where(["role_name" => "member"])->hydrate(false)->toArray();
			}
		} else {
			$data = $this->GymMember->find("all")->contain(['MemberTags'])->where(["role_name" => "member"])->hydrate(false)->toArray();
		}


		$this->set("data", $data);
	}
	public function addMember()
	{
		Log::write('debug', 'Datos enviados: ' . json_encode($this->request->getData()));
		// Log::write('debug', 'Intentando guardar el miembro...');
		$this->set("edit", false);
		$this->set("title", __("Add Member"));
		
		$tagsTable = TableRegistry::getTableLocator()->get('MemberTags');
		$tags = $tagsTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();
	
		$this->set('tags', $tags);
		
		// Inicializar $data con valores predeterminados para evitar errores en la vista
		$data = [
			'member_id' => '',
			'first_name' => '',
			'middle_name' => '',
			'last_name' => '',
			'gender' => '',
			'birth_date' => '',
			'assign_group' => '',
			'address' => '',
			'city' => '',
			'state' => '',
			'zipcode' => '',
			'mobile' => '',
			'phone' => '',
			'email' => '',
			'weight' => '',
			'height' => '',
			'chest' => '',
			'waist' => '',
			'thing' => '',
			'arms' => '',
			'fat' => '',
			'username' => '',
			'password' => '',
			'image' => '',
			'assign_staff_mem' => '',
			'intrested_area' => '',
			'g_source' => '',
			'referrer_by' => '',
			'inquiry_date' => '',
			'trial_end_date' => '',
			'member_type' => 'Member',
			'selected_membership' => '',
			'membership_valid_from' => '',
			'membership_valid_to' => ''
		];
		$this->set('data', $data);
	
		$lastid = $this->GymMember->find("all", ["fields" => "id"])->last();
		$lastid = ($lastid != null) ? $lastid->id + 1 : 01;
	
		$member = $this->GymMember->newEntity();
		$m = date("d");
		$y = date("y");
		$prefix = "M" . $lastid;
		$member_id = $prefix . $m . $y;
	
		$this->set("member_id", $member_id);
		$staff = $this->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "staff_member"]);
		$staff = $staff->select(["id", "name" => $staff->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
		$classes = $this->GymMember->ClassSchedule->find("list", ["keyField" => "id", "valueField" => "class_name"]);
		$groups = $this->GymMember->GymGroup->find("list", ["keyField" => "id", "valueField" => "name"]);
		$interest = $this->GymMember->GymInterestArea->find("list", ["keyField" => "id", "valueField" => "interest"]);
		$source = $this->GymMember->GymSource->find("list", ["keyField" => "id", "valueField" => "source_name"]);
		$membership = $this->GymMember->Membership->find("list", ["keyField" => "id", "valueField" => "membership_label"]);
	
		$this->set("staff", $staff);
		$this->set("classes", $classes);
		$this->set("groups", $groups);
		$this->set("interest", $interest);
		$this->set("source", $source);
		$this->set("membership", $membership);
		$this->set("referrer_by", $staff);
	
		if ($this->request->is("post")) {
			$ext = $this->GYMFunction->check_valid_extension($this->request->data['image']['name']);
			if ($ext != 0) {
				$this->request->data['member_id'] = $member_id;
				$image = $this->GYMFunction->uploadImage($this->request->data['image']);
				$this->request->data['image'] = (!empty($image)) ? $image : "Thumbnail-img.png";
				$this->request->data['birth_date'] = $this->GYMFunction->get_db_format_date($this->request->data['birth_date']);
				$this->request->data['inquiry_date'] = $this->GYMFunction->get_db_format_date($this->request->data['inquiry_date']);
				$this->request->data['trial_end_date'] = $this->GYMFunction->get_db_format_date($this->request->data['trial_end_date']);
				
				if (isset($this->request->data['membership_valid_from'])) {
					$this->request->data['membership_valid_from'] = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_from']);
				}
				if (isset($this->request->data['membership_valid_to'])) {
					$this->request->data['membership_valid_to'] = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_to']);
				}
				if ($this->request->data['first_pay_date'] != '') {
					$this->request->data['first_pay_date'] = $this->GYMFunction->get_db_format_date($this->request->data['first_pay_date']);
				}
	
				$this->request->data['created_date'] = date("Y-m-d");
				$this->request->data['assign_group'] = json_encode($this->request->data['assign_group']);
				
				switch ($this->request->data['member_type']) {
					case "Member":
						$this->request->data['membership_status'] = "Continue";
						break;
					case "Prospect":
						$this->request->data['membership_status'] = "Not Available";
						break;
					case "Alumni":
						$this->request->data['membership_status'] = "Expired";
						break;
				}
				
				$this->request->data["role_name"] = "member";
				$this->request->data["activated"] = 1;
				
				  // Validar assigned_tags
				  if (empty($this->request->data['assigned_tags'])) {
					Log::write('error', 'El campo assigned_tags está vacío. No se puede guardar el miembro.');
					$this->Flash->error(__("Por favor, selecciona un tag antes de guardar."));
					return $this->redirect(["action" => "add-member"]);
				} else {
					$this->request->data['assigned_tags'] = (int)$this->request->data['assigned_tags'];
				}
				
				Log::write('debug', 'Valor de assigned_tags antes de patchEntity: ' . $this->request->data['assigned_tags']);
				$member = $this->GymMember->patchEntity($member, $this->request->data);
				Log::write('debug', 'Valor de assigned_tags después de patchEntity: ' . $member->assigned_tags);
				
				// Forzar la asignación del valor
				$member->assigned_tags = (int)$this->request->data['assigned_tags'];
				Log::write('debug', 'Valor de assigned_tags forzado antes de guardar: ' . $member->assigned_tags);
				Log::write('debug', 'Entidad completa antes de guardar: ' . json_encode($member->toArray()));
				
				if ($this->GymMember->save($member)) {
					// Actualizar directamente con SQL por si acaso
					$id = $member->id;
					$tag = (int)$this->request->data['assigned_tags'];
					$connection = \Cake\Datasource\ConnectionManager::get('default');
					$connection->execute("UPDATE gym_member SET assigned_tags = :tag WHERE id = :id", ['tag' => $tag, 'id' => $id]);
					
					$saved = $this->GymMember->get($member->id);
					Log::write('debug', 'Valor de assigned_tags guardado en la base de datos: ' . $saved->assigned_tags);
					

					$this->request->data['member_id'] = $member->id;
					$this->GYMFunction->add_membership_history($this->request->data);
					
					if ($this->addPaymentHistory($this->request->data)) {
						$this->Flash->success(__("Success! Record Saved Successfully."));
					}
	
					foreach ($this->request->data["assign_class"] as $class) {
						$new_row = $this->GymMember->GymMemberClass->newEntity();
						$data = array();
						$data["member_id"] = $member->id;
						$data["assign_class"] = $class;
						$new_row = $this->GymMember->GymMemberClass->patchEntity($new_row, $data);
						$this->GymMember->GymMemberClass->save($new_row);
					}
					
					return $this->redirect(["action" => "memberList"]);
				} else {
					Log::write('error', 'Error al guardar el miembro: ' . json_encode($member->getErrors()));
					if ($member->errors()) {
						foreach ($member->errors() as $error) {
							foreach ($error as $key => $value) {
								$this->Flash->error(__($value));
							}
						}
					}
				}
			} else {
				$this->Flash->error(__("Invalid File Extension, Please Retry."));
				return $this->redirect(["action" => "add-member"]);
			}
		}
	}
	// public function addMember()
	// {
	// 	$this->set("edit", false);
	// 	$this->set("title", __("Add Member"));
		
	// 	$tagsTable = TableRegistry::getTableLocator()->get('MemberTags');
	// 	$tags = $tagsTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();
	
	// 	$this->set('tags', $tags);

	// 	$lastid = $this->GymMember->find("all", ["fields" => "id"])->last();
	// 	$lastid = ($lastid != null) ? $lastid->id + 1 : 01;

	// 	$member = $this->GymMember->newEntity();
	// 	$m = date("d");
	// 	$y = date("y");
	// 	$prefix = "M" . $lastid;
	// 	$member_id = $prefix . $m . $y;

	// 	$this->set("member_id", $member_id);
	// 	$staff = $this->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "staff_member"]);
	// 	$staff = $staff->select(["id", "name" => $staff->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
	// 	$classes = $this->GymMember->ClassSchedule->find("list", ["keyField" => "id", "valueField" => "class_name"]);
	// 	$groups = $this->GymMember->GymGroup->find("list", ["keyField" => "id", "valueField" => "name"]);
	// 	$interest = $this->GymMember->GymInterestArea->find("list", ["keyField" => "id", "valueField" => "interest"]);
	// 	$source = $this->GymMember->GymSource->find("list", ["keyField" => "id", "valueField" => "source_name"]);
	// 	$membership = $this->GymMember->Membership->find("list", ["keyField" => "id", "valueField" => "membership_label"]);

	// 	$this->set("staff", $staff);
	// 	$this->set("classes", $classes);
	// 	$this->set("groups", $groups);
	// 	$this->set("interest", $interest);
	// 	$this->set("source", $source);
	// 	$this->set("membership", $membership);
	// 	$this->set("referrer_by", $staff);

	// 	if ($this->request->is("post")) {
	// 		// debug($this->request->data);
	// 		$ext = $this->GYMFunction->check_valid_extension($this->request->data['image']['name']);
	// 		if ($ext != 0) {

	// 			$this->request->data['member_id'] = $member_id;
	// 			$image = $this->GYMFunction->uploadImage($this->request->data['image']);
	// 			$this->request->data['image'] = (!empty($image)) ? $image : "Thumbnail-img.png";
	// 			$this->request->data['birth_date'] = $this->GYMFunction->get_db_format_date($this->request->data['birth_date']);
	// 			//$this->request->data['birth_date'] = date("Y-m-d",strtotime($this->request->data['birth_date']));
	// 			//$this->request->data['inquiry_date'] = date("Y-m-d",strtotime($this->request->data['inquiry_date']));
	// 			$this->request->data['inquiry_date'] = $this->GYMFunction->get_db_format_date($this->request->data['inquiry_date']);
	// 			$this->request->data['trial_end_date'] = $this->GYMFunction->get_db_format_date($this->request->data['trial_end_date']);
	// 			//$this->request->data['trial_end_date'] = date("Y-m-d",strtotime($this->request->data['trial_end_date']));
	// 			if (isset($this->request->data['membership_valid_from'])) {
	// 				//$this->request->data['membership_valid_from'] = date("Y-m-d",strtotime($this->request->data['membership_valid_from']));
	// 				$this->request->data['membership_valid_from'] = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_from']);
	// 			}
	// 			if (isset($this->request->data['membership_valid_to'])) {
	// 				//$this->request->data['membership_valid_to'] = date("Y-m-d",strtotime($this->request->data['membership_valid_to']));
	// 				$this->request->data['membership_valid_to'] = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_to']);
	// 			}
	// 			//$this->request->data['first_pay_date'] = date("Y-m-d",strtotime($this->request->data['first_pay_date']));
	// 			if ($this->request->data['first_pay_date'] != '') {
	// 				$this->request->data['first_pay_date'] = $this->GYMFunction->get_db_format_date($this->request->data['first_pay_date']);
	// 			}

	// 			$this->request->data['created_date'] = date("Y-m-d");
	// 			$this->request->data['assign_group'] = json_encode($this->request->data['assign_group']);
	// 			switch ($this->request->data['member_type']) {
	// 				case "Member":
	// 					$this->request->data['membership_status'] = "Continue";
	// 					break;
	// 				case "Prospect":
	// 					$this->request->data['membership_status'] = "Not Available";
	// 					break;
	// 				case "Alumni":
	// 					$this->request->data['membership_status'] = "Expired";
	// 					break;
	// 			}
	// 			$this->request->data["role_name"] = "member";
	// 			$this->request->data["activated"] = 1;
	// 			  // En addMember() y editMember()
	// 			  if (empty($this->request->data['assigned_tags'])) {
	// 				$this->request->data['assigned_tags'] = '11';
	// 			}




	// 			$member = $this->GymMember->patchEntity($member, $this->request->data);
	// 			// debug('hola'.$member);
	// 			// die(); // Detener la ejecución para examinar
	// 			if ($this->GymMember->save($member)) {
	// 				$this->request->data['member_id'] = $member->id;
	// 				$this->GYMFunction->add_membership_history($this->request->data);
					
	// 				if ($this->addPaymentHistory($this->request->data)) {
	// 					$this->Flash->success(__("Success! Record Saved Successfully."));
	// 				}

	// 				foreach ($this->request->data["assign_class"] as $class) {
	// 					$new_row = $this->GymMember->GymMemberClass->newEntity();
	// 					$data = array();
	// 					$data["member_id"] = $member->id;
	// 					$data["assign_class"] = $class;
	// 					$new_row = $this->GymMember->GymMemberClass->patchEntity($new_row, $data);
	// 					$this->GymMember->GymMemberClass->save($new_row);
	// 				}
	// 			} else {
	// 				if ($member->errors()) {
	// 					foreach ($member->errors() as $error) {
	// 						foreach ($error as $key => $value) {
	// 							$this->Flash->error(__($value));
	// 						}
	// 					}
	// 				}
	// 			}
	// 			return $this->redirect(["action" => "memberList"]);
	// 		} else {
	// 			$this->Flash->error(__("Invalid File Extension, Please Retry."));
	// 			return $this->redirect(["action" => "add-member"]);
	// 		}
	// 	}
	// }


	public function addPaymentHistory($data)
	{
		$row = $this->GymMember->MembershipPayment->newEntity();
		$save["member_id"] = $data["member_id"];
		$save["membership_id"] = $data["selected_membership"];
		$save["membership_amount"] = $this->GYMFunction->get_membership_amount($data["selected_membership"]);
		$save["paid_amount"] = 0;
		$save["start_date"] = $data["membership_valid_from"];
		$save["end_date"] = $data["membership_valid_to"];
		$save["membership_status"] = $data["membership_status"];
		$save["payment_status"] = 0;
		$save["created_date"] = date("Y-m-d");
		$save["created_by"] = 1;
		$row = $this->GymMember->MembershipPayment->patchEntity($row, $save);
		if ($this->GymMember->MembershipPayment->save($row)) {
			return true;
		} else {
			return false;
		}
	}
	public function editMember($id)
	{
		$this->set("edit", true);
		$this->set("title", __("Edit Member"));
		$this->set("eid", $id);
		$membership_classes_id = array();
		$session = $this->request->session()->read("User");
		 // Obtener los datos del miembro incluyendo la relación con MemberTags
		 $data = $this->GymMember->get($id, [
			'contain' => ['MemberTags']
		])->toArray();
	
	    $tagsTable = TableRegistry::getTableLocator()->get('MemberTags');
		$tags = $tagsTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();

		$this->set('tags', $tags);
    
		$membership_classes = $this->GymMember->Membership->find()->where(["id" => $data['selected_membership']])->select(["membership_class"])->hydrate(false)->toArray();
	
		foreach ($membership_classes as $value) {
			$membership_classes_id[] = $value['membership_class'];
		}
	
		if (!empty($membership_classes_id)) {
			$classes = $this->GymMember->ClassSchedule->find("list", ["keyField" => "id", "valueField" => "class_name"])->where(["id IN" => $membership_classes_id]);
		} else {
			$classes = array();
		}
	
		$member_classes = $this->GymMember->GymMemberClass->find()->where(["member_id" => $id])->select(["assign_class"])->order(['id' => 'ASC'])->hydrate(false)->toArray();
		$mem_classes = array();
		foreach ($member_classes as $mc) {
			$mem_classes[] = $mc["assign_class"];
		}
	
		$this->set("member_class", $mem_classes);
		if ($session["id"] != $data["id"] && $session["role_name"] != 'administrator') {
			echo $this->Flash->error("No sneaking around! ;( ");
			return $this->redirect(["action" => "memberList"]);
		}
	
		$this->set("data", $data);
		$staff = $this->GymMember->find("list", ["keyField" => "id", "valueField" => ["name"]])->where(["role_name" => "staff_member"]);
		$staff = $staff->select(["id", "name" => $staff->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
	
		$groups = $this->GymMember->GymGroup->find("list", ["keyField" => "id", "valueField" => "name"]);
		$interest = $this->GymMember->GymInterestArea->find("list", ["keyField" => "id", "valueField" => "interest"]);
		$source = $this->GymMember->GymSource->find("list", ["keyField" => "id", "valueField" => "source_name"]);
		$membership = $this->GymMember->Membership->find("list", ["keyField" => "id", "valueField" => "membership_label"]);
	
		$this->set("staff", $staff);
		$this->set("classes", $classes);
		$this->set("groups", $groups);
		$this->set("interest", $interest);
		$this->set("source", $source);
		$this->set("membership", $membership);
		$this->set("referrer_by", $staff);
	
		if ($this->request->is("post")) {
			$row = $this->GymMember->get($id);
			$ext = $this->GYMFunction->check_valid_extension($this->request->data['image']['name']);
			if ($ext != 0) {
				$image = $this->GYMFunction->uploadImage($this->request->data['image']);
				if ($image != "") {
					$this->request->data['image'] = $image;
					unlink(WWW_ROOT . "/upload/" . $data['image']);
				} else {
					unset($this->request->data['image']);
				}
	
				$this->request->data['birth_date'] = $this->GYMFunction->get_db_format_date($this->request->data['birth_date']);
				$this->request->data['inquiry_date'] = (($this->request->data['inquiry_date'] != '') ? $this->GYMFunction->get_db_format_date($this->request->data['inquiry_date']) : '');
				$this->request->data['trial_end_date'] = (($this->request->data['trial_end_date'] != '') ? $this->GYMFunction->get_db_format_date($this->request->data['trial_end_date']) : '');
				
				if (isset($this->request->data['membership_valid_from'])) {
					$this->request->data['membership_valid_from'] = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_from']);
					$this->request->data['alert_sent'] = 0;
					$this->request->data['admin_alert'] = 0;
				}
				
				if (isset($this->request->data['membership_valid_to'])) {
					$this->request->data['membership_valid_to'] = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_to']);
				}
				
				if ($this->request->data['first_pay_date'] != '') {
					$this->request->data['first_pay_date'] = $this->GYMFunction->get_db_format_date($this->request->data['first_pay_date']);
				}
				
				// Asegurarse de que assigned_tags sea un valor entero
				if (empty($this->request->data['assigned_tags'])) {
					$this->request->data['assigned_tags'] = null; // Permitir null si no se selecciona nada
				} else {
					// Convertir a entero si es string
					$this->request->data['assigned_tags'] = (int)$this->request->data['assigned_tags'];
				}
				
				// Forzar el valor de assigned_tags
				$this->request->data['assign_group'] = json_encode($this->request->data['assign_group']);
				$update = $this->GymMember->patchEntity($row, $this->request->data);
				
				// Forzar el valor de assigned_tags en la entidad
				$update->assigned_tags = (int)$this->request->data['assigned_tags'];
				
				if ($this->GymMember->save($update)) {
					// Asegurarse de que se guardó correctamente con SQL directo
					$connection = \Cake\Datasource\ConnectionManager::get('default');
					$connection->execute(
						"UPDATE gym_member SET assigned_tags = :tag WHERE id = :id", 
						['tag' => (int)$this->request->data['assigned_tags'], 'id' => $id]
					);
					
					$this->Flash->success(__("Success! Record Updated Successfully."));
					$this->GymMember->GymMemberClass->deleteAll(["member_id" => $id]);
					
					foreach ($this->request->data["assign_class"] as $class) {
						$data = array();
						$new_row = $this->GymMember->GymMemberClass->newEntity();
						$data["member_id"] = $id;
						$data["assign_class"] = $class;
						$new_row = $this->GymMember->GymMemberClass->patchEntity($new_row, $data);
						$this->GymMember->GymMemberClass->save($new_row);
					}
					
					return $this->redirect(["action" => "memberList"]);
				} else {
					if ($update->errors()) {
						foreach ($update->errors() as $error) {
							foreach ($error as $key => $value) {
								$this->Flash->error(__($value));
							}
						}
					}
				}
			} else {
				$this->Flash->error(__("Invalid File Extension, Please Retry."));
				return $this->redirect(["action" => "editMember", $id]);
			}
		}
		
		// Renderizar la vista después de procesar todo
		$this->render("addMember");
	}
	// public function editMember($id)
	// {
	// 	$this->set("edit", true);
	// 	$this->set("title", __("Edit Member"));
	// 	$this->set("eid", $id);
	// 	$membership_classes_id = array();
	// 	$session = $this->request->session()->read("User");
	// 	$data = $this->GymMember->get($id)->toArray();

	// 	$tagsTable = TableRegistry::getTableLocator()->get('MemberTags');
	// 	$tags = $tagsTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();
	
	// 	$this->set('tags', $tags);

	// 	$membership_classes = $this->GymMember->Membership->find()->where(["id" => $data['selected_membership']])->select(["membership_class"])->hydrate(false)->toArray();

	// 	//$membership_classes = (json_decode($membership_classes[0]["membership_class"])); /*ERROR IN NEW PHP 5.7 VERSION */
	// 	/* if(!empty($membership_classes)) FOR PHP 5.7 But NOT WORKNIG
	// 	{
	// 		$membership_classes = $membership_classes[0]["membership_class"];
	// 		$membership_classes = str_ireplace(array("[","]","'"),"",$membership_classes);
	// 		$membership_classes = explode(",",$membership_classes);	
	// 		$classes = $this->GymMember->ClassSchedule->find("list",["keyField"=>"id","valueField"=>"class_name"])->where(["id IN"=>$membership_classes])->toArray();

	// 	}
	// 	else{
	// 		$classes = array();
	// 	} */
	// 	foreach ($membership_classes as $value) {

	// 		$membership_classes_id[] = $value['membership_class'];
	// 	}

	// 	if (!empty($membership_classes_id)) {
	// 		$classes = $this->GymMember->ClassSchedule->find("list", ["keyField" => "id", "valueField" => "class_name"])->where(["id IN" => $membership_classes_id]);
	// 	} else {
	// 		$classes = array();
	// 	}

	// 	$member_classes = $this->GymMember->GymMemberClass->find()->where(["member_id" => $id])->select(["assign_class"])->order(['id' => 'ASC'])->hydrate(false)->toArray();
	// 	$mem_classes = array();
	// 	foreach ($member_classes as $mc) {
	// 		$mem_classes[] = $mc["assign_class"];
	// 	}

	// 	$this->set("member_class", $mem_classes);
	// 	if ($session["id"] != $data["id"] && $session["role_name"] != 'administrator') {
	// 		echo $this->Flash->error("No sneaking around! ;( ");
	// 		return $this->redirect(["action" => "memberList"]);
	// 	}

	// 	$this->set("data", $data);
	// 	$staff = $this->GymMember->find("list", ["keyField" => "id", "valueField" => ["name"]])->where(["role_name" => "staff_member"]);
	// 	$staff = $staff->select(["id", "name" => $staff->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();

	// 	$groups = $this->GymMember->GymGroup->find("list", ["keyField" => "id", "valueField" => "name"]);
	// 	$interest = $this->GymMember->GymInterestArea->find("list", ["keyField" => "id", "valueField" => "interest"]);
	// 	$source = $this->GymMember->GymSource->find("list", ["keyField" => "id", "valueField" => "source_name"]);
	// 	$membership = $this->GymMember->Membership->find("list", ["keyField" => "id", "valueField" => "membership_label"]);

	// 	$this->set("staff", $staff);
	// 	$this->set("classes", $classes);
	// 	$this->set("groups", $groups);
	// 	$this->set("interest", $interest);
	// 	$this->set("source", $source);
	// 	$this->set("membership", $membership);
	// 	$this->set("referrer_by", $staff);

	// 	$this->render("addMember");
	// 	// dd($edit);
	// 	if ($this->request->is("post")) {
	// 		$row = $this->GymMember->get($id);
	// 		//var_dump($this->request->data['birth_date']);die;
	// 		$ext = $this->GYMFunction->check_valid_extension($this->request->data['image']['name']);
	// 		if ($ext != 0) {
	// 			$image = $this->GYMFunction->uploadImage($this->request->data['image']);
	// 			if ($image != "") {
	// 				$this->request->data['image'] = $image;
	// 				unlink(WWW_ROOT . "/upload/" . $data['image']);
	// 			} else {
	// 				unset($this->request->data['image']);
	// 			}
	// 			/* $this->request->data['image'] = $image ; */

	// 			$this->request->data['birth_date'] = $this->GYMFunction->get_db_format_date($this->request->data['birth_date']);

	// 			//$this->request->data['birth_date'] = date("Y-m-d",strtotime($this->request->data['birth_date']));

	// 			//$this->request->data['inquiry_date'] = (($this->request->data['inquiry_date'] != '')?date("Y-m-d",strtotime($this->request->data['inquiry_date'])):'');
	// 			$this->request->data['inquiry_date'] = (($this->request->data['inquiry_date'] != '') ? $this->GYMFunction->get_db_format_date($this->request->data['inquiry_date']) : '');

	// 			//echo $this->request->data['inquiry_date']; die;
	// 			$this->request->data['trial_end_date'] = (($this->request->data['trial_end_date'] != '') ? $this->GYMFunction->get_db_format_date($this->request->data['trial_end_date']) : '');
	// 			if (isset($this->request->data['membership_valid_from'])) {
	// 				//echo $this->request->data['membership_valid_from'] = date("Y-m-d",strtotime($this->request->data['membership_valid_from'])); die;
	// 				$this->request->data['membership_valid_from'] = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_from']);

	// 				$this->request->data['alert_sent'] = 0;
	// 				$this->request->data['admin_alert'] = 0;
	// 			}
	// 			if (isset($this->request->data['membership_valid_to'])) {
	// 				//$this->request->data['membership_valid_to'] = date("Y-m-d",strtotime($this->request->data['membership_valid_to']));
	// 				$this->request->data['membership_valid_to'] = $this->GYMFunction->get_db_format_date($this->request->data['membership_valid_to']);
	// 			}
	// 			//$this->request->data['first_pay_date'] = date("Y-m-d",strtotime($this->request->data['first_pay_date']));
	// 			if ($this->request->data['first_pay_date'] != '') {
	// 				$this->request->data['first_pay_date'] = $this->GYMFunction->get_db_format_date($this->request->data['first_pay_date']);
	// 			}
	// 							  // En addMember() y editMember()
	// 					// Si no se selecciona ningún tag, establecer como null
	// 			if (empty($this->request->data['assigned_tags'])) {
	// 				$this->request->data['assigned_tags'] = null;
	// 			}
	// 			$update = $this->GymMember->patchEntity($row, $this->request->data);

	// 			if ($this->GymMember->save($update)) {
	// 				$this->Flash->success(__("Success! Record Updated Successfully."));
	// 				$this->GymMember->GymMemberClass->deleteAll(["member_id" => $id]);
	// 				foreach ($this->request->data["assign_class"] as $class) {
	// 					$data = array();
	// 					$new_row = $this->GymMember->GymMemberClass->newEntity();
	// 					$data["member_id"] = $id;
	// 					$data["assign_class"] = $class;
	// 					//var_dump($data);
	// 					$new_row = $this->GymMember->GymMemberClass->patchEntity($new_row, $data);
	// 					$this->GymMember->GymMemberClass->save($new_row);

	// 					//debug($data);
	// 				}
	// 				//die;
	// 				return $this->redirect(["action" => "memberList"]);
	// 			} else {
	// 				if ($update->errors()) {
	// 					foreach ($update->errors() as $error) {
	// 						foreach ($error as $key => $value) {
	// 							$this->Flash->error(__($value));
	// 						}
	// 					}
	// 				}
	// 			}
	// 		} else {
	// 			$this->Flash->error(__("Invalid File Extension, Please Retry."));
	// 			return $this->redirect(["action" => "editMember", $id]);
	// 		}
	// 	}
	// }

	public function deleteMember($id)
	{
		$row = $this->GymMember->get($id);
		if ($this->GymMember->delete($row)) {
			$this->Flash->success(__("Success! Record Deleted Successfully."));
			return $this->redirect($this->referer());
		}
	}

	public function viewMember($id)
	{
		$weight_data["data"] = $this->GYMFunction->generate_chart("Weight", $id);
		$weight_data["option"] = $this->GYMFunction->report_option("Weight");
		$this->set("weight_data", $weight_data);

		$height_data["data"] = $this->GYMFunction->generate_chart("Height", $id);
		$height_data["option"] = $this->GYMFunction->report_option("Height");
		$this->set("height_data", $height_data);

		$thigh_data["data"] = $this->GYMFunction->generate_chart("Thigh", $id);
		$thigh_data["option"] = $this->GYMFunction->report_option("Thigh");
		$this->set("thigh_data", $thigh_data);

		$chest_data["data"] = $this->GYMFunction->generate_chart("Chest", $id);
		$chest_data["option"] = $this->GYMFunction->report_option("Chest");
		$this->set("chest_data", $chest_data);

		$waist_data["data"] = $this->GYMFunction->generate_chart("Waist", $id);
		$waist_data["option"] = $this->GYMFunction->report_option("Waist");
		$this->set("waist_data", $waist_data);

		$arms_data["data"] = $this->GYMFunction->generate_chart("Arms", $id);
		$arms_data["option"] = $this->GYMFunction->report_option("Arms");
		$this->set("arms_data", $arms_data);

		$fat_data["data"] = $this->GYMFunction->generate_chart("Fat", $id);
		$fat_data["option"] = $this->GYMFunction->report_option("Fat");
		$this->set("fat_data", $fat_data);

		$photos = $this->GymMember->GymMeasurement->find()->where(["user_id" => $id])->select(["image"])->hydrate(false)->toArray();
		$this->set("photos", $photos);

		$history = $this->GymMember->MembershipPayment->find()->contain(["Membership"])->where(["MembershipPayment.member_id" => $id])->hydrate(false)->toArray();

		$this->set("history", $history);

		##########################################

		$data = $this->GymMember->find()->where(["GymMember.id" => $id])->contain(['Membership', 'GymInterestArea'])->select(["Membership.membership_label", "GymInterestArea.interest"])->select($this->GymMember)->hydrate(false)->toArray();

		$this->set("data", $data[0]);
	}

	public function viewAttendance()
	{
		$this->set("view", false);
		if ($this->request->is("post")) {
			$uid = $this->request->params["pass"][0];

			//$s_date = date("Y-m-d",strtotime($this->request->data["sdate"]));
			$s_date = $this->GYMFunction->get_db_format_date($this->request->data["sdate"]);
			$e_date = $this->GYMFunction->get_db_format_date($this->request->data["edate"]);

			$conditions = array(
				'conditions' => array(
					'and' => array(
						array(
							'attendance_date <=' => $e_date,
							'attendance_date >=' => $s_date
						),
						'user_id' => $uid
					)
				)
			);
			$data = $this->GymMember->GymAttendance->find('all', $conditions)->hydrate(false)->toArray();

			$this->set("data", $data);
			$this->set("s_date", $s_date);
			$this->set("e_date", $e_date);
			$this->set("view", true);
		}
	}

	public function activateMember($aid)
	{
		$this->autoRender = false;
		$row = $this->GymMember->get($aid);
		$member_email = $row->email;
		$member_id = $row->member_id;
		$membership_status = $row->membership_status;
		$membership_valid_from = date($this->GYMFunction->getSettings("date_format"), strtotime($row->membership_valid_from));
		$membership_valid_to = date($this->GYMFunction->getSettings("date_format"), strtotime($row->membership_valid_to));
		$membership = $this->GYMFunction->get_membership_name($row->selected_membership);

		$sys_name = $this->GYMFunction->getSettings('name');
		$sys_email = $this->GYMFunction->getSettings('email');

		$message = "Member ID: $member_id\nMembership: $membership\nMembership Status: $membership_status\nMembership Valid From: $membership_valid_from\nMembership Valid To: $membership_valid_to\n\nBest Regards\n$sys_name";

		$headers = "";
		$headers .= "From: $sys_name<$sys_email> \r\n";
		$headers .= "Reply-To: $sys_email \r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$row->activated = 1;
		if ($this->GymMember->save($row)) {
			@mail($member_email, 'Member Activated', $message, $headers);

			$this->Flash->success(__("Success! Member activated successfully."));
			return $this->redirect(["action" => "memberList"]);
		}
	}

	/* public function membershipDropped($id)
	{
		$this->autoRender = false;
		$row = $this->GymMember->get($id)->toArray();;
		$this->request->data['member_id'] = $row['member_id'];
		$this->request->data['membership_status'] = "Dropped";
		//$update = $this->GymMember->patchEntity($row,$this->request->data);
		echo "<script>alert('$id')</script>";
		
	} */

	public function export()
	{
		$this->autoRender = false;

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=user.csv');
		$output = fopen("php://output", "w");


		fputcsv($output, array('FIRST NAME', 'MIDDLE NAME', 'LAST NAME', 'EMAIL', 'GENDER', 'BIRTHDATE', 'ADDRESS', 'CITY', 'STATE', 'ZIPCODE', 'MOBILE NO.', 'USERNAME', 'MEMBERSHIP', 'MEMBERSHIP START DATE', 'MEMBERSHIP END DATE'));

		fputcsv($output, array('Alex', 'D', 'Deo', 'alex@gmail.com', 'male', '1997-05-20', '19 Sale-Heyfield Road', 'sorrento', 'Victoria', '3943', '0396630920', 'alex', 'Silver Membership', '2020-05-20', '2021-05-20'));
		/*$mem_tbl = $this->GymMember->find()->select(['member_id','first_name','middle_name','last_name','email','gender','birth_date','address','city','state','zipcode','mobile','username','selected_membership','membership_valid_from','membership_valid_to'])->where(['role_name'=>'member'])->hydrate(false)->toArray(); */

		/*foreach($mem_tbl as $data)  
		{  
			$data['selected_membership'] = $this->GYMFunction->get_membership_name($data['selected_membership']);
			fputcsv($output, $data);  
		}  */

		fclose($output);
	}

	public function import()
	{
		$this->autoRender = false;

		if ($this->request->data['import_export'] == 'export') {
			$this->autoRender = false;

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=user.csv');
			$output = fopen("php://output", "w");


			fputcsv($output, array('MEMBER ID', 'FIRST NAME', 'MIDDLE NAME', 'LAST NAME', 'EMAIL', 'GENDER', 'BIRTHDATE', 'ADDRESS', 'CITY', 'STATE', 'ZIPCODE', 'MOBILE NO.', 'USERNAME', 'MEMBERSHIP', 'ASSIGNED CLASS', 'MEMBERSHIP START DATE', 'MEMBERSHIP END DATE'));

			$mem_tbl = $this->GymMember->find()->select(['member_id', 'first_name', 'middle_name', 'last_name', 'email', 'gender', 'birth_date', 'address', 'city', 'state', 'zipcode', 'mobile', 'username', 'selected_membership', 'assign_class', 'membership_valid_from', 'membership_valid_to'])->where(['role_name' => 'member'])->hydrate(false)->toArray();

			foreach ($mem_tbl as $data) {

				$data['selected_membership'] = $this->GYMFunction->get_membership_name($data['selected_membership']);
				fputcsv($output, $data);
			}

			fclose($output);
		} else {
			$this->autoRender = false;
			$filename = $this->request->data['import']['tmp_name'];
			$img_name = $this->request->data['import']["name"];

			$ext = substr(strtolower(strrchr($img_name, '.')), 1);

			if ($ext == 'csv') {
				$count = 0;
				if ($this->request->data['import']['size'] > 0) {
					$file = fopen($filename, "r");

					while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {

						if ($count == 0) {
							$count++;
							continue;
						}
						$lastid = $this->GymMember->find("all", ["fields" => "id"])->last();
						$lastid = ($lastid != null) ? $lastid->id + 1 : 01;
						$m = date("d");
						$y = date("y");
						$prefix = "M" . $lastid;
						$member_id = $prefix . $m . $y;

						$member_ship = array('Platinum Membership' => "1", 'Gold Membership' => "2", 'Silver Membership' => "3");
						$row['activated'] = 1;
						$row['role_name'] = 'member';
						$row['member_id'] = $member_id;
						$row['first_name'] = $getData[1];
						$row['middle_name'] = $getData[2];
						$row['last_name'] = $getData[3];
						$row['member_type'] = 'Member';
						$row['gender'] = $getData[5];
						$row['birth_date'] = date('Y-m-d', strtotime($getData[6]));
						//$row['assign_class'] = 1;
						$row['address'] =  $getData[7];
						$row['city'] =  $getData[8];
						$row['state'] = $getData[9];
						$row['zipcode'] =  $getData[10];
						$row['mobile'] =  $getData[11];
						$row['email'] =  $getData[4];
						$row['username'] =  $getData[12];
						$row['password'] =  '';
						$row['image'] =  'Thumbnail-img.png';
						$row['assign_staff_mem'] =  2;
						$row['selected_membership'] = $member_ship[$getData[13]];
						$row['membership_status'] = ($getData[14] < date('Y-m-d')) ? 'Continue' : 'Prospect';
						$row['membership_valid_from'] = date('Y-m-d', strtotime($getData[15]));
						$row['membership_valid_to'] = date('Y-m-d', strtotime($getData[16]));
						$row['created_date'] = date('Y-m-d');

						$conn = ConnectionManager::get('default');
						$table_name = TableRegistry::get("gym_member");

						$sql = "INSERT INTO gym_member	(activated,role_name,member_id,first_name,middle_name,last_name,member_type,gender,birth_date,address,city,state,zipcode,mobile,email,username,password,image,assign_staff_mem,selected_membership,membership_status,membership_valid_from,membership_valid_to,created_date) VALUES('" . $row['activated'] . "','" . $row['role_name'] . "','" . $row['member_id'] . "','" . $row['first_name'] . "','" . $row['middle_name'] . "','" . $row['last_name'] . "','" . $row['member_type'] . "','" . $row['gender'] . "','" . $row['birth_date'] . "','" . $row['address'] . "','" . $row['city'] . "','" . $row['state'] . "','" . $row['zipcode'] . "','" . $row['mobile'] . "','" . $row['email'] . "','" . $row['username'] . "','" . $row['password'] . "','" . $row['image'] . "','" . $row['assign_staff_mem'] . "','" . $row['selected_membership'] . "','" . $row['membership_status'] . "','" . $row['membership_valid_from'] . "','" . $row['membership_valid_to'] . "','" . $row['created_date'] . "')";

						if ($conn->execute($sql)) {
							$count++;
							/*$member_id = $conn->id;

							/*$membership_amount = $this->GYMFunction->get_membership_amount($row["selected_membership"]);
							$sql1 = "INSERT INTO membership_payment(member_id,membership_id,membership_amount,paid_amount,start_date,end_date,membership_status,payment_status,created_date,created_by) VALUES('".$member_id."','".$row['selected_membership']."','".$membership_amount."',0,'".$row['membership_valid_from']."','".$row['membership_valid_to']."','".$row['membership_status']."',0,date('Y-m-d'),1)";

							$conn->query($sql1);*/
						} else {
							$this->Flash->error(__("File Not Import , Please Retry."));
							return $this->redirect(["action" => "memberList"]);
						}
					}
					if ($count > 0) {
						$count = $count - 1;
						$this->Flash->success(__("File Import Successfully and $count Record Generated."));
						return $this->redirect(["action" => "memberList"]);
					}
					fclose($file);
				} else {
					echo '<script>alert("File is empty")</script>';
					$this->Flash->error(__("File is empty"));
					return $this->redirect(["action" => "memberList"]);
				}
			} else {
				$this->Flash->error(__("File extension mismatch. please upload csv file"));
				return $this->redirect(["action" => "memberList"]);
			}
		}
	}
	public function isAuthorized($user)
	{
		$role_name = $user["role_name"];
		$curr_action = $this->request->action;
		$members_actions = ["viewMember", "memberList", "viewAttendance"];
		$staff_acc_actions = ["deleteTags","addMember", "memberList", "viewMember", "viewAttendance", "viewMemberTags", "addTags", "editTags"];
		$admin_actions = ["deleteTags","addMember", "memberList", "viewMember", "viewAttendance", "viewMemberTags", "addTags", "editTags"];
		$staff_actions = ["viewMemberTags", "addTags", "editTags"];
		$coach_actions = ["viewMemberTags", "addTags", "editTags"];
		switch ($role_name) {
			case "member":
				if (in_array($curr_action, $members_actions)) {
					return true;
				} else {
					return false;
				}
				break;

			case "staff_member":
				if (in_array($curr_action, $staff_acc_actions)) {
					return true;
				} else {
					return false;
				}
				break;

			case "accountant":
				if (in_array($curr_action, $staff_acc_actions)) {
					return true;
				} else {
					return false;
				}
				break;
			// case "administrator":
			// 	if (in_array($curr_action, $admin_actions)) {
			// 		return true;
			// 	} else {
			// 		return false;
			// 	}
			// 	break;
			case "coach":
				if (in_array($curr_action, $coach_actions)) {
					return true;
				} else {
					return false;
				}
				break;
		}

		return parent::isAuthorized($user);
	}
	//funciones para los tags
	public function viewMemberTags()
	{
		$this->set("title", __("View Member Tags"));

		// Obtener los tags desde la base de datos
		$tagsTable = TableRegistry::getTableLocator()->get('MemberTags');
		$tags = $tagsTable->find("all")->toArray();

		$this->set("tags", $tags);
	}

	public function addTags()
	{
		$this->set("title", __("Add Tags"));

		if ($this->request->is("post")) {
			$tagsTable = TableRegistry::getTableLocator()->get('MemberTags');
			$tag = $tagsTable->newEntity($this->request->getData());

			if ($tagsTable->save($tag)) {
				$this->Flash->success(__("Success! Tag Creado con exito."));
				return $this->redirect(["action" => "viewMemberTags"]);
			} else {
				$this->Flash->error(__("Error! Unable to add tag. Please try again."));
			}
		}
	}

	public function editTags($id = null)
	{
		$this->set("title", __("Edit Tag"));

		$tagsTable = TableRegistry::getTableLocator()->get('MemberTags');
		$tag = $tagsTable->get($id);

		if ($this->request->is(["post", "put"])) {
			$tag = $tagsTable->patchEntity($tag, $this->request->getData());

			if ($tagsTable->save($tag)) {
				$this->Flash->success(__("Success! Tag Editado con exito"));
				return $this->redirect(["action" => "viewMemberTags"]);
			} else {
				$this->Flash->error(__("Error! Unable to update tag. Please try again."));
			}
		}

		$this->set("tag", $tag);
	}
	
	public function deleteTags($id = null)
{
    $this->autoRender = false;

    $tagsTable = TableRegistry::getTableLocator()->get('MemberTags');
    $tag = $tagsTable->get($id);

    if ($tagsTable->delete($tag)) {
        $this->Flash->success(__("Success! Tag eliminado con éxito."));
    } else {
        $this->Flash->error(__("Error! No se pudo eliminar el tag. Por favor, inténtelo de nuevo."));
    }

    return $this->redirect(["action" => "viewMemberTags"]);
}
// aca termina la funcion de tags

// se agregan metodos para la cuota de mantenimiento

public function maintenanceMode()
{
    $session = $this->request->session()->read("User");
    
    // Solo administradores y staff pueden acceder
    if($session["role_name"] !== "administrator" && $session["role_name"] !== "staff_member") {
        $this->Flash->error(__("Access Denied"));
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }
    
    // Asegurarnos de que las tablas estén cargadas
    if (!isset($this->GymBranch)) {
        $this->GymBranch = TableRegistry::get('GymBranch');
    }
    
    if (!isset($this->Membership)) {
        $this->Membership = TableRegistry::get('Membership');
    }
    
    // Obtener sucursales para el filtro
    $branches = $this->GymBranch->find('list', [
        'keyField' => 'id',
        'valueField' => 'name'
    ])->where(['is_active' => true])->toArray();
    
    // Obtener membresías con cuota de mantenimiento para filtro
    $memberships = $this->Membership->find('list', [
        'keyField' => 'id',
        'valueField' => 'membership_label'
    ])->where(['maintenance_fee >' => 0])->toArray();
    
    // Iniciar la consulta base - MODIFICADA para incluir is_maintenance_mode
    $memberQuery = $this->GymMember->find()
        ->select(['id', 'member_id', 'first_name', 'last_name', 'email', 'is_maintenance_mode', 'maintenance_start_date', 'maintenance_end_date', 'selected_membership'])
        ->contain([
            'Membership' => function($q) {
                return $q->select([
                    'id', 
                    'membership_label', 
                    'maintenance_fee', 
                    'membership_amount', 
                    'branch_id'
                ])
                ->where(['maintenance_fee >' => 0])
                ->contain(['GymBranch']);
            },
            'GymBranch'
        ])
        ->where([
            'GymMember.role_name' => 'member'
        ]);
    
    // Filtro por sucursal (si se especifica) - CORREGIDO para filtrar por sucursal de la membresía
    $branch_id = null;
    if($this->request->getQuery('branch') && $this->request->getQuery('branch') != 'all') {
        $branch_id = $this->request->getQuery('branch');
        
        // Usar matching para crear un INNER JOIN con condición en la sucursal de la membresía
        $memberQuery->matching('Membership', function($q) use ($branch_id) {
            return $q->where(['Membership.branch_id' => $branch_id]);
        });
        
        $this->set('branch_id', $branch_id);
    }
    
    // Filtro por membresía (si se especifica)
    $membership_id = null;
    if($this->request->getQuery('membership') && $this->request->getQuery('membership') != 'all') {
        $membership_id = $this->request->getQuery('membership');
        $memberQuery->where(['GymMember.selected_membership' => $membership_id]);
        $this->set('membership_id', $membership_id);
    }
    
    // Filtrar por sucursal si no es administrador
    if($session["role_name"] !== "administrator" && isset($session["branch_id"])) {
        // Aquí también modificamos para filtrar por la sucursal de la membresía
        $staff_branch_id = $session["branch_id"];
        
        $memberQuery->matching('Membership', function($q) use ($staff_branch_id) {
            return $q->where(['Membership.branch_id' => $staff_branch_id]);
        });
    }
    
    // Filtrar por término de búsqueda si se proporciona
    if($this->request->is('post') && !empty($this->request->getData('search_term'))) {
        $search = $this->request->getData('search_term');
        $memberQuery->where([
            'OR' => [
                'GymMember.first_name LIKE' => '%' . $search . '%',
                'GymMember.last_name LIKE' => '%' . $search . '%',
                'GymMember.email LIKE' => '%' . $search . '%',
                'GymMember.member_id LIKE' => '%' . $search . '%'
            ]
        ]);
    }
    
    // Configurar paginación
    $this->paginate = [
        'limit' => 20,
        'order' => ['GymMember.first_name' => 'ASC']
    ];
    
    try {
        // Paginar los resultados
        $members = $this->paginate($memberQuery);
        $this->set('showPagination', true);
    } catch (\Cake\Datasource\Exception\PageOutOfBoundsException $e) {
        // Si la página solicitada no existe, volver a la primera página
        $this->request->getParam('paging')['GymMember']['page'] = 1;
        $members = $this->paginate($memberQuery);
        $this->set('showPagination', true);
    }
    
    // Pasar variables a la vista
    $this->set('members', $members);
    $this->set('branches', $branches);
    $this->set('memberships', $memberships);
}

// Añadir este método para activar/desactivar el modo mantenimiento
public function toggleMaintenanceMode($memberId)
{
    $this->autoRender = false;
    $session = $this->request->session()->read("User");
    
    // Solo administradores y staff pueden acceder
    if($session["role_name"] !== "administrator" && $session["role_name"] !== "staff_member") {
        $this->Flash->error(__("Access Denied"));
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }
    
    if($this->request->is('post')) {
        try {
            // Obtener el miembro
            $member = $this->GymMember->get($memberId);
            
            // Cargar la tabla Membership si no está cargada
            if (!isset($this->Membership)) {
                $this->Membership = TableRegistry::get('Membership');
            }
            
            // Verificación directa de la cuota de mantenimiento usando SQL para depuración
            $connection = \Cake\Datasource\ConnectionManager::get('default');
            $membership_check = $connection->execute(
                "SELECT id, membership_label, maintenance_fee FROM membership WHERE id = :id",
                ['id' => $member->selected_membership]
            )->fetch('assoc');
            
            // Registrar los resultados para depuración
            Log::write('debug', 'Verificación de cuota de mantenimiento: ' . json_encode($membership_check));
            
            $data = $this->request->getData();
            
            // Si está activando el modo mantenimiento
            if(isset($data['activate']) && $data['activate'] == 1) {
                // Inicializar parámetros básicos
                $params = [
                    'id' => $memberId,
                    'is_maintenance' => 1,
                    'start_date' => date('Y-m-d')
                ];
                
                // Iniciar construcción del SQL
                $sql = "UPDATE gym_member SET 
                        is_maintenance_mode = :is_maintenance, 
                        maintenance_start_date = :start_date";
                
                // Añadir condicionalmente fecha fin
                if (!empty($data['end_date'])) {
                    $sql .= ", maintenance_end_date = :end_date";
                    $params['end_date'] = $data['end_date'];
                } else {
                    $sql .= ", maintenance_end_date = NULL";
                }
                
                // Añadir condicionalmente notas
                if (!empty($data['notes'])) {
                    $sql .= ", maintenance_notes = :notes";
                    $params['notes'] = $data['notes'];
                } else {
                    $sql .= ", maintenance_notes = NULL";
                }
                
                $sql .= " WHERE id = :id";
                
                $connection->execute($sql, $params);
                $message = __("El miembro ha sido puesto en modo de mantenimiento correctamente.");
            } else {
                // Desactivar el modo mantenimiento con SQL directo
                $sql = "UPDATE gym_member SET 
                        is_maintenance_mode = 0, 
                        maintenance_end_date = :end_date 
                        WHERE id = :id";
                        
                $connection->execute($sql, [
                    'id' => $memberId,
                    'end_date' => date('Y-m-d')
                ]);
                
                $message = __("El modo de mantenimiento ha sido desactivado correctamente.");
            }
            
            $this->Flash->success($message);
            
        } catch (\Exception $e) {
            Log::write('error', 'Error en toggleMaintenanceMode: ' . $e->getMessage());
            $this->Flash->error(__("Error: ") . $e->getMessage());
        }
    }
    
    return $this->redirect(['action' => 'maintenanceMode']);
}
// Método auxiliar para cancelar reservas futuras
private function cancelFutureClassBookings($memberId)
{
    // Implementar lógica para cancelar reservas futuras
    $bookingTable = TableRegistry::get('class_booking');
    $today = date('Y-m-d');
    
    // Buscar reservas futuras
    $futureBookings = $bookingTable->find()
        ->where([
            'member_id' => $memberId,
            'booking_date >=' => $today
        ])->toArray();
    
    // Cancelar cada reserva
    foreach($futureBookings as $booking) {
        $booking->status = 'Cancelled';
        $booking->notes = __("Cancelado automáticamente debido a activación de modo mantenimiento");
        $bookingTable->save($booking);
    }
    
    return count($futureBookings);
}
}
