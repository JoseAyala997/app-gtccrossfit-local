<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class MemberTags extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}