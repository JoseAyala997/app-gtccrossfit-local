<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

class GymDailyWorkoutController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent("GYMFunction");
	}

	public function workoutList()
	{
		$session = $this->request->session()->read("User");
		if($session["role_name"] == "administrator" || $session["role_name"] == "accountant")
		{
			$data = $this->GymDailyWorkout->GymMember->find("all")->where(["role_name"=>"member"])->hydrate(false)->toArray();
		}
		else if($session["role_name"] == "staff_member")
		{
			if($this->GYMFunction->getSettings("staff_can_view_own_member"))
			{			
				$data = $this->GymDailyWorkout->GymMember->find("all")->where(["role_name"=>"member","assign_staff_mem"=>$session["id"]])->hydrate(false)->toArray();
			}else{
				$data = $this->GymDailyWorkout->GymMember->find("all")->where(["role_name"=>"member"])->hydrate(false)->toArray();
			}
		}
		else if($session["role_name"] == "member")
		{
			$uid = $session["id"];
			$data = $this->GymDailyWorkout->GymMember->find("all")->where(["id"=>$uid])->hydrate(false)->toArray();
		}
		$this->set("data", $data);
	}

	public function addWorkout()
	{
		$session = $this->request->session()->read("User");
		$this->set("edit",false);
		$this->set("title",__("Add Workout"));
		
		$session = $this->request->session()->read("User");
		if($session["role_name"] == "member")
		{
			$members = $this->GymDailyWorkout->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["id"=>$session["id"]]);
			$members = $members->select(["id","name"=>$members->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
		}
		else if($session["role_name"] == "staff_member"){
			if($this->GYMFunction->getSettings("staff_can_view_own_member"))
			{	
				$members = $this->GymDailyWorkout->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"member","assign_staff_mem"=>$session["id"],"member_type"=>"Member"]);
				$members = $members->select(["id","name"=>$members->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
		
			}else{
				$members = $this->GymDailyWorkout->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"member","member_type"=>"Member"]);
				$members = $members->select(["id","name"=>$members->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
			}
		}
		else{		
			$members = $this->GymDailyWorkout->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"member","member_type"=>"Member"]);
			$members = $members->select(["id","name"=>$members->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
		}
		$this->set("members",$members);		
		//debug($this->request);
		//die;
		if($this->request->is("post") && !isset($this->request->data["new_data"]) && !isset($this->request->data["edit"]))
		{ 
			$row = $this->GymDailyWorkout->newEntity();
			$this->request->data["created_date"] = date("Y-m-d");
			$this->request->data["record_date"] = $this->GYMFunction->get_db_format_date($this->request->data['record_date']);
			$this->request->data["created_by"] = $session["id"];
			$row = $this->GymDailyWorkout->patchEntity($row,$this->request->data);
			if($this->GymDailyWorkout->save($row))
			{
				
				//$this->GymFunction->sendworkout($this->request->data["member_id"]);

				$id = $row->id;

				//$this->GymFunction->sendWorkoutAlertEmail();
				foreach($this->request->data["workouts_array"] as $val)
				{
					$user_workoutdata = array();
					$user_workoutdata['user_workout_id']=$id;
					$user_workoutdata['workout_name']=$this->request->data['workout_name_'.$val];
					$user_workoutdata['workout_name']=$this->request->data['workout_name_'.$val];
					$user_workoutdata['sets']=$this->request->data['sets_'.$val];
					$user_workoutdata['reps']=$this->request->data['reps_'.$val];
					$user_workoutdata['kg']=$this->request->data['kg_'.$val];
					$user_workoutdata['rest_time']=$this->request->data['rest_'.$val];				
					$new = $this->GymDailyWorkout->GymUserWorkout->newEntity();
					$new =  $this->GymDailyWorkout->GymUserWorkout->patchEntity($new,$user_workoutdata);	
					$chk =  $this->GymDailyWorkout->GymUserWorkout->save($new);
				}
				$this->Flash->success(__("Success! Record Saved Successfully."));
			}
			else{
				if($row->errors())
				{
					foreach($row->errors() as $error)
					{
						foreach($error as $key=>$value)
						{
							$this->Flash->error(__($value));
						}						
					}
				}
			}
		}
		else if($this->request->is("post") && isset($this->request->data["new_data"]) && !isset($this->request->data["edit"]))
		{
			$row = $this->GymDailyWorkout->newEntity();
			$this->request->data["created_date"] = date("Y-m-d");
			$this->request->data["record_date"] = $this->GYMFunction->get_db_format_date($this->request->data['record_date']);
			$this->request->data["created_by"] = $session["id"];
			$row = $this->GymDailyWorkout->patchEntity($row,$this->request->data);
			$post = $this->request->data;
			if($this->GymDailyWorkout->save($row))
			{
				//$this->GymFunction->sendworkout($this->request->data["member_id"]);
				$id = $row->id;				
				
				$activities = $post["activity_name"];
				
				foreach($activities as $activity)
				{
					$error = null;
					$data = array();
					$data["user_workout_id"] = $id;
					$data["workout_name"] = $activity;
					$data["sets"] = $post["sets_{$activity}"];
					$data["reps"] = $post["reps_{$activity}"];
					$data["kg"] = $post["kg_{$activity}"];
					$data["rest_time"] = $post["rest_{$activity}"];
					$row = $this->GymDailyWorkout->GymUserWorkout->newEntity();
					$row = $this->GymDailyWorkout->GymUserWorkout->patchEntity($row,$data);
					if($this->GymDailyWorkout->GymUserWorkout->save($row))
					{
						$error = 0;
					}else{
						$error = 1;
						}					
				}
				if($error == 0)
				{
					// $this->Flash->success(__("Success! Record Saved Successfully."));
					// return $this->redirect(["action"=>"workoutList"]);
				}				
			}
			else
			{
				if($row->errors())
				{
					foreach($row->errors() as $error)
					{
						foreach($error as $key=>$value)
						{
							$this->Flash->error(__($value));
							return $this->redirect(["action"=>"workoutList"]);
						}						
					}
				}
			}
			
			$assign_row = $this->GymDailyWorkout->GymAssignWorkout->newEntity();
			$assign_data["level_id"]= $this->request->data["level_id"];
			$assign_data["user_id"]= $this->request->data["member_id"];
			$assign_data["description"]= $this->request->data["note"];
			$assign_data["direct_assign"]= 1;
			$assign_data["start_date"]= $this->request->data["record_date"];
			$assign_data["end_date"]= $this->request->data["record_date"];
			$assign_data["created_date"]= date("Y-m-d");
			$assign_data["created_by"]= $session["id"];
			$assign_row = $this->GymDailyWorkout->GymAssignWorkout->patchEntity($assign_row,$assign_data);
			if($this->GymDailyWorkout->GymAssignWorkout->save($assign_row))
			{
				//$this->GymFunction->sendworkout($this->request->data["member_id"]);
				$id = $assign_row->id;				
				$post = $this->request->data;
				$activities = $post["activity_name"];
				foreach($activities as $activity)
				{
					$error = null;
					$data = array();
					$day_name = date("l",strtotime($post["record_date"]));
					$data["day_name"] = $day_name;
					$data["workout_id"] = $id;
					$data["workout_name"] = $activity;
					$data["sets"] = $post["sets_{$activity}"];
					$data["reps"] = $post["reps_{$activity}"];
					$data["kg"] = $post["kg_{$activity}"];
					$data["time"] = $post["rest_{$activity}"];
					$data["created_date"]= date("Y-m-d");
					$data["created_by"]= $session["id"];
					
					$row = $this->GymDailyWorkout->GymWorkoutData->newEntity();
					$row = $this->GymDailyWorkout->GymWorkoutData->patchEntity($row,$data);
					if($this->GymDailyWorkout->GymWorkoutData->save($row))
					{$error = 0;}else{$error = 1;}					
				}
				if($error == 0)
				{
					$this->Flash->success(__("Success! Record Saved Successfully."));
					return $this->redirect(["action"=>"workoutList"]);
				}
			}
						
		}
		else if($this->request->is("post") && !isset($this->request->data["new_data"]) && isset($this->request->data["edit"]) && $this->request->data["edit"] == "yes")
		{
			$post = $this->request->data;
			foreach($post["workouts_array"] as $wa)
			{
				$wn = $post["workout_name_".$wa];
				$row[$wn]["sets"] = $post["sets_{$wa}"];
				$row[$wn]["reps"] = $post["reps_{$wa}"];
				$row[$wn]["kg"] = $post["kg_{$wa}"];
				$row[$wn]["rest"] = $post["rest_{$wa}"];
				
				$query = $this->GymDailyWorkout->GymUserWorkout->query();
				//debug($post["rest_{$wa}"]);//die;
				/* $query->update()
						->set(["sets" => $post["sets_{$wa}"],"reps"=>$post["reps_{$wa}"],"kg"=>$post["kg_{$wa}"],"rest_time"=>$post["rest_{$wa}"]])
						->where(['user_workout_id' => $post["user_workout_id"],"workout_name"=>$wn])
						->execute(); */ 
				$query->update()
						->set(["sets" => $row[$wn]['sets'],"reps"=>$row[$wn]['reps'],"kg"=>$row[$wn]['kg'],"rest_time"=>$row[$wn]['rest']])
						->where(['user_workout_id' => $post["user_workout_id"],"workout_name"=>$wn])
						->execute();
				
				$query2 = $this->GymDailyWorkout->GymAssignWorkout->query();
				 //die;
				$query2->update()
						->set(["description" => $post["note"]])
						->where(['user_id' => $post["member_id"],"start_date "=>$post["record_date"]])
						->execute();
						//$this->GYMFunction->sendworkout($this->request->data["member_id"]);		

				$query3 = $this->GymDailyWorkout->query();
				$query3->update()->set(['note' => $post['note']])->where(['member_id' => $post["member_id"],"record_date "=>$post["record_date"]])->execute();
				
			}	
			$this->Flash->success(__("Success! Record Saved Successfully."));
			return $this->redirect(["action"=>"workoutList"]);
		}
	}
	
	public function addMeasurment($id = null,$type = null)
    {
		$session = $this->request->session()->read("User");
		$this->loadComponent("GYMFunction");
		if($id != null && $type != null)
		{
			$data["user_id"] = $id;
			$data["result_measurment"] = $type;
			$this->set("data",$data);			
			$this->set("set",true);			
		}else{
			$this->set("set",false);	
		}
		
		$this->set("edit",false);
		$this->set("title",__("Add Measurement"));
		if($session["role_name"] == "staff_member")
		{
			if($this->GYMFunction->getSettings("staff_can_view_own_member"))
			{
				$members = $this->GymDailyWorkout->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"member","assign_staff_mem"=>$session["id"],"member_type"=>"Member"]);
				$members = $members->select(["id","name"=>$members->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
			}
			else{
				$members = $this->GymDailyWorkout->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"member","member_type"=>"Member"]);
				$members = $members->select(["id","name"=>$members->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
			}
		}else{
				$members = $this->GymDailyWorkout->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"member","member_type"=>"Member"]);
				$members = $members->select(["id","name"=>$members->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
		}
		$this->set("members",$members);

		if($this->request->is("post"))
		{
			$ext = $this->GYMFunction->check_valid_extension($this->request->data['image']['name']);
			if($ext != 0)
			{
				$row = $this->GymDailyWorkout->GymMeasurement->newEntity();
				$image = $this->GYMFunction->uploadImage($this->request->data['image']);
				$this->request->data['image'] = (!empty($image)) ? $image : "measurement.png";
				$this->request->data["created_by"]= $session["id"];
				$this->request->data["created_date"]= date("Y-m-d");
				$this->request->data["result_date"]= $this->GYMFunction->get_db_format_date($this->request->data['result_date']);
				$row = $this->GymDailyWorkout->GymMeasurement->patchEntity($row,$this->request->data);
				if($this->GymDailyWorkout->GymMeasurement->save($row))
				{
					$this->Flash->success(__("Success! Record Saved Successfully."));
					return $this->redirect(["action"=>"workoutList"]);
				}
			}else{
				$this->Flash->error(__("Invalid File Extension, Please Retry."));
				return $this->redirect(["action"=>"add-measurment"]);
			}
		}
    }
	
	public function viewWorkout($uid)
    {		
		$member = $this->GymDailyWorkout->GymMember->get($uid)->toArray();
		$this->set("member_name",$member["first_name"]." ".$member["last_name"]);		
		
		$session = $this->request->session()->read("User");		
		if(intval($session["id"]) != intval($uid) && $session["role_name"] == 'member')
		{
			echo $this->Flash->error("No sneaking around! ;p ");
			return $this->redirect(["action"=>"workoutList"]);			
		}
		
		  ##### Gets All Schedule Assigned date ###
		$dates = $this->GymDailyWorkout->find()->select(["id","record_date"])->where(["member_id"=>$uid])->hydrate(false)->toArray();
		$date_array = array();
		foreach($dates as $date)
		{
			$wid = $date["id"];
			$date_array[]=$date["record_date"]->format("Y-m-d");
		}
		$this->set("date_array",$date_array);
		$this->set("uid",$uid);
		
		if($this->request->is("post"))
		{
			$schedule_date = $this->request->data["schedule_date"];
			$dates = '';
			$dates = $this->GymDailyWorkout->find()->select(["id","record_date"])->where(["member_id"=>$uid,'record_date'=>$schedule_date])->hydrate(false)->toArray();
			
			if(!empty($dates))
			{
				$user_workout_id = $dates[0]["id"];
			
				$workouts = $this->GymDailyWorkout->find()->select(['GymDailyWorkout.note'])->where(["GymDailyWorkout.id"=>$user_workout_id]);
				
				$workouts = $workouts->leftjoin(['GymUserWorkout'=>'gym_user_workout'],[	'GymUserWorkout.user_workout_id=GymDailyWorkout.id'])->select($this->GymDailyWorkout->GymUserWorkout)->hydrate(false)->toArray();			
				
				$this->set("workouts",$workouts);
				$this->set("schedule_date",$schedule_date);
			}
		}	
	}
	
	public function editMeasurment($id)
    {
		$this->loadComponent("GYMFunction");
		$this->set("edit",true);
		$this->set("set",false);
		$this->set("title",__("Edit Measurement"));
		
		$data = $this->GymDailyWorkout->GymMeasurement->get($id);
		$members = $this->GymDailyWorkout->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"member","member_type"=>"Member"]);
		$members = $members->select(["id","name"=>$members->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])])->hydrate(false)->toArray();
		$this->set("members",$members);		
		$this->set("data",$data->toArray());
		$this->render("addMeasurment");
		
		if($this->request->is("post"))
		{
			$ext = $this->GYMFunction->check_valid_extension($this->request->data['image']['name']);
			if($ext != 0)
			{
				$this->request->data["result_date"]= $this->GYMFunction->get_db_format_date($this->request->data['result_date']);
				$image = $this->GYMFunction->uploadImage($this->request->data['image']);
				if($image != "")
				{
					$this->request->data['image'] = $image;
				}else{
					unset($this->request->data['image']);
				}
				
				$data = $this->GymDailyWorkout->GymMeasurement->patchEntity($data,$this->request->data);
				if($this->GymDailyWorkout->GymMeasurement->save($data))
				{
					$this->Flash->success(__("Success! Record Updated Successfully."));
					return $this->redirect(["action"=>"workoutList"]);
				}
			}else{
				$this->Flash->error(__("Invalid File Extension, Please Retry."));
				return $this->redirect(["action" => "editMeasurment", $id]);
			}
		}
	}

	// FUNCIONES PARA EL CALENDAR WORKOUT
	// Función para mostrar el calendario de workouts
	public function calendarioWorkout()
	{
		$this->loadModel('Workouts');
		$this->loadModel('Components');
		$this->loadModel('Activity');
		$this->loadModel('Category');
		$this->loadModel('GymBranch');

		// Obtener la información del usuario actual
		$session = $this->request->getSession()->read("User");
		$isAdmin = ($session["role_name"] === "administrator");

		// Pasar la variable a la vista para controlar la visibilidad de los botones
		$this->set('isAdmin', $isAdmin);

		// Obtener fecha de la URL o usar la fecha actual
		$date = $this->request->getQuery('date') ? new FrozenDate($this->request->getQuery('date')) : FrozenDate::now();

		// Actualizar las variables de mes y año según la fecha seleccionada
		$mes = $date->format('m');
		$anio = $date->format('Y');
		$diaActual = $date->format('d');

		// Obtener la sucursal seleccionada (por defecto, la primera)
		$selected_branch = $this->request->getQuery('branch');

		// Si no hay sucursal seleccionada, obtener la primera
		if (!$selected_branch) {
			$first_branch = $this->GymBranch->find('all')->first();
			$selected_branch = $first_branch ? $first_branch->id : null;
		}

		// Obtener todas las sucursales para el combobox
		$branches = $this->GymBranch->find('list', [
			'keyField' => 'id',
			'valueField' => 'name'
		])->toArray();

		// Calcular el primer y último día del mes seleccionado
		$primerDia = new FrozenDate("$anio-$mes-01");
		$ultimoDia = $primerDia->copy()->modify('last day of this month');

		// Obtener todos los workouts del mes
		$query = $this->Workouts->find()
			->where([
				'date >=' => $primerDia,
				'date <=' => $ultimoDia
			]);

		// Filtrar por sucursal si está seleccionada
		if ($selected_branch) {
			$query->where(['program_id' => $selected_branch]);
		}

		// Obtener los workouts con sus relaciones
		$workouts = $query->contain([
			'Components' => [
				'Category',
				'Activity'
			]
		])->toArray();

		// Organizar workouts por fecha
		$workoutsByDate = [];
		foreach ($workouts as $workout) {
			$fecha = $workout->date->format('Y-m-d');
			$workoutsByDate[$fecha] = $workout;
		}

		// Calcular variables para el calendario
		$diasDelMes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
		$primerDiaSemana = date('N', strtotime("$anio-$mes-01"));

		// Calcular mes anterior
		$mesAnterior = $date->copy()->modify('-1 month');
		$diasMesAnterior = cal_days_in_month(
			CAL_GREGORIAN,
			$mesAnterior->format('m'),
			$mesAnterior->format('Y')
		);

		// Pasar todas las variables necesarias a la vista
		$this->set(compact(
			'workoutsByDate',
			'branches',
			'selected_branch',
			'date',
			'mes',
			'anio',
			'diaActual',
			'diasDelMes',
			'primerDiaSemana',
			'diasMesAnterior'
		));

		$this->render('calendar_workout');
	}
	// funcion para mostrar las notas por dia para entrenadores y clientes
	public function dailyWorkout()
	{
		$this->loadModel('Workouts');
		$this->loadModel('Components');
		$this->loadModel('Activity');
		$this->loadModel('Category');
		$this->loadModel('GymBranch');

		// Obtener la información del usuario actual
		$session = $this->request->getSession()->read("User");
		$userRole = $session["role_name"];

		// Verificar si el usuario es member o staff_member
		$isMember = ($userRole === "member");
		$isStaffMember = ($userRole === "staff_member");

		// Obtener fecha de la URL o usar la fecha actual
		$date = $this->request->getQuery('date') ?
			new FrozenDate($this->request->getQuery('date')) :
			FrozenDate::now();

		// Obtener la sucursal seleccionada
		$selected_branch = $this->request->getQuery('branch');

		// Si no hay sucursal seleccionada, obtener la primera
		if (!$selected_branch) {
			$first_branch = $this->GymBranch->find('all')->first();
			$selected_branch = $first_branch ? $first_branch->id : null;
		}

		// Obtener todas las sucursales para el combobox
		$branches = $this->GymBranch->find('list', [
			'keyField' => 'id',
			'valueField' => 'name'
		])->toArray();

		// Obtener el workout del día seleccionado
		$query = $this->Workouts->find()
			->where([
				'date' => $date->format('Y-m-d')
			]);

		// Filtrar por sucursal si está seleccionada
		if ($selected_branch) {
			$query->where(['program_id' => $selected_branch]);
		}

		// Obtener el workout con sus relaciones
		$workout = $query->contain([
			'Components' => [
				'Category',
				'Activity'
			]
		])->first();

		// Variables para el calendario
		$diasSemana = [
			'Domingo',
			'Lunes',
			'Martes',
			'Miércoles',
			'Jueves',
			'Viernes',
			'Sábado'
		];

		$meses = [
			'01' => 'Enero',
			'02' => 'Febrero',
			'03' => 'Marzo',
			'04' => 'Abril',
			'05' => 'Mayo',
			'06' => 'Junio',
			'07' => 'Julio',
			'08' => 'Agosto',
			'09' => 'Septiembre',
			'10' => 'Octubre',
			'11' => 'Noviembre',
			'12' => 'Diciembre'
		];

		$this->set(compact(
			'workout',
			'branches',
			'selected_branch',
			'date',
			'diasSemana',
			'meses',
			'isMember',
			'isStaffMember'
		));

		$this->render('daily_workout');
	}
	// DESDE ACA SON LAS FUNCIONES PARA LAS NOTAS DEL CLIENTE 
	public function dailynote()
{
    $this->loadModel('ClientNotes');

    // Obtener la información del usuario actual
    $session = $this->request->getSession()->read("User");
    $userRole = $session["role_name"];
    $userId = $session["id"];

    // Verificar si el usuario es member
    $isMember = ($userRole === "member");

    // Obtener fecha de la URL o usar la fecha actual
    $date = $this->request->getQuery('date') ? 
        new FrozenDate($this->request->getQuery('date')) : 
        FrozenDate::now();

    // Variables para el calendario
    $mes = $date->format('m');
    $anio = $date->format('Y');
    $diaActual = $date->format('d');

    // Verificar si la fecha seleccionada es la actual
    $isToday = $date->format('Y-m-d') === FrozenDate::now()->format('Y-m-d');

    // Consulta para obtener las notas del día seleccionado
    $query = $this->ClientNotes->find()
        ->select([
            'ClientNotes.id',
            'ClientNotes.created',
            'NoteDetails.id',
            'NoteDetails.description',
            'NoteDetails.display_order',
            'Category.name',
            'Activity.title'
        ])
        ->join([
            'NoteDetails' => [
                'table' => 'note_details',
                'type' => 'INNER',
                'conditions' => 'NoteDetails.note_id = ClientNotes.id'
            ],
            'Category' => [
                'table' => 'category',
                'type' => 'INNER',
                'conditions' => 'Category.id = NoteDetails.category_id'
            ],
            'Activity' => [
                'table' => 'activity',
                'type' => 'LEFT',
                'conditions' => 'Activity.id = NoteDetails.activity_id'
            ]
        ])
        ->where([
            'ClientNotes.user_id' => $userId,
            'DATE(ClientNotes.created)' => $date->format('Y-m-d')
        ])
        ->order(['ClientNotes.created' => 'ASC', 'NoteDetails.display_order' => 'ASC']);

    $notes = $query->toArray();

    // Organizar notas por fecha
    $notesByDate = [];
    if (!empty($notes)) {
        $fecha = $date->format('Y-m-d');
        $notesByDate[$fecha] = (object)[
            'created' => $notes[0]->created,
            'note_details' => $notes
        ];
    }

    // Arrays para fechas en español
    $diasSemana = [
        'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'
    ];

    $meses = [
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
        '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
        '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
    ];

    // Verificar si ya existe una nota para este día
    $existingNote = $this->ClientNotes->find()
        ->where([
            'user_id' => $userId,
            'DATE(created)' => $date->format('Y-m-d')
        ])
        ->first();

    // Determinar el estado del botón
    $buttonState = [
        'canAdd' => $isToday && !$existingNote,
        'hasNote' => !empty($existingNote),
        'wrongDate' => !$isToday
    ];

    $this->set(compact(
        'notesByDate',
        'date',
        'mes',
        'anio',
        'diaActual',
        'diasSemana',
        'meses',
        'isMember',
        'buttonState'
    ));

    $this->render('daily_workout_notes');
}
	//llamar la vista para agregar notas
	public function addnota() {
		// Verificar si el usuario es miembro
		$session = $this->request->getSession()->read("User");
		if ($session["role_name"] !== "member") {
			$this->Flash->error(__('No tienes acceso a esta función.'));
			return $this->redirect(['action' => 'dailynote']);
		}
		
		$this->loadModel('Category');
		$this->loadModel('ClientNotes');
		$this->loadModel('NoteDetails');
		$this->loadModel('Activity');
		
		if ($this->request->is('post')) {
			$data = $this->request->getData();
			
			// Verificar si hay componentes para procesar
			if (isset($data['components']) && !empty($data['components'])) {
				// Iniciar transacción
				$connection = $this->ClientNotes->getConnection();
				$connection->begin();
				
				try {
					// Crear la nota principal con fecha de creación
					$noteData = [
						'branch_id' => isset($data['branch_id']) ? $data['branch_id'] : $this->request->getQuery('branch', 1),
						'user_id' => $session['id'],
						'created' => date('Y-m-d H:i:s'), // Agregar fecha de creación
						'modified' => date('Y-m-d H:i:s') // Agregar fecha de modificación
					];
					
					$clientNote = $this->ClientNotes->newEntity($noteData);
					
					if (!$this->ClientNotes->save($clientNote)) {
						throw new \Exception('Error al guardar la nota principal');
					}
					
					$noteId = $clientNote->id;
					
					// Guardar cada componente como un detalle de la nota
					$order = 1;
					foreach ($data['components'] as $componentData) {
						$detailData = [
							'note_id' => $noteId,
							'category_id' => $componentData['category_id'],
							'activity_id' => isset($componentData['activity_id']) ? $componentData['activity_id'] : null,
							'description' => $componentData['description'],
							'display_order' => $order++,
							'created' => date('Y-m-d H:i:s'), // Agregar fecha de creación
							'modified' => date('Y-m-d H:i:s') // Agregar fecha de modificación
						];
						
						$noteDetail = $this->NoteDetails->newEntity($detailData);
						
						if (!$this->NoteDetails->save($noteDetail)) {
							throw new \Exception('Error al guardar un detalle de la nota');
						}
					}
					
					// Confirmar transacción
					$connection->commit();
					
					$this->Flash->success(__('La nota ha sido guardada correctamente.'));
					return $this->redirect(['action' => 'dailynote']);
					
				} catch (\Exception $e) {
					// Rollback en caso de error
					$connection->rollback();
					$this->Flash->error(__('Error al guardar la nota: ') . $e->getMessage());
				}
			} else {
				$this->Flash->error(__('No se enviaron componentes.'));
			}
		}
		
		// Cargar categorías
		$categories = $this->Category->find('list', [
			'keyField' => 'id',
			'valueField' => 'name'
		]);
		
		// Cargar actividades con información de su categoría
		$columns = $this->Activity->getSchema()->columns();
		$query = $this->Activity->find('all');
		$fields = ['id', 'title'];
		
		if (in_array('cat_id', $columns)) {
			$fields[] = 'cat_id';
		} elseif (in_array('category_id', $columns)) {
			$fields[] = 'category_id';
		}
		
		$activities = $query->select($fields)->toArray();
		
		$this->set(compact('categories', 'activities'));
		$this->render('add_note');
	}

public function editnota()
{
    // Verificar si el usuario es miembro
    $session = $this->request->getSession()->read("User");
    if ($session["role_name"] !== "member") {
        $this->Flash->error(__('No tienes acceso a esta función.'));
        return $this->redirect(['action' => 'dailynote']);
    }

    $this->loadModel('ClientNotes');
    $this->loadModel('NoteDetails');
    $this->loadModel('Category');
    $this->loadModel('Activity');

    // Obtener la fecha de la URL
    $date = $this->request->getQuery('date') ? 
        new FrozenDate($this->request->getQuery('date')) : 
        FrozenDate::now();

    // Buscar la nota principal
    $connection = $this->ClientNotes->getConnection();
    $noteQuery = $connection->execute(
        "SELECT id 
         FROM client_notes 
         WHERE user_id = :userId 
         AND DATE(created) = :date",
        ['userId' => $session['id'], 'date' => $date->format('Y-m-d')]
    );
    $noteRow = $noteQuery->fetch('assoc');

    if (!$noteRow) {
        $this->Flash->error(__('No se encontró la nota para editar.'));
        return $this->redirect(['action' => 'dailynote']);
    }

    $noteId = $noteRow['id'];

    // Obtener los detalles de la nota
    $detailsQuery = $connection->execute(
        "SELECT 
            nd.id, 
            nd.category_id, 
            nd.activity_id, 
            nd.description, 
            nd.display_order,
            cat.name as category_name,
            act.title as activity_title
         FROM note_details nd
         INNER JOIN category cat ON cat.id = nd.category_id
         LEFT JOIN activity act ON act.id = nd.activity_id
         WHERE nd.note_id = :noteId
         ORDER BY nd.display_order ASC",
        ['noteId' => $noteId]
    );
    $noteDetails = $detailsQuery->fetchAll('assoc');

    // Estructurar los datos para la vista
	$clientNote = (object)[
        'id' => $noteId,
        'note_details' => []
    ];

    foreach ($noteDetails as $detail) {
        $clientNote->note_details[] = (object)[
            'id' => $detail['id'],
            'category_id' => $detail['category_id'],
            'activity_id' => $detail['activity_id'],
            'description' => $detail['description'],
            'display_order' => $detail['display_order'],
            'category_name' => $detail['category_name'],
            'activity_title' => $detail['activity_title']
        ];
    }

    if ($this->request->is(['patch', 'post', 'put'])) {
        $data = $this->request->getData();

        if (isset($data['components']) && !empty($data['components'])) {
            $connection->begin();

            try {
                // Eliminar detalles anteriores
                $connection->execute(
                    "DELETE FROM note_details WHERE note_id = :noteId",
                    ['noteId' => $noteId]
                );

                // Guardar nuevos detalles
                $order = 1;
                foreach ($data['components'] as $componentData) {
                    $connection->execute(
                        "INSERT INTO note_details 
                         (note_id, category_id, activity_id, description, display_order, created, modified)
                         VALUES (:noteId, :categoryId, :activityId, :description, :order, NOW(), NOW())",
                        [
                            'noteId' => $noteId,
                            'categoryId' => $componentData['category_id'],
                            'activityId' => !empty($componentData['activity_id']) ? $componentData['activity_id'] : null,
                            'description' => $componentData['description'],
                            'order' => $order++
                        ]
                    );
                }

                $connection->commit();
                $this->Flash->success(__('La nota ha sido actualizada.'));
                return $this->redirect(['action' => 'dailynote']);

            } catch (\Exception $e) {
                $connection->rollback();
                $this->Flash->error(__('Error al actualizar la nota: ') . $e->getMessage());
            }
        }
    }

    // Cargar categorías
    $categories = $this->Category->find('list', [
        'keyField' => 'id',
        'valueField' => 'name'
    ])->toArray();

    // Cargar actividades
    $activities = $this->Activity->find('list', [
        'keyField' => 'id',
        'valueField' => 'title'
    ])->toArray();

    $this->set(compact('clientNote', 'categories', 'activities'));
    $this->render('edit_note');
}
	//FIN DE FUNCIONES PARA LAS NOTAS DE CLIENTES

	// funcion para eliminar las notas
	public function deleteWorkout($id = null)
	{
		// Verificar si el usuario es administrador
		$session = $this->request->getSession()->read("User");
		if ($session["role_name"] !== "administrator") {
			$this->Flash->error(__('No tienes acceso a esta función.'));
			return $this->redirect(['action' => 'calendarioWorkout']);
		}
		$this->request->allowMethod(['post', 'delete', 'get']);
		$this->loadModel('Workouts');
		$this->loadModel('Components');

		try {
			// Iniciar una transacción
			$connection = $this->Workouts->getConnection();
			$connection->begin();

			// Obtener el workout y sus componentes
			$workout = $this->Workouts->get($id, [
				'contain' => ['Components']
			]);

			// Primero eliminar todos los componentes relacionados
			if (!empty($workout->components)) {
				foreach ($workout->components as $component) {
					if (!$this->Components->delete($component)) {
						throw new \Exception('No se pudieron eliminar los componentes.');
					}
				}
			}

			// Luego eliminar el workout
			if (!$this->Workouts->delete($workout)) {
				throw new \Exception('No se pudo eliminar el workout.');
			}

			// Si todo salió bien, confirmar la transacción
			$connection->commit();
			$this->Flash->success(__('El workout y sus componentes han sido eliminados.'));
		} catch (\Exception $e) {
			// Si algo salió mal, revertir la transacción
			$connection->rollback();
			$this->Flash->error(__('Ocurrió un error al eliminar el workout: ' . $e->getMessage()));
		}

		return $this->redirect(['action' => 'calendarioWorkout']);
	}

	//llamar la vista para agregar notas
	public function addCalendarWorkout()
	{
		// Verificar si el usuario es administrador
		$session = $this->request->getSession()->read("User");
		if ($session["role_name"] !== "administrator") {
			$this->Flash->error(__('No tienes acceso a esta función.'));
			return $this->redirect(['action' => 'calendarioWorkout']);
		}
		$this->loadModel('Workouts');
		$this->loadModel('Components');
		$this->loadModel('Activity');
		$this->loadModel('Category');
		$this->loadModel('GymBranch');

		if ($this->request->is('post')) {
			// Crear una nueva entidad workout con los datos del formulario
			$workout = $this->Workouts->newEntity($this->request->getData());

			// Intentar guardar el workout
			if ($this->Workouts->save($workout)) {
				// Obtener el workout_id después de guardar
				$workoutId = $workout->id;

				// Verificar que el workout_id se haya asignado correctamente
				if (!$workoutId) {
					$this->Flash->error(__('Error: No se generó el ID del workout.'));
					return $this->redirect(['action' => 'addCalendarWorkout']);
				}

				// Obtener los datos de los componentes
				$componentsData = $this->request->getData('components');

				// Verificar si se enviaron componentes
				if (!empty($componentsData)) {
					$allComponentsSaved = true;  // Bandera para verificar si todos los componentes fueron guardados correctamente

					// Guardar cada componente
					foreach ($componentsData as $componentData) {
						// Agregar el workout_id antes de guardar el componente
						$componentData['workout_id'] = $workoutId;

						// Crear una nueva entidad para el componente
						$component = $this->Components->newEntity($componentData);

						// Intentar guardar el componente
						if (!$this->Components->save($component)) {
							// Si ocurre un error, muestra los errores de validación
							// debug($component->getErrors());
							$this->Flash->error(__('Error al guardar un componente.', $component->getErrors()));
							$allComponentsSaved = false;  // Si al menos un componente no se guardó correctamente
						}
					}

					// Verificar si todos los componentes se guardaron correctamente
					if ($allComponentsSaved) {
						$this->Flash->success(__('El workout y los componentes han sido guardados.'));
					} else {
						// En caso de que no todos los componentes se hayan guardado, mostramos un mensaje de error
						$this->Flash->error(__('Algunos componentes no pudieron ser guardados.'));
					}
				} else {
					$this->Flash->error(__('No se enviaron componentes.'));
				}

				// Redirigir a la lista de workouts
				return $this->redirect(['action' => 'calendarioWorkout']);
			} else {
				// Si el workout no pudo ser guardado, mostramos los errores de validación
				// debug($workout->getErrors());
				$this->Flash->error(__('No se pudo guardar el workout. Por favor, intente de nuevo.', $workout->getErrors()));
			}
		}

		// Cargar los datos necesarios para el formulario
		$programs = $this->GymBranch->find('list', ['keyField' => 'id', 'valueField' => 'name']);
		$categories = $this->Category->find('list', ['keyField' => 'id', 'valueField' => 'name']);
		$activities = $this->Activity->find('all')->toArray();
		// Obtener fechas que ya tienen workouts
		$occupiedDates = $this->Workouts->find()
			->select(['date'])
			->map(function ($row) {
				return $row->date->format('Y-m-d');
			})
			->toArray();

		$this->set(compact('programs', 'categories', 'activities', 'occupiedDates'));
		$this->render('add_calendar_workout');
	}
	//funcion para editar las notas
	public function editCalendarWorkout($id = null)
	{ // Verificar si el usuario es administrador
		$session = $this->request->getSession()->read("User");
		if ($session["role_name"] !== "administrator") {
			$this->Flash->error(__('No tienes acceso a esta función.'));
			return $this->redirect(['action' => 'calendarioWorkout']);
		}
		$this->loadModel('Workouts');
		$this->loadModel('Components');
		$this->loadModel('Activity');
		$this->loadModel('Category');
		$this->loadModel('GymBranch');

		// Obtener el workout por ID
		$workout = $this->Workouts->get($id, [
			'contain' => ['Components']  // Cargar componentes relacionados
		]);

		if ($this->request->is(['patch', 'post', 'put'])) {
			// Actualizar la entidad workout con los datos del formulario
			$this->Workouts->patchEntity($workout, $this->request->getData());

			// Intentar guardar los cambios en el workout
			if ($this->Workouts->save($workout)) {
				// Obtener los datos de los componentes
				$componentsData = $this->request->getData('components');

				// Obtener IDs de componentes existentes para este workout
				$existingComponentIds = array_map(function ($component) {
					return $component->id;
				}, $workout->components);

				// IDs de componentes en el formulario actual
				$formComponentIds = [];

				// Verificar si se enviaron componentes
				if (!empty($componentsData)) {
					$allComponentsSaved = true;  // Bandera para verificar si todos los componentes fueron guardados correctamente

					// Guardar cada componente
					foreach ($componentsData as $componentData) {
						// Si el componente tiene ID, es una actualización
						if (!empty($componentData['id'])) {
							$component = $this->Components->get($componentData['id']);
							$this->Components->patchEntity($component, $componentData);
							$formComponentIds[] = $componentData['id'];
						} else {
							// Agregar el workout_id antes de guardar el componente nuevo
							$componentData['workout_id'] = $id;
							// Crear una nueva entidad para el componente
							$component = $this->Components->newEntity($componentData);
						}

						// Intentar guardar el componente
						if (!$this->Components->save($component)) {
							$this->Flash->error(__('Error al guardar un componente.'));
							$allComponentsSaved = false;  // Si al menos un componente no se guardó correctamente
						}
					}

					// Eliminar componentes que existían previamente pero ya no están en el formulario
					$componentsToDelete = array_diff($existingComponentIds, $formComponentIds);
					foreach ($componentsToDelete as $componentId) {
						$componentToDelete = $this->Components->get($componentId);
						if (!$this->Components->delete($componentToDelete)) {
							$this->Flash->error(__('No se pudo eliminar un componente antiguo.'));
							$allComponentsSaved = false;
						}
					}

					// Verificar si todos los componentes se guardaron correctamente
					if ($allComponentsSaved) {
						$this->Flash->success(__('El workout y los componentes han sido actualizados.'));
					} else {
						// En caso de que no todos los componentes se hayan guardado, mostramos un mensaje de error
						$this->Flash->error(__('Algunos componentes no pudieron ser actualizados.'));
					}
				} else {
					// Si no hay componentes en el formulario, eliminar todos los componentes existentes
					foreach ($existingComponentIds as $componentId) {
						$componentToDelete = $this->Components->get($componentId);
						if (!$this->Components->delete($componentToDelete)) {
							$this->Flash->error(__('No se pudo eliminar un componente antiguo.'));
						}
					}
					$this->Flash->success(__('El workout ha sido actualizado sin componentes.'));
				}

				// Redirigir a la lista de workouts
				return $this->redirect(['action' => 'calendarioWorkout']);
			} else {
				// Si el workout no pudo ser guardado, mostramos los errores de validación
				$this->Flash->error(__('No se pudo actualizar el workout. Por favor, intente de nuevo.'));
			}
		}		
    }
	
	public function isAuthorized($user)
	{
		$role_name = $user["role_name"];
		$curr_action = $this->request->action;
		// $members_actions = ["workoutList"];
		$staff_acc_actions = ["workoutList", "viewWorkout", "calendarioWorkout"];
		switch ($role_name) {
			// CASE "member":
			// if(in_array($curr_action,$members_actions))
			// {return true;}else{return false;}
			// break;

			// CASE "staff_member":
			// if(in_array($curr_action,$staff_acc_actions))
			// {return true;}else{ return false;}
			// break;

			case "accountant":
				if (in_array($curr_action, $staff_acc_actions)) {
					return true;
				} else {
					return false;
				}
				break;
		}
		return parent::isAuthorized($user);
	}
}