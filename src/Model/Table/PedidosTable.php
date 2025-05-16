<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class PedidosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pedidos');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('id_comercio', 'El ID del comercio es obligatorio.')
            ->notEmptyString('hash_pagopar', 'El hash de PagoPar es obligatorio.');

        return $validator;
    }
}
