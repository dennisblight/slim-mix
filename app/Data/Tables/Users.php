<?php
namespace App\Data\Tables;

use App\Data\Entities\User;
use Cake\ORM\Table;
use DI\Container;

class Users extends Table
{
    public function __construct(Container $container)
    {
        parent::__construct([
            // 'connection' => $container->get('db.remote'),
        ]);
    }

    public function initialize(array $config): void
    {
        $this->_table = 'users';
        $this->setEntityClass(User::class);
    }
}