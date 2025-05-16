<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Routing\RouteBuilder;

use Cake\Routing\Router;

use Cake\ORM\TableRegistry;

use Cake\Database\Expression\QueryExpression;

use Cake\Log\Log;

class MembershipController extends AppController
{	
	public function initialize()
    {
        parent::initialize();
        
        $this->loadComponent('GYMFunction');
        $this->loadComponent('Csrf');
        $this->loadComponent('RequestHandler');
        
        $this->loadModel('Membership');
        $this->loadModel('MembershipCredits');
        $this->loadModel('MembershipCreditBranches');
        $this->loadModel('Category');
        $this->loadModel('ClassSchedule');
        $this->loadModel('GymBranch');
        $this->loadModel('Activity');
        $this->loadModel('Membership_Activity');
        $this->loadModel('Installment_Plan');
        
        $session = $this->request->session()->read("User");
        $this->set('session', $session);
	}
	
	public function membershipList()
	{
		$session = $this->request->session()->read("User");
		
		$query = $this->Membership->find()
			->contain(['GymBranch'])
			->select([
				'Membership.id',
				'Membership.branch_id',
				'Membership.membership_label',
				'Membership.membership_class',
				'Membership.membership_cat_id',
				'Membership.membership_length',
				'Membership.membership_class_limit',
				'Membership.limit_days',
				'Membership.limitation',
				'Membership.install_plan_id',
				'Membership.signup_fee',
				'Membership.membership_amount',
				'Membership.installment_amount',
				'Membership.gmgt_membershipimage',
				'GymBranch.id',
				'GymBranch.name'
			]);
			
		if($session["role_name"] !== "administrator" && isset($session["branch_id"])) {
			$query->where(['Membership.branch_id' => $session["branch_id"]]);
		}
		
		$membership_data = $query->toArray();   
		$this->set("membership_data", $membership_data);
	}
	
	public function add()
	{			
		$session = $this->request->session()->read("User");
		
		$this->set([
			"membership" => null,			
			"edit" => false,		
			"title" => __("Add Membership")
		]);
		
		// Get available branches based on role
		$branchesQuery = $this->Membership->GymBranch->find('list', [
			'keyField' => 'id',
			'valueField' => 'name'
		])->where(['is_active' => true]);
		
		if($session["role_name"] !== "administrator" && isset($session["branch_id"])) {
			$branchesQuery->where(['id' => $session["branch_id"]]);
		}
		
		// Prepare form data
		$this->set([
			'categories' => $this->Membership->Category->find("list", ["keyField" => "id", "valueField" => "name"])->toArray(),
			'classes' => $this->Membership->ClassSchedule->find("list", ["keyField" => "id", "valueField" => "class_name"])->toArray(),
			'branches' => $branchesQuery->toArray()
		]);
		
		// Get installment plan options
		$query = $this->Membership->Installment_Plan->find();
		$installment_plan = $this->Membership->Installment_Plan->find("list", [
			"keyField" => "id", 
			"valueField" => "concatenated"
		])->select([
			'id',
			'concatenated' => $query->func()->concat([
				'number' => 'literal',
				' ',
				'duration' => 'literal'
			])
		]);
		
		$this->set('installment_plan', $installment_plan->toArray());
		
		if($this->request->is("post")) {
			$data = $this->request->getData();
			$membership = $this->Membership->newEntity();
			
			if($this->GYMFunction->check_valid_extension($data['gmgt_membershipimage']['name'])) {
				$data['gmgt_membershipimage'] = $this->GYMFunction->uploadImage($data["gmgt_membershipimage"]);
				$data['created_date'] = date("Y-m-d");
				$data['membership_class'] = json_encode($data['membership_class']);

				// Set branch_id for non-admin users or use the selected branch
				if($session["role_name"] !== "administrator" && isset($session["branch_id"])) {
					$data['branch_id'] = $session["branch_id"];
				} elseif (!empty($data['branch_id'])) {
					// Keep the selected branch_id for admin users
					$data['branch_id'] = (int)$data['branch_id'];
				}
				
				$membership = $this->Membership->patchEntity($membership, $data);
				$result = $this->Membership->save($membership);
				
				if($result) {
					$updateData = ['branch_id' => $data['branch_id']];
    
					// Solo incluir maintenance_fee si es elegible
					if($this->isEligibleForMaintenanceFee($data)) {
						$maintenanceFee = isset($data['maintenance_fee']) ? floatval($data['maintenance_fee']) : 0.00;
						$updateData['maintenance_fee'] = $maintenanceFee;
					} else {
						$updateData['maintenance_fee'] = 0.00;
					}
					
					$this->Membership->updateAll(
						$updateData,
						['id' => $result->id]
					);
					
					// Handle credits if they are set
					if (!empty($data['credits'])) {
						$credits = $this->MembershipCredits->newEntity([
							'membership_id' => $membership->id,
							'credits' => $data['credits']
						]);
						
						if ($this->MembershipCredits->save($credits)) {
							// Save branch associations if any branches are selected
							if (!empty($data['credit_branches'])) {
								foreach ($data['credit_branches'] as $branchId) {
									$creditBranch = $this->MembershipCreditBranches->newEntity([
										'membership_credit_id' => $credits->id,
										'branch_id' => $branchId
									]);
									$this->MembershipCreditBranches->save($creditBranch);
								}
							}
						}
					}
					
					// Force update branch_id directly
					$this->Membership->updateAll(
						['branch_id' => $data['branch_id']],
						['id' => $result->id]
					);
					
					$this->Flash->success(__("Success! Record Saved Successfully"));
					return $this->redirect(["action" => "membershipList"]);
				}
				
				$this->Flash->error(__("Error! Failed to save record"));
				return;
			}
			
			$this->Flash->error(__("Invalid File Extension"));
			return $this->redirect(["action" => "add"]);
		}
	}

	public function editMembership($id)
	{	
		$session = $this->request->session()->read("User");
		
		$this->set([
			"edit" => true,	
			"membership" => null,
			"title" => __("Edit Membership")
		]);
		
		try {
			$membership_data = $this->Membership->get($id, [
				'contain' => ['GymBranch', 'MembershipCredits', 'MembershipCredits.MembershipCreditBranches'],
				'fields' => [
					'Membership.id',
					'Membership.branch_id',
					'Membership.membership_label',
					'Membership.membership_class',
					'Membership.membership_cat_id',
					'Membership.membership_length',
					'Membership.membership_class_limit',
					'Membership.limit_days',
					'Membership.limitation',
					'Membership.install_plan_id',
					'Membership.signup_fee',
					'Membership.membership_amount',
					'Membership.maintenance_fee',
					'Membership.installment_amount',
					'Membership.gmgt_membershipimage',
					'GymBranch.id',
					'GymBranch.name'
				]
			]);
			
			// Get available branches based on role
			$branchesQuery = $this->Membership->GymBranch->find('list', [
				'keyField' => 'id',
				'valueField' => 'name'
			])->where(['is_active' => true]);
			
			if($session["role_name"] !== "administrator" && isset($session["branch_id"])) {
				$branchesQuery->where(['id' => $session["branch_id"]]);
				
				// Verify user has access to this membership's branch
				if($membership_data->branch_id !== $session["branch_id"]) {
					$this->Flash->error(__("Access Denied"));
					return $this->redirect(["action" => "membershipList"]);
				}
			}

			// Prepare form data
			$this->set([
				'membership_data' => $membership_data,
				'membership_class' => json_decode($membership_data->membership_class),
				'categories' => $this->Membership->Category->find("list", ["keyField" => "id", "valueField" => "name"])->toArray(),
				'classes' => $this->Membership->ClassSchedule->find("list", ["keyField" => "id", "valueField" => "class_name"])->toArray(),
				'branches' => $branchesQuery->toArray()
			]);
			
			// Get installment plan options
			$query = $this->Membership->Installment_Plan->find();
			$installment_plan = $this->Membership->Installment_Plan->find("list", [
				"keyField" => "id", 
				"valueField" => "concatenated"
			])->select([
				'id',
				'concatenated' => $query->func()->concat([
					'number' => 'literal',
					' ',
					'duration' => 'literal'
				])
			]);
			
			$this->set('installment_plan', $installment_plan->toArray());
			
			if($this->request->is("post")) {
				$data = $this->request->getData();
				$data['membership_class'] = json_encode($data['membership_class']);

				if($this->GYMFunction->check_valid_extension($data['gmgt_membershipimage']['name'])) {
					if(!empty($data['gmgt_membershipimage']['name'])) {
						$data['gmgt_membershipimage'] = $this->GYMFunction->uploadImage($data["gmgt_membershipimage"]);
						
						// Delete old image
						if($membership_data->gmgt_membershipimage) {
							unlink(WWW_ROOT."/upload/".$membership_data->gmgt_membershipimage);
						}
					} else {
						unset($data['gmgt_membershipimage']);
					}
					
					// Set branch_id for non-admin users or use the selected branch
					if($session["role_name"] !== "administrator" && isset($session["branch_id"])) {
						$data['branch_id'] = $session["branch_id"];
					} elseif (!empty($data['branch_id'])) {
						// Keep the selected branch_id for admin users
						$data['branch_id'] = (int)$data['branch_id'];
					}
					
					// Now update the rest of the fields
					$membership = $this->Membership->patchEntity($membership_data, $data);
					$result = $this->Membership->save($membership);
					
					if($result) {
						$updateData = ['branch_id' => $data['branch_id']];
						// Solo incluir maintenance_fee si es elegible
						if($this->isEligibleForMaintenanceFee($data)) {
							$maintenanceFee = isset($data['maintenance_fee']) ? floatval($data['maintenance_fee']) : 0.00;
							$updateData['maintenance_fee'] = $maintenanceFee;
						} else {
							$updateData['maintenance_fee'] = 0.00;
						}
						
						$this->Membership->updateAll(
							$updateData,
							['id' => $result->id]
						);
    
						// Handle credits
						if (!empty($data['credits'])) {
							// Delete existing credits and their branch associations
							if (!empty($membership_data->membership_credits)) {
								foreach ($membership_data->membership_credits as $credit) {
									$this->MembershipCredits->delete($credit);
								}
							}
							
							// Create new credit records
							foreach ($data['credits'] as $creditData) {
								if (empty($creditData['amount'])) {
									continue;
								}
								
								$credits = $this->MembershipCredits->newEntity([
									'membership_id' => $membership->id,
									'credits' => $creditData['amount']
								]);
								
								if ($this->MembershipCredits->save($credits)) {
									// Save branch associations if any branches are selected
									if (!empty($creditData['branches'])) {
										foreach ($creditData['branches'] as $branchId) {
											// Extract the actual branch ID from the array if needed
											$actualBranchId = is_array($branchId) ? $branchId[0] : $branchId;
											
											$creditBranch = $this->MembershipCreditBranches->newEntity([
												'membership_credit_id' => $credits->id,
												'branch_id' => $actualBranchId
											]);
											$this->MembershipCreditBranches->save($creditBranch);
										}
									}
								}
							}
						}
						
						// Force update branch_id directly
						$this->Membership->updateAll(
							['branch_id' => $data['branch_id']],
							['id' => $membership_data->id]
						);

						$this->Flash->success(__("Success! Record Updated Successfully"));
						return $this->redirect(["action" => "membershipList"]);
					}
					
					// Debug validation errors if any
					Log::write('debug', 'Validation Errors: ' . print_r($membership->getErrors(), true));
					$this->Flash->error(__("Error! Failed to update record"));
					return;
				}
				
				$this->Flash->error(__("Invalid File Extension"));
				return $this->redirect(["action" => "editMembership", $id]);
			}
			
		} catch (\Exception $e) {
			$this->Flash->error(__("Membership not found"));
			return $this->redirect(["action" => "membershipList"]);
		}
		
		// Get selected branch IDs for credits
		$selectedBranches = [];
		if (!empty($membership_data->membership_credits)) {
			foreach ($membership_data->membership_credits as $credit) {
				if (!empty($credit->membership_credit_branches)) {
					foreach ($credit->membership_credit_branches as $branch) {
						$selectedBranches[] = $branch->branch_id;
					}
				}
			}
		}
		
		$this->set('selectedBranches', $selectedBranches);
		$this->render("add");
	}

	public function activateOnStripe($mid)
	{
		$this->autoRender = false;
		$stripeSecretKey = $this->GYMFunction->getGeneralSettingFieldValue("stripe_secret_key");
		if($stripeSecretKey == "YOUR SECRET KEY" || $stripeSecretKey == "")
		{
			$this->Flash->error(__("Please add stripe credentials in setting tab"));

			return $this->redirect(["action"=>"membership-list",$id]);
		}else{
			// Stripe connect
			$stripe = new \Stripe\StripeClient(
				$stripeSecretKey
			  );
			 
			$stripeProductId = $this->GYMFunction->getGeneralSettingFieldValue("stripe_product_id");
			$currency = strtolower($this->GYMFunction->getGeneralSettingFieldValue("currency"));
			//   var_dump($stripeProductId);die;
			if($stripeProductId == '' || $stripeProductId == NULL || $stripeProductId == 'null')
			{
				// Creacte product  
				$product = $stripe->products->create([
					'name' => 'Gym Membership',
				]);
				
				if(!empty($product))
				{
					// Update setting
					$settings = TableRegistry::get("GeneralSetting");
					$results = $settings->find()->all();
					$update_row = $results->first();

					$save['stripe_product_created'] = 1;
					$save['stripe_product_id'] = $product['id'];

					$update = $settings->patchEntity($update_row,$save);
					$updated = $settings->save($update);
					
				}
			}
			 // Create Plan
			 $membershipTbl = $settings = TableRegistry::get("membership");
			 $membershipRow = $membershipTbl->get($mid);
			 $membershipRowArray = $membershipRow->toArray();

			 $stripeProductId = $this->GYMFunction->getGeneralSettingFieldValue("stripe_product_id");

			$plan =  $stripe->plans->create([
				'amount' => $membershipRowArray['membership_amount'] * 100,
				'currency' => $currency,
				'interval' => 'day',
				'interval_count' => $membershipRowArray['membership_length'],
				'product' => $stripeProductId,
			  ]);
			  
			  if(!empty($plan))
			  {
				$membershipRow->activated_on_stripe = 1;
				$membershipRow->stripe_plan_id = $plan['id'];
				$membershipTbl->save($membershipRow);

				$this->Flash->success(__("Success! Membership activated on stripe successfully."));

				return $this->redirect(["action"=>"membershipList"]);
			  }
		}
	}

	public function viewActivity($mid)

	{

		$activities_list = $this->Membership->Activity->find("list",["keyField"=>"id","valueField"=>"title"]);

		$activities_list = $activities_list->toArray();

		

		$selected_activities = $this->Membership->Membership_Activity->find("list",["keyField"=>"id","valueField"=>"activity_id"])->where(["membership_id"=>$mid]);

		$selected_activities = array_values($selected_activities->toArray());

	

		$assigned_activities = $this->Membership->Membership_Activity->find("all")->where(["membership_id"=>$mid])->contain(["Activity"])->select($this->Membership->Membership_Activity);

		$assigned_activities = $assigned_activities->select(["Activity.cat_id","Activity.assigned_to"])->hydrate(false)->toArray();

		

		$this->set("activities",$activities_list);

		$this->set("selected_activities",$selected_activities);

		$this->set("assigned_activities",$assigned_activities);	



		if($this->request->is("post"))

		{

			$membership_activity = TableRegistry::get("Membership_Activity");			

			$data = $this->request->data;

			$delete_row= $membership_activity->deleteAll(["membership_id"=>$data['membership_id']]);

			$save_data = array();

			foreach($data["activity_id"] as $activity)

			{				

				$save_data[] = ["membership_id"=>$data["membership_id"],"activity_id"=>$activity,"created_date"=>date("Y-m-d")];

			}			

			$rows = $membership_activity->newEntities($save_data);

			foreach($rows as $row)

			{

				$membership_activity->save($row);

			}

			$this->Flash->Success(__("Success! Activity Successfully Assigned."));

			return $this->redirect($this->referer());

		}		

	}

	

	public function deleteActivity($id)

	{

		$row = $this->Membership->Membership_Activity->get($id);
		debug($row);
		die;
		if($this->Membership->Membership_Activity->delete($row))
		
		{
			$this->Flash->Success(__("Success! Activity Unassigned Successfully."));


			return $this->redirect($this->referer());

		}

	}

	

	public function isAuthorized($user)

	{

		$role_name = $user["role_name"];

		$curr_action = $this->request->action;	

		$members_actions = ["membershipList"];

		$staff_acc_actions = ["membershipList","add","editMembership","viewActivity","deleteActivity"];

		switch($role_name)

		{			

			CASE "member":

				if(in_array($curr_action,$members_actions))

				{return true;}else{return false;}

			break;

			

			CASE "staff_member":

				if(in_array($curr_action,$staff_acc_actions))

				{return true;}else{ return false;}

			break;

			

			CASE "accountant":

				if(in_array($curr_action,$staff_acc_actions))

				{return true;}else{return false;}

			break;

		}

		

		return parent::isAuthorized($user);

	}

	private function isEligibleForMaintenanceFee($data) {
		// Verificar si hay duración
		if (!isset($data['duration_value']) || !isset($data['duration_type'])) {
			return false;
		}
		
		$value = intval($data['duration_value']);
		$type = $data['duration_type'];
		
		// Verificar si cumple el requisito de 6 meses o más
		if ($type === 'months' && $value >= 6) {
			return true;
		} else if ($type === 'days' && $value >= 180) {
			return true;
		}
		
		return false;
	}

}