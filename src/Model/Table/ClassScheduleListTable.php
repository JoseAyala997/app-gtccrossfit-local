<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ClassScheduleListTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
        
        // Define the association with ClassSchedule
        $this->belongsTo('ClassSchedule', [
            'foreignKey' => 'class_id',
            'joinType' => 'INNER'
        ]);
    }
}
