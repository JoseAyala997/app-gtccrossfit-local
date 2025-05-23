<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

Class StaffMembersTable extends Table{
	
	public function initialize(array $config)
	{
		$this->addBehavior('Timestamp');
		$this->BelongsTo("GymRoles",["foreignKey"=>"role"]);
		$this->BelongsTo("GymMember");
		$this->BelongsTo("Specialization",["propertyName"=>"specialization"]);
		$this->BelongsToMany('GymBranch', [
			'joinTable' => 'staff_branches',
			'foreignKey' => 'staff_id',
			'targetForeignKey' => 'branch_id',
		]);
	}
}