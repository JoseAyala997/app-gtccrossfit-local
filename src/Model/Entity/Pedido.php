<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Pedido extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
