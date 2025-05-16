<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class MembershipCreditBranchesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->setTable('membership_credit_branches');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        
        $this->belongsTo('MembershipCredits', [
            'foreignKey' => 'membership_credit_id',
            'joinType' => 'INNER'
        ]);
        
        $this->belongsTo('GymBranch', [
            'foreignKey' => 'branch_id',
            'joinType' => 'INNER'
        ]);
    }
    
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create')
            ->requirePresence('membership_credit_id', 'create')
            ->notEmptyString('membership_credit_id')
            ->requirePresence('branch_id', 'create')
            ->notEmptyString('branch_id');
            
        return $validator;
    }
}
