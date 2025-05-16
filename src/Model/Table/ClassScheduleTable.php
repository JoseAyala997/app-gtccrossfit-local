<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ClassScheduleTable extends Table
{
	public function initialize(array $config)
	{
		$this->addBehavior('Timestamp');
		$this->belongsTo("GymMember",['foreignKey'=>"assign_staff_mem"]);
		$this->hasMany("GymNotice",["foreignKey"=>"class_id","dependent"=>true]); // it will also delete all notice for deleted class_id
		$this->belongsTo("ClassScheduleList");
		$this->belongsTo("GymMemberClass");
		$this->belongsTo("GymBranch", [
			'foreignKey' => 'branch_id',
			'joinType' => 'INNER'
		]);
	}
	
	/**
	 * Get classes available on a specific day of the week
	 * 
	 * @param string $date Date in YYYY-MM-DD format
	 * @param int $branchId Branch ID
	 * @return \Cake\ORM\Query The query object
	 */
	public function getClassesByDate($date, $branchId = null)
	{
		// Get day of week
		$dayOfWeek = date('l', strtotime($date));

		$query = $this->find('all')
			->select(['id', 'class_name', 'start_time', 'end_time', 'location', 'max_quota', 'days'])
			->contain(['GymMember' => function ($q) {
				return $q->select(['id', 'first_name', 'last_name']);
			}]);
			
		if ($branchId) {
			$query = $query->where(['class_schedule.branch_id' => $branchId]);
		}
		
		// Filter classes that are scheduled for this day of week
		$query = $query->where(function ($exp) use ($dayOfWeek) {
			return $exp->like('days', '%"' . $dayOfWeek . '"%');
		});
		
		return $query;
	}
}