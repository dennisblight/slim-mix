<?php
namespace App\Forms;

use Core\Base\AbstractForm;
use Respect\Validation\Validatable;
use Respect\Validation\Rules;

class LoginForm extends AbstractForm
{
    public static $properties = [
        'username',
        'password',
    ];

    public function setUsername($value)
    {
        $this->data['username'] = strtolower($value);
    }

    public function getValidator(): Validatable
    {
        return new Rules\AlwaysInvalid;
    }
}