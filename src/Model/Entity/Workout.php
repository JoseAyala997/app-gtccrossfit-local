<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Workout Entity
 *
 * @property int $id
 * @property int $program_id
 * @property \Cake\I18n\FrozenDate $date
 * @property string $visibility
 * @property \Cake\I18n\FrozenTime|null $visibility_time
 * @property string|null $description
 * @property string|null $coach_notes
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\Component[] $components
 * @property \App\Model\Entity\GymDailyWorkout[] $gym_daily_workout
 * @property \App\Model\Entity\GymWorkoutData[] $gym_workout_data
 */
class Workout extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'program_id' => true,
        'date' => true,
        'visibility' => true,
        'visibility_time' => true,
        'description' => true,
        'coach_notes' => true,
        'created' => true,
        'modified' => true,
        // 'program' => true,
        // 'components' => true,
        // 'gym_daily_workout' => true,
        // 'gym_workout_data' => true,
    ];
}
