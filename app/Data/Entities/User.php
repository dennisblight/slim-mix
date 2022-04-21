<?php
namespace App\Data\Entities;

use Core\Base\AbstractEntity;

class User extends AbstractEntity
{
    protected $properties = ['id', 'username', 'password_hash', 'name', 'email'];
}