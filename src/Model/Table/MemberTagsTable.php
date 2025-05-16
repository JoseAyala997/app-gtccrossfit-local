<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class MemberTagsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->setTable('member_tags');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        
        $this->hasMany('GymMember', [
            'foreignKey' => 'assigned_tags'
        ]);
    }
    
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');
            
        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');
            
        $validator
            ->requirePresence('color', 'create')
            ->notEmpty('color');
            
        return $validator;
    }
}