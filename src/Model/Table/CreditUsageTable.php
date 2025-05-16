<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class CreditUsageTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->setTable('credit_usage');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        
        $this->addBehavior('Timestamp');
        
        $this->belongsTo('GymMemberCredits', [
            'foreignKey' => 'gym_member_credit_id',
            'joinType' => 'INNER'
        ]);
        
        $this->belongsTo('ClassSchedule', [
            'foreignKey' => 'class_schedule_id',
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
            ->dateTime('used_at')
            ->notEmptyDateTime('used_at');

        return $validator;
    }
}
