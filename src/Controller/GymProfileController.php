<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;

class GymProfileController extends AppController
{
	public function viewProfile()
	{
		$session = $this->request->session()->read("User");
		$user_data = $this->GymProfile->GymMember->get($session["id"]);
		$cover_image = $this->GymProfile->GeneralSetting->find()->select('cover_image')->hydrate(false)->toArray();
		$coverIMG = $cover_image[0]['cover_image'];
		$this->set("data", $user_data->toArray());
		$this->set("cover_image", $coverIMG);

		if ($this->request->is("post")) {
			if (isset($this->request->data["save_change"])) {
				$post = $this->request->data;
				$saved_pass = $this->GymProfile->GymMember->get($this->Auth->user('id'))->password;
				$curr_pass = (new DefaultPasswordHasher)->check($post["current_password"], $saved_pass);

				// if($post["password"] != $post["confirm_password"])
				// {
				// 	$this->Flash->error(__("Error! New password and confirm password does not matched.Please try again."));
				// }else{
				if ($this->request->data["confirm_password"] != '') {
					if ($curr_pass) {
						$this->request->data['password'] = $this->request->data["confirm_password"];
						$update_row = $this->GymProfile->GymMember->patchEntity($user_data, $this->request->data);

						if ($this->GymProfile->GymMember->save($update_row)) {
							$this->Flash->success(__("Success! Record Updated Successfully"));
							return $this->redirect(["action" => "viewProfile"]);
						}
					} else {
						$this->Flash->error(__("Error! Current password is wrong.Please try again."));
						return $this->redirect(["action" => "viewProfile"]);
					}
				} else {
					$update_row = $this->GymProfile->GymMember->patchEntity($user_data, $this->request->data);

					if ($this->GymProfile->GymMember->save($update_row)) {
						$this->Flash->success(__("Success! Record Updated Successfully"));
					}
				}
				// }


			}
			if (isset($this->request->data["profile_save_change"])) {
				$post = $this->request->data;

				$curr_email = $this->Auth->User('email');
				if ($curr_email != $post["email"]) {
					$emails = $this->GymProfile->GymMember->find("all")->where(["email" => $post["email"]]);
					$count = $emails->count();
				} else {
					$count = 0;
				}
				if ($count == 0) {

					$post['birth_date'] = date('Y-m-d', strtotime($post['birth_date']));
					$update_row = $this->GymProfile->GymMember->patchEntity($user_data, $post);

					if ($this->GymProfile->GymMember->save($update_row)) {
						$this->Flash->success(__("Success! Information Updated Successfully"));
						return $this->redirect(["action" => "viewProfile"]);
					}
				} else {
					$this->Flash->error(__("Error! Not Update.Please try again."));
				}
			}
		}
	}

	//FUNCIONES PARA LA FICHA MEDICA
	
	public function medicalRecord($id = null)
	{
		$session = $this->request->session()->read("User");
		
		// Si no se proporciona ID o el usuario no es admin, usar el ID del usuario actual
		if ($id === null || ($session["role_name"] != "administrator" && $session["id"] != $id)) {
			$user_id = $session["id"];
		} else {
			$user_id = $id;
		}
		
		// Cargar el modelo GymMedicalRecords
		$this->loadModel('GymMedicalRecords');
		
		// Buscar registro médico existente para este usuario
		$medical_data = $this->GymMedicalRecords->find()
			->where(['user_id' => $user_id])
			->first();
			
		// Si no existe registro médico, crear uno nuevo
		if (empty($medical_data)) {
			$medical_data = $this->GymMedicalRecords->newEntity();
			$medical_data->user_id = $user_id;
		}
		
		if ($this->request->is(['post', 'put'])) {
			// Asignar los datos del formulario al entity
			$medical_data = $this->GymMedicalRecords->patchEntity($medical_data, $this->request->getData());
			
			// Guardar los datos
			if ($this->GymMedicalRecords->save($medical_data)) {
				$this->Flash->success(__('La ficha médica ha sido guardada correctamente.'));
				return $this->redirect(['action' => 'medicalRecord', $user_id]);
			} else {
				$this->Flash->error(__('No se pudo guardar la ficha médica. Por favor, intente nuevamente.'));
			}
		}
		
		// Obtener datos completos del usuario
		$user_data = $this->GymProfile->GymMember->get($user_id);
		
		// Enviar datos a la vista
		$this->set('session', $session);
		$this->set('data', $user_data->toArray());
		$this->set('medical_data', $medical_data);
	}
}
