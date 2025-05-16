<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class ClientNotesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('client_notes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('GymBranch', [
            'foreignKey' => 'branch_id',
            'joinType' => 'INNER'
        ]);

        $this->hasMany('NoteDetails', [
            'foreignKey' => 'note_id',
            'dependent' => true
        ]);
    }
}