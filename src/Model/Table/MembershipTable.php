<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MembershipTable extends Table
{
	public function initialize(array $config)
	{
		parent::initialize($config);
		
		$this->setTable('membership');
		$this->setDisplayField('membership_label');
		$this->setPrimaryKey('id');
		
		$this->addBehavior('Timestamp');
		$this->belongsTo("Category");
		$this->belongsTo("Installment_Plan",[
			"foreignKey" => "install_plan_id",
			"propertyName" => 'duration'
		]);
		$this->belongsTo("Activity");			
		$this->belongsTo("ClassSchedule");
		$this->belongsTo("GymBranch", [
			"foreignKey" => "branch_id"
		]);
		$this->hasMany("Membership_Activity", [
			"foreignKey" => "membership_id"
		]);
		$this->Membership_Activity->belongsTo("Activity");
		$this->Membership_Activity->belongsTo("Category");
		$this->hasMany('MembershipCredits', [
			'foreignKey' => 'membership_id',
			'dependent' => true
		]);
		$this->hasMany('MembershipCredits', [
			'foreignKey' => 'membership_id',
			'dependent' => true,
		]);
	}
	
	public function validationDefault(Validator $validator)
	{
		$validator
			->notEmpty('membership_label', __('Please enter membership name'))
			->notEmpty('branch_id', __('Please select a branch'))
			->notEmpty('membership_length', __('Please enter membership period'))
			->notEmpty('signup_fee', __('Please enter signup fee'));
		return $validator;
	}
}