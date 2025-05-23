<?php
namespace App\Model\Table;
use Cake\ORM\Table;
/* use Cake\Validation\Validator; */
use Cake\ORM\RulesChecker;
use Cake\ORM\Rule\IsUnique;

Class GymMemberTable extends Table{
	
	public function initialize(array $config)
	{
		$this->addBehavior('Timestamp');
		//relacion con la tabla member_tags
		$this->belongsTo("MemberTags", [
            "foreignKey" => "assigned_tags",
            "joinType" => "LEFT"
        ]);
		// $this->belongsTo("StaffMembers",["foreignKey"=>"assign_staff_mem"]);
		$this->belongsTo("ClassSchedule",["foreignKey"=>"assign_class"]);
		$this->belongsTo("GymGroup",["foreignKey"=>"assign_group"]);
		$this->belongsTo("GymInterestArea",["foreignKey"=>"intrested_area"]);
		$this->belongsTo("GymSource",["foreignKey"=>"g_source"]);
		$this->belongsTo("Membership",["foreignKey"=>"selected_membership"]);
		$this->belongsTo("MembershipHistory");
		$this->belongsTo("MembershipPayment");
		$this->belongsTo("GymAttendance");
		$this->belongsTo("GymMeasurement");
		$this->belongsTo("GymMemberClass",["targetForeignKey"=>"member_id"]);
		/* // $this->belongsTo("GymMemberClass"); */
		$this->BelongsTo("GymRoles",["foreignKey"=>"role"]); //for staffmember
		$this->BelongsTo("Specialization",["propertyName"=>"specialization"]); //for staffmember
		$this->BelongsToMany('GymBranch', [
			'foreignKey' => 'staff_id',
			'targetForeignKey' => 'branch_id',
			'joinTable' => 'staff_branches',
			'conditions' => ['GymMember.role_name' => 'staff_member']
		]);
		$this->hasMany('GymMemberMemberships', [
			'foreignKey' => 'member_id',
			'dependent' => true
		]);
	}
	
	public function buildRules(RulesChecker $rules)
	{
		$rules->add($rules->isUnique(['email'],'Email-id already in use.'));
		$rules->add($rules->isUnique(['username'],'Username already in use.')); /*  MOVED TO LOGIN TABLE - REMOVE */ 
		return $rules;
	} 
}