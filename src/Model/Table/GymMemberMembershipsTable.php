<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GymMemberMembershipsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->setTable('gym_member_memberships');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        
        $this->addBehavior('Timestamp');
        
        // Definir la asociación con GymMember
        $this->belongsTo('GymMember', [
            'foreignKey' => 'member_id',
            'joinType' => 'INNER'
        ]);
        
        // Definir la asociación con Membership
        $this->belongsTo('Membership', [
            'foreignKey' => 'membership_id',
            'joinType' => 'INNER'
        ]);
        
        // Definir la asociación con GymMemberCredits
        $this->hasMany('GymMemberCredits', [
            'foreignKey' => 'gym_member_membership_id',
            'dependent' => true
        ]);
        $this->belongsTo('GymBranch', [
            'foreignKey' => 'branch_id', 
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
            
        $validator
            ->date('start_date')
            ->requirePresence('start_date', 'create')
            ->notEmptyDate('start_date');
            
        $validator
            ->date('end_date')
            ->requirePresence('end_date', 'create')
            ->notEmptyDate('end_date');
            
        $validator
            ->scalar('status')
            ->maxLength('status', 20);
            
        $validator
            ->scalar('payment_status')
            ->maxLength('payment_status', 20);

        return $validator;
    }
}