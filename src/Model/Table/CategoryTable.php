<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

Class categoryTable extends Table{
	public function initialize(array $config)
	{
		parent::initialize($config);
        
        $this->setTable('category');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        
        $this->addBehavior("Timestamp");
        
        // Agregar relación con NoteDetails
        $this->hasMany('NoteDetails', [
            'foreignKey' => 'category_id',
            'className' => 'NoteDetails'
        ]);
    }
}