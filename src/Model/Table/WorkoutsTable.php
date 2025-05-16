<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Workouts Model
 *
 * @property \App\Model\Table\ProgramsTable&\Cake\ORM\Association\BelongsTo $Programs
 * @property \App\Model\Table\ComponentsTable&\Cake\ORM\Association\HasMany $Components
 * @property \App\Model\Table\GymDailyWorkoutTable&\Cake\ORM\Association\HasMany $GymDailyWorkout
 * @property \App\Model\Table\GymWorkoutDataTable&\Cake\ORM\Association\HasMany $GymWorkoutData
 *
 * @method \App\Model\Entity\Workout get($primaryKey, $options = [])
 * @method \App\Model\Entity\Workout newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Workout[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Workout|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Workout saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Workout patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Workout[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Workout findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class WorkoutsTable extends Table
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

        $this->setTable('workouts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Components', [
            'foreignKey' => 'workout_id',
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
            ->integer('program_id')
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'El campo programa es obligatorio.');

        $validator
            ->date('date')
            ->requirePresence('date', 'create')
            ->notEmptyDate('date', 'El campo fecha es obligatorio.');

        $validator
            ->scalar('visibility')
            ->maxLength('visibility', 255)
            ->requirePresence('visibility', 'create')
            ->notEmptyString('visibility', 'El campo visibilidad es obligatorio.');

        $validator
            ->time('visibility_time')
            ->requirePresence('visibility_time', 'create')
            ->notEmptyTime('visibility_time', 'El campo hora de visibilidad es obligatorio.');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description', 'El campo descripción es obligatorio.');

        $validator
            ->scalar('coach_notes')
            ->requirePresence('coach_notes', 'create')
            ->notEmptyString('coach_notes', 'El campo notas para el coach es obligatorio.');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
   
}
