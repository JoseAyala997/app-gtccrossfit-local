<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GymMedicalRecord Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $cedula
 * @property string|null $occupation
 * @property string|null $marital_status
 * @property string|null $gender
 * @property string|null $emergency_name
 * @property string|null $emergency_relation
 * @property string|null $emergency_phone
 * @property string|null $emergency_address
 * @property string|null $insurance_type
 * @property string|null $insurance_number
 * @property string|null $insurance_plan
 * @property string|null $chronic_diseases
 * @property string|null $previous_surgeries
 * @property string|null $allergies
 * @property string|null $current_medication
 * @property string|null $family_history
 * @property string|null $smoke
 * @property string|null $alcohol
 * @property string|null $physical_activity
 * @property string|null $diet
 * @property \Cake\I18n\FrozenDate|null $last_period
 * @property int|null $pregnancies
 * @property string|null $contraceptive
 * @property string|null $fitness_level
 * @property string|null $experience
 * @property string|null $injuries
 * @property string|null $limitations
 * @property string|null $goals
 * @property bool|null $consent_treatment
 * @property bool|null $consent_share_info
 * @property bool|null $confirm_true_info
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\GymMember $gym_member
 */
class GymMedicalRecord extends Entity
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
        'user_id' => true,
        'cedula' => true,
        'occupation' => true,
        'marital_status' => true,
        'gender' => true,
        'emergency_name' => true,
        'emergency_relation' => true,
        'emergency_phone' => true,
        'emergency_address' => true,
        'insurance_type' => true,
        'insurance_number' => true,
        'insurance_plan' => true,
        'chronic_diseases' => true,
        'previous_surgeries' => true,
        'allergies' => true,
        'current_medication' => true,
        'family_history' => true,
        'smoke' => true,
        'alcohol' => true,
        'physical_activity' => true,
        'diet' => true,
        'last_period' => true,
        'pregnancies' => true,
        'contraceptive' => true,
        'fitness_level' => true,
        'experience' => true,
        'injuries' => true,
        'limitations' => true,
        'goals' => true,
        'consent_treatment' => true,
        'consent_share_info' => true,
        'confirm_true_info' => true,
        'created' => true,
        'modified' => true
    ];
}