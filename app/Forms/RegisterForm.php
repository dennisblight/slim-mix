<?php
namespace App\Forms;

use Core\Base\AbstractForm;

class RegisterForm extends AbstractForm
{
    public static $properties = [
        'username',
        'password',
    ];

    public function setUsername($value)
    {
        $this->data['username'] = strtolower($value);
    }
}