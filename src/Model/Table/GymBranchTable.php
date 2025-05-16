<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GymBranchTable extends Table {
    public function initialize(array $config): void {
        parent::initialize($config);
        
        $this->setTable('gym_branch');
        $this->setPrimaryKey('id');
        
        $this->hasMany('GymMember', [
            'foreignKey' => 'branch_id',
            'dependent' => false
        ]);
        
        $this->hasMany('ClassSchedule', [
            'foreignKey' => 'branch_id',
            'dependent' => false
        ]);
        
        $this->belongsToMany('StaffMembers', [
            'className' => 'GymMember',
            'joinTable' => 'staff_branches',
            'foreignKey' => 'branch_id',
            'targetForeignKey' => 'staff_id',
            'conditions' => ['StaffMembers.role_name' => 'staff_member']
        ]);
    }
    
    public function validationDefault(Validator $validator): Validator {
        $validator
            ->notEmptyString('name', 'Branch name is required')
            ->notEmptyString('address', 'Address is required')
            ->email('email', false, 'Invalid email format')
            ->allowEmptyString('email')
            ->allowEmptyString('phone')
            ->boolean('is_active');
            
        return $validator;
    }
}
