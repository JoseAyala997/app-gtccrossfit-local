<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Membership extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        '*' => true,
        'maintenance_fee' => true,
        'id' => false,
        'branch_id' => true  // Explicitly allow branch_id
    ];

    /**
     * Ensure branch_id is always treated as an integer
     *
     * @param mixed $value The value to set
     * @return int|null
     */
    protected function _setBranchId($value)
    {
        return $value !== null ? (int)$value : null;
    }
}
