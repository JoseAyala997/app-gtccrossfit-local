<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GymMemberCreditsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('gym_member_credits');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('GymMemberMemberships', [
            'foreignKey' => 'gym_member_membership_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('MembershipCreditBranches', [
            'foreignKey' => 'membership_credit_id',
            'dependent' => true,
        ]);
        
        // Relación indirecta con GymBranch a través de GymMemberMemberships
        $this->hasOne('GymBranch', [
            'className' => 'GymBranch',
            'foreignKey' => 'id',
            'bindingKey' => 'branch_id',
            'joinType' => 'LEFT', // Cambiar a LEFT para incluir registros sin relación
            'through' => 'GymMemberMemberships',
        ]);

        // $this->hasOne('Membership', [
        //     'className' => 'Membership',
        //     'foreignKey' => 'id',
        //     'bindingKey' => 'membership_id',
        //     'joinType' => 'INNER',
        //     'through' => 'GymMemberMemberships',
        // ]);
        $this->belongsTo('Membership', [
            'foreignKey' => 'membership_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('CreditUsage', [
            'foreignKey' => 'gym_member_credit_id',
            'dependent' => true
        ]);
        $this->belongsTo('GymBranch', [
            'foreignKey' => 'branch_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create')
            ->integer('credits_remaining')
            ->requirePresence('credits_remaining', 'create')
            ->notEmptyString('credits_remaining')
            ->greaterThanOrEqual('credits_remaining', 0, __('Credits remaining must be greater than or equal to 0'));

        return $validator;
    }
}
