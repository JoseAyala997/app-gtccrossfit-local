<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Component Entity
 *
 * @property int $id
 * @property int $workout_id
 * @property int $category_id
 * @property int $activity_id
 * @property string|null $description
 * @property bool $rx
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Workout $workout
 */
class Component extends Entity
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
        'workout_id' => true,
        'category_id' => true,
        'activity_id' => true,
        'description' => true,
        'rx' => true,
        'created' => true,
        'modified' => true,
        'workout' => true,
    ];
}