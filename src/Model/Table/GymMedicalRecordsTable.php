<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GymMedicalRecords Model
 *
 * @property \App\Model\Table\GymMemberTable&\Cake\ORM\Association\BelongsTo $GymMember
 *
 * @method \App\Model\Entity\GymMedicalRecord get($primaryKey, $options = [])
 * @method \App\Model\Entity\GymMedicalRecord newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GymMedicalRecord[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GymMedicalRecord|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GymMedicalRecord saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GymMedicalRecord patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GymMedicalRecord[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GymMedicalRecord findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GymMedicalRecordsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('gym_medical_records');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('GymMember', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('cedula')
            ->maxLength('cedula', 30)
            ->allowEmptyString('cedula');

        $validator
            ->scalar('occupation')
            ->maxLength('occupation', 100)
            ->allowEmptyString('occupation');

        $validator
            ->scalar('marital_status')
            ->maxLength('marital_status', 20)
            ->allowEmptyString('marital_status');

        $validator
            ->scalar('gender')
            ->maxLength('gender', 10)
            ->allowEmptyString('gender');

        $validator
            ->scalar('emergency_name')
            ->maxLength('emergency_name', 100)
            ->allowEmptyString('emergency_name');

        $validator
            ->scalar('emergency_relation')
            ->maxLength('emergency_relation', 50)
            ->allowEmptyString('emergency_relation');

        $validator
            ->scalar('emergency_phone')
            ->maxLength('emergency_phone', 30)
            ->allowEmptyString('emergency_phone');

        $validator
            ->scalar('emergency_address')
            ->maxLength('emergency_address', 255)
            ->allowEmptyString('emergency_address');

        $validator
            ->scalar('insurance_type')
            ->maxLength('insurance_type', 20)
            ->allowEmptyString('insurance_type');

        $validator
            ->scalar('insurance_number')
            ->maxLength('insurance_number', 50)
            ->allowEmptyString('insurance_number');

        $validator
            ->scalar('insurance_plan')
            ->maxLength('insurance_plan', 100)
            ->allowEmptyString('insurance_plan');

        $validator
            ->allowEmptyString('chronic_diseases');

        $validator
            ->allowEmptyString('previous_surgeries');

        $validator
            ->allowEmptyString('allergies');

        $validator
            ->allowEmptyString('current_medication');

        $validator
            ->allowEmptyString('family_history');

        $validator
            ->scalar('smoke')
            ->maxLength('smoke', 15)
            ->allowEmptyString('smoke');

        $validator
            ->scalar('alcohol')
            ->maxLength('alcohol', 15)
            ->allowEmptyString('alcohol');

        $validator
            ->scalar('physical_activity')
            ->maxLength('physical_activity', 20)
            ->allowEmptyString('physical_activity');

        $validator
            ->scalar('diet')
            ->maxLength('diet', 20)
            ->allowEmptyString('diet');

        $validator
            ->date('last_period')
            ->allowEmptyDate('last_period');

        $validator
            ->integer('pregnancies')
            ->allowEmptyString('pregnancies');

        $validator
            ->scalar('contraceptive')
            ->maxLength('contraceptive', 100)
            ->allowEmptyString('contraceptive');

        $validator
            ->scalar('fitness_level')
            ->maxLength('fitness_level', 20)
            ->allowEmptyString('fitness_level');

        $validator
            ->scalar('experience')
            ->maxLength('experience', 20)
            ->allowEmptyString('experience');

        $validator
            ->allowEmptyString('injuries');

        $validator
            ->allowEmptyString('limitations');

        $validator
            ->allowEmptyString('goals');

        $validator
            ->boolean('consent_treatment')
            ->allowEmptyString('consent_treatment');

        $validator
            ->boolean('consent_share_info')
            ->allowEmptyString('consent_share_info');

        $validator
            ->boolean('confirm_true_info')
            ->notEmptyString('confirm_true_info', 'Debe confirmar que la información es verdadera.');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'GymMember'));

        return $rules;
    }
}