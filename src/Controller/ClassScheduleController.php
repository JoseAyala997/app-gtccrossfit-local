<?php
namespace App\Controller;
use App\Controller\AppController;

class ClassScheduleController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent("GYMFunction");
	}

	public function classList()
	{
		$session = $this->request->session()->read("User");
		
		// Show all classes for all users (both members and staff)
		$data = $this->ClassSchedule->find();
		$data = $data->contain(["GymMember"])
			->select([
				"ClassSchedule.id",
				"ClassSchedule.class_name",
				"ClassSchedule.assign_staff_mem",
				"ClassSchedule.start_time",
				"ClassSchedule.end_time",
				"ClassSchedule.location",
				"ClassSchedule.class_fees",
				"ClassSchedule.days",
				"GymMember.first_name",
				"GymMember.last_name"
			])
			->hydrate(false)
			->toArray();
		
		$this->set("data", $data);
	}

	public function addClass()
	{
		$this->set("edit",false);
		$this->set("title",__("Add Class Schedule"));
		$session = $this->request->session()->read("User");

		$staff = $this->ClassSchedule->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"staff_member"]);
		$staff = $staff->select(["id","name"=>$staff->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])]);
		$staff = $staff->toArray();
		$this->set("staff",$staff);
		$this->set("assistant_staff",$staff);

		$branches = $this->ClassSchedule->GymBranch->find("list",["keyField"=>"id","valueField"=>"name"])->toArray();
		$this->set("branches",$branches);

		if($this->request->is("post"))
		{
			$time_list = $this->request->data["time_list"];

			$class = $this->ClassSchedule->newEntity();
			$this->request->data['days'] = json_encode($this->request->data['days']);
			$this->request->data['start_time'] = $this->request->data['start_time'];
			$this->request->data['end_time'] = $this->request->data['end_time'];
			
			$this->request->data["created_date"] = date("Y-m-d");
			$this->request->data["created_by"] = $session["id"];

			$class = $this->ClassSchedule->patchEntity($class,$this->request->data);
			if($this->ClassSchedule->save($class))
			{
				$class_id = $class->id;
				foreach($time_list as $time)
				{
					$schedule = array();
					$time = json_decode($time);
					$schedule["class_id"] = $class_id;
					$schedule["days"] = json_encode($time[0]);
					$schedule["start_time"] = $time[1];
					$schedule["end_time"] = $time[2];
					$schedule_row = $this->ClassSchedule->ClassScheduleList->newEntity();
					$schedule_row = $this->ClassSchedule->ClassScheduleList->patchEntity($schedule_row,$schedule);
					$this->ClassSchedule->ClassScheduleList->save($schedule_row);
				}
				$this->Flash->success(__("Success! Record Saved Successfully"));

			}else{
				$this->Flash->error(__("Error! There was an error while updating,Please try again later."));
			}
			return $this->redirect(["action"=>"classList"]);
		}
	}

	public function editClass($id)
	{
		$this->set("edit",true);
		$this->set("title",__("Edit Class Schedule"));
		
		$row = $this->ClassSchedule->find()
    ->where(['id' => $id])
    ->select(['id', 'class_name', 'assign_staff_mem', 'assistant_staff_member', 'location', 'class_fees', 'branch_id', 'max_quota', 'start_time', 'end_time', 'days'])
    ->first();

		if (!$row) {
			$this->Flash->error(__('Invalid class schedule'));
			return $this->redirect(['action' => 'classList']);
		}

		$this->set("data", $row->toArray());
		
		$staff = $this->ClassSchedule->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])
			->where(["role_name"=>"staff_member"]);
		$staff = $staff->select(["id","name"=>$staff->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])]);
		$staff = $staff->toArray();
		$this->set("staff",$staff);
		$this->set("assistant_staff",$staff);

		// Get branches list
		$branches = $this->ClassSchedule->GymBranch->find("list",["keyField"=>"id","valueField"=>"name"])->toArray();
		$this->set("branches",$branches);
		
		$schedule_list = $this->ClassSchedule->ClassScheduleList->find()->where(["class_id"=>$id])->hydrate(false)->toArray();
		$this->set("schedule_list",$schedule_list);
		
		$this->render("addClass");

		if($this->request->is("post"))
		{
			$time_list = $this->request->data["time_list"];
			$class_data = $this->request->data;
			$row = $this->ClassSchedule->patchEntity($row, [
				'class_name' => $class_data['class_name'],
				'assign_staff_mem' => $class_data['assign_staff_mem'],
				'assistant_staff_member' => $class_data['assistant_staff_member'],
				'location' => $class_data['location'],
				'class_fees' => $class_data['class_fees'],
				'branch_id' => $class_data['branch_id'],
				'max_quota' => $class_data['max_quota']
			]);

			if($this->ClassSchedule->save($row))
			{
				$this->ClassSchedule->ClassScheduleList->deleteAll(["class_id"=>$id]);
				foreach($time_list as $time)
				{
					$schedule = array();
					$time = json_decode($time);
					$schedule["class_id"] = $id;
					$schedule["days"] = json_encode($time[0]);
					$schedule["start_time"] = $time[1];
					$schedule["end_time"] = $time[2];
					$schedule_row = $this->ClassSchedule->ClassScheduleList->newEntity();
					$schedule_row = $this->ClassSchedule->ClassScheduleList->patchEntity($schedule_row,$schedule);
					$this->ClassSchedule->ClassScheduleList->save($schedule_row);
				}
			}
			$this->Flash->success(__("Success! Record Updated Successfully"));
			return $this->redirect(["action"=>"classList"]);
		}
	}

	public function deleteClass($id)
	{
		$row = $this->ClassSchedule->get($id);
		if($this->ClassSchedule->delete($row))
		{
			$this->Flash->success(__("Success! Class Deleted Successfully."));
			return $this->redirect($this->referer());
		}
	}

	public function viewSchedule()
	{
		$session = $this->request->session()->read("User");
		
		// Get all class schedules from all branches with related class information
		$classScheduleListTable = \Cake\ORM\TableRegistry::get('ClassScheduleList');
		
		// Create query to get all classes without branch filtering
		$query = $classScheduleListTable->find('all')
			->contain(['ClassSchedule' => ['GymMember']]);
		
		// Explicitly remove any branch filtering that might be applied by default
		$query->where(['1 = 1']); // This ensures no default scope filtering is applied
		
		$classes = $query->hydrate(false)->toArray();
		
		$this->set("classes", $classes);
	}

	public function isAuthorized($user)
	{
		$role_name = $user["role_name"];
		$curr_action = $this->request->action;
		$members_actions = ["classList","viewSchedule"];
		$staff_acc_actions = ["classList","viewSchedule","addClass","editClass"];
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
}
