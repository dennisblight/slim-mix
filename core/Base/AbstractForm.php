<?php
namespace Core\Base;

use Laminas\Diactoros\ServerRequest;

abstract class AbstractForm extends AbstractEntity
{
    /** @var bool */
    public $trimStrings = true;

    /** @var bool */
    public $removeInvisibleCharacters = true;

    public function __construct(ServerRequest $request)
    {
        parent::__construct($request->getParsedBody() ?? []);
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
}