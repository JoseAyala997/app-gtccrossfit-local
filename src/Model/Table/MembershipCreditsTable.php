<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class MembershipCreditsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->setTable('membership_credits');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        
        $this->addBehavior('Timestamp');
        
        $this->belongsTo('Membership', [
            'foreignKey' => 'membership_id',
            'joinType' => 'INNER'
        ]);
        
        $this->hasMany('MembershipCreditBranches', [
            'foreignKey' => 'membership_credit_id',
            'dependent' => true
        ]);
        
        $this->hasMany('GymMemberCredits', [
            'foreignKey' => 'membership_credit_id'
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create')
            ->requirePresence('membership_id', 'create')
            ->notEmptyString('membership_id')
            ->integer('credits')
            ->requirePresence('credits', 'create')
            ->notEmptyString('credits')
            ->greaterThanOrEqual('credits', 1, __('Credits must be at least 1'));

        return $validator;
    }
}
