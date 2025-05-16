<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class ClassBookingTable extends Table
{
	protected $_accessible = [
		'*' => true,
		'booking_id' => false
	];
	public function initialize(array $config)
	{
		$this->addBehavior("Timestamp");
		$this->primaryKey('booking_id');
		$this->belongsTo("ClassSchedule", [
			'foreignKey' => 'class_id',
		]);
		$this->belongsTo("ClassScheduleList");
		$this->belongsTo("GymMember", [
			'foreignKey' => 'member_id'
		]);
		
		// Set default field accessibility
		$this->schema()->columnType('member_id', 'integer');
	}
	
	/**
	 * Calculate available spots for a class on a specific date
	 * 
	 * @param int $classId The class schedule ID
	 * @param string $date The booking date (Y-m-d format)
	 * @return array ['available' => int, 'total' => int, 'booked' => int]
	 */
	public function getAvailableSpots($classId, $date)
	{
		// Get the class schedule with quota information
		$classScheduleTable = TableRegistry::getTableLocator()->get('ClassSchedule');
		$classInfo = $classScheduleTable->find()
			->select(['max_quota'])
			->where(['id' => $classId])
			->first();
			
		if (!$classInfo) {
			return [
				'available' => 0,
				'total' => 0,
				'booked' => 0
			];
		}
		
		// Count current bookings for this class on the specified date
		$bookedCount = $this->find()
			->where([
				'class_id' => $classId,
				'booking_date' => $date,
				'status !=' => 'Cancelled'
			])
			->count();
		
		// Calculate available spots
		$totalSpots = (int)$classInfo->max_quota;
		$availableSpots = max(0, $totalSpots - $bookedCount);
		
		return [
			'available' => $availableSpots,
			'total' => $totalSpots,
			'booked' => $bookedCount
		];
	}
}