<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ClassBooking Entity
 *
 * @property int $booking_id
 * @property int $member_id
 * @property int $class_id
 * @property string $booking_date
 * @property string $status
 * @property string $created_at
 * @property string $full_name
 * @property string $gender
 * @property string $mobile_no
 * @property string $email
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zipcode
 */
class ClassBooking extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'member_id' => true,
        'class_id' => true,
        'booking_date' => true,
        'status' => true,
        'created_at' => true,
        'full_name' => true,
        'gender' => true,
        'mobile_no' => true,
        'email' => true,
        'address' => true,
        'city' => true,
        'state' => true,
        'zipcode' => true
    ];
}
