<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class NoteDetailsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('note_details');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ClientNotes', [
            'foreignKey' => 'note_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Category', [
            'foreignKey' => 'category_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Activity', [
            'foreignKey' => 'activity_id',
            'joinType' => 'LEFT'
        ]);
        $this->addBehavior('Timestamp');
    }
}