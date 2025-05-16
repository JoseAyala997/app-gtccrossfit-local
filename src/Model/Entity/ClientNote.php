<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class ClientNote extends Entity
{
    protected $_accessible = [
        'branch_id' => true,
        'user_id' => true,
        'created' => true,
        'modified' => true,
        'note_details' => true
    ];
}