<?php
namespace App\Controller;
use Cake\App\Controller;
use Cake\Network\Session\DatabaseSession;

class StaffMembersController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent("GYMFunction");	
	}
	
	public function staffList()
	{		
		$role_name = $this->request->session()->read('Auth.User.role_name');
		$staff_id = $this->request->session()->read('Auth.User.id');
		
		if($role_name != 'staff_member'){
			$data = $this->StaffMembers->GymMember->find()
				->contain(['GymRoles', 'GymBranch'])
				->where(["GymMember.role_name"=>"staff_member"])
				->select(['GymRoles.name'])
				->select($this->StaffMembers->GymMember)
				->hydrate(false)
				->toArray();
		}else{
			$data = $this->StaffMembers->GymMember->find()
				->contain(['GymRoles', 'GymBranch'])
				->where(["GymMember.role_name"=>"staff_member","GymMember.id"=>$staff_id])
				->select(['GymRoles.name'])
				->select($this->StaffMembers->GymMember)
				->hydrate(false)
				->toArray();	
		}
		$this->set("data",$data);
	}
	
	public function addStaff()
	{
		$this->set("edit",false);
		$this->set("title",__("Add Staff Member"));
		
		$roles = $this->StaffMembers->GymMember->GymRoles->find("list",["keyField"=>"id","valueField"=>"name"])->hydrate(false)->toArray();
		$this->set("roles",$roles);

		$specialization = $this->StaffMembers->GymMember->Specialization->find("list",["keyField"=>"id","valueField"=>"name"])->hydrate(false)->toArray();
		$this->set("specialization",$specialization);		
		
		// Get list of branches
		$branches = $this->StaffMembers->GymBranch->find('list', [
			'keyField' => 'id',
			'valueField' => 'name'
		])->where(['is_active' => true])->toArray();
		$this->set('branches', $branches);
		
		if($this->request->is("post"))
		{
			$ext = $this->GYMFunction->check_valid_extension($this->request->data['image']['name']);
			if($ext != 0)
			{
				$staff = $this->StaffMembers->GymMember->newEntity();
							
				$image = $this->GYMFunction->uploadImage($this->request->data['image']);
				$this->request->data['image'] = (!empty($image)) ? $image : "Thumbnail-img.png";
				$this->request->data['birth_date'] = $this->GYMFunction->get_db_format_date($this->request->data['birth_date']); 
				$this->request->data['created_date'] = date("Y-m-d");
				$this->request->data['s_specialization'] = json_encode($this->request->data['s_specialization']);
				$this->request->data["role_name"]="staff_member";
				$this->request->data['activated']=1;
				
				// Handle branch assignments
				$branchIds = isset($this->request->data['branch_ids']) ? $this->request->data['branch_ids'] : [];
				
				$staff = $this->StaffMembers->GymMember->patchEntity($staff, $this->request->data);
		
				if($this->StaffMembers->GymMember->save($staff))
				{
					// Save branch associations
					if (!empty($branchIds)) {
						$staffBranches = [];
						foreach ($branchIds as $branchId) {
							$staffBranches[] = [
								'staff_id' => $staff->id,
								'branch_id' => $branchId
							];
						}
						$this->StaffMembers->GymBranch->connection()->insert('staff_branches', $staffBranches);
					}
					
					$this->Flash->success(__("Success! Record Successfully Saved."));
					return $this->redirect(["action"=>"staffList"]);
				}else
				{		
					if($staff->errors())
					{	
						foreach($staff->errors() as $error)
						{
							foreach($error as $key=>$value)
							{
								$this->Flash->error(__($value));
							}						
						}
					}
				}
			}else{
				$this->Flash->error(__("Invalid File Extension, Please Retry."));
				return $this->redirect(["action"=>"add-staff"]);
			}
		}
	}
	
	public function editStaff($id)
	{
		$this->set("edit",true);
		$this->set("title",__("Edit Staff Member"));
		
		// Get staff data with associated branches
		$data = $this->StaffMembers->GymMember->get($id, [
			'contain' => ['GymBranch']
		]);
		
		$roles = $this->StaffMembers->GymMember->GymRoles->find("list",["keyField"=>"id","valueField"=>"name"])->hydrate(false)->toArray();
		$specialization = $this->StaffMembers->GymMember->Specialization->find("list",["keyField"=>"id","valueField"=>"name"])->hydrate(false)->toArray();
		
		// Get list of branches
		$branches = $this->StaffMembers->GymBranch->find('list', [
			'keyField' => 'id',
			'valueField' => 'name'
		])->where(['is_active' => true])->toArray();
		
		$this->set("specialization",$specialization);
		$this->set("roles",$roles);		
		$this->set("data",$data);
		$this->set('branches', $branches);
		$this->render("AddStaff");
		
		if($this->request->is("post"))
		{
			$staff = $this->StaffMembers->GymMember->get($id);
			$old_image = $staff->image;
			
			if($this->request->data['image']['name'] == "") {
				$this->request->data['image'] = $old_image;
				$ext = 1;
			} else {
				$ext = $this->GYMFunction->check_valid_extension($this->request->data['image']['name']);
			}
			
			if($ext != 0)
			{
				if($this->request->data['image'] != $old_image) {
					$image = $this->GYMFunction->uploadImage($this->request->data['image']);
					if($image != "") {
						$this->request->data['image'] = $image;
					}
				}
				
				$this->request->data['birth_date'] = $this->GYMFunction->get_db_format_date($this->request->data['birth_date']);
				if(isset($this->request->data['s_specialization'])) {
					$this->request->data['s_specialization'] = json_encode($this->request->data['s_specialization']);
				}
				
				// Handle branch assignments
				$branchIds = isset($this->request->data['branch_ids']) ? $this->request->data['branch_ids'] : [];
				
				$staff = $this->StaffMembers->GymMember->patchEntity($staff,$this->request->data);
				if($this->StaffMembers->GymMember->save($staff))
				{
					// Update branch associations
					$this->StaffMembers->GymBranch->connection()->delete('staff_branches', ['staff_id' => $id]);
					if (!empty($branchIds)) {
						$staffBranches = [];
						foreach ($branchIds as $branchId) {
							$staffBranches[] = [
								'staff_id' => $id,
								'branch_id' => $branchId
							];
						}
						$this->StaffMembers->GymBranch->connection()->execute(
							'INSERT INTO staff_branches (staff_id, branch_id) VALUES ' . 
							implode(',', array_fill(0, count($staffBranches), '(?,?)')),
							array_reduce($staffBranches, function($carry, $item) {
								return array_merge($carry, [$item['staff_id'], $item['branch_id']]);
							}, [])
						);
					}
					
					$this->Flash->success(__("Success! Record Updated Successfully."));
					return $this->redirect(["action"=>"staffList"]);
				}else
				{
					if($staff->errors())
					{	
						foreach($staff->errors() as $error)
						{
							foreach($error as $key=>$value)
							{
								$this->Flash->error(__($value));
							}						
						}
					}
				}
			}else{
				$this->Flash->error(__("Invalid File Extension, Please Retry."));
				return $this->redirect(["action"=>"editStaff",$id]);
			}
		}
	}
	
	public function deleteStaff($id)
	{
		$row = $this->StaffMembers->GymMember->get($id);
		if($this->StaffMembers->GymMember->delete($row))
		{
			$this->Flash->success(__("Success! Staff Member Deleted Successfully."));
			return $this->redirect($this->referer());
		}
	}
	public function isAuthorized($user)
	{
		$role_name = $user["role_name"];
		$curr_action = $this->request->action;	
		$members_actions = ["staffList"];
		$staff_acc_actions = ["staffList"];
		switch($role_name)
		{			
			CASE "member":
				if(in_array($curr_action,$members_actions))
				{return true;}else{return false;}
			break;
			
			/*CASE "staff_member":
				if(in_array($curr_action,$staff_acc_actions))
				{return true;}else{ return false;}
			break;*/
			
			CASE "accountant":
				if(in_array($curr_action,$staff_acc_actions))
				{return true;}else{return false;}
			break;
		}
		
		return parent::isAuthorized($user);
	}
}