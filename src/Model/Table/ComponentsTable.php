<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Components Model
 *
 * @property \App\Model\Table\WorkoutsTable&\Cake\ORM\Association\BelongsTo $Workouts
 *
 * @method \App\Model\Entity\Component get($primaryKey, $options = [])
 * @method \App\Model\Entity\Component newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Component[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Component|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Component saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Component patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Component[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Component findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ComponentsTable extends Table
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

        $this->setTable('components');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Workouts', [
            'foreignKey' => 'workout_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Activity', [
            'foreignKey' => 'activity_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Category', [
            'foreignKey' => 'category_id',
            'joinType' => 'LEFT',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('workout_id')
            ->requirePresence('workout_id', 'create')
            ->notEmptyString('workout_id', 'El campo workout_id es obligatorio.');

        $validator
            ->integer('category_id')
            ->requirePresence('category_id', 'create')
            ->notEmptyString('category_id', 'El campo category_id es obligatorio.');

        $validator
            ->integer('activity_id')
            ->requirePresence('activity_id', 'create')
            ->notEmptyString('activity_id', 'El campo activity_id es obligatorio.');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description', 'El campo descripción es obligatorio.');

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
        $rules->add($rules->existsIn(['workout_id'], 'Workouts'));

        return $rules;
    }
}
