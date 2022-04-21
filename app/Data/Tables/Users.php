<?php
namespace App\Data\Tables;

use App\Data\Entities\User;
use Cake\ORM\Table;

class Users extends Table
{
    public function initialize(array $config): void
    {
        $this->_table = 'users';
        $this->setEntityClass(User::class);
    }
}