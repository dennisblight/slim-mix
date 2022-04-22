<?php
namespace Core\Base;

use Respect\Validation\Rules;
use Respect\Validation\Validatable;
use Laminas\Diactoros\ServerRequest;

abstract class AbstractForm extends AbstractEntity
{
    /** @var bool */
    public $trimStrings = true;

    /** @var bool */
    public $removeInvisibleCharacters = true;

    public function __construct(ServerRequest $request)
    {
        $params = $request->getQueryParams() ?? [];
        if($request->getMethod() == 'POST')
        {
            $params = array_merge($params, $request->getParsedBody() ?? []);
        }

        parent::__construct($params);
        $this->validate();
    }

    public function __set($name, $value)
    {
        if(is_string($value))
        {
            if($this->trimStrings)
            {
                $value = trim($value);
            }

            if($this->removeInvisibleCharacters)
            {
                $value = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $value);
            }
        }

        parent::__set($name, $value);
    }

    public function getValidator(): Validatable
    {
        return new Rules\AlwaysValid();
    }

    public function validate()
    {
        $this->getValidator()->assert($this->all());
    }
}