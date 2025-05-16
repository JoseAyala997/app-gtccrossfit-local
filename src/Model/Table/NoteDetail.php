<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class NoteDetail extends Entity
{
    protected $_accessible = [
        'note_id' => true,
        'category_id' => true,
        'activity_id' => true,
        'description' => true,
        'display_order' => true,
        'created' => true,
        'modified' => true
    ];
}