<?php
namespace App\Forms;

use Core\Base\AbstractForm;

class RegisterForm extends AbstractForm
{
    protected $properties = [
        'username',
        'password',
    ];

    public function setUsername($value)
    {
        $this->data['username'] = strtolower($value);
    }
}