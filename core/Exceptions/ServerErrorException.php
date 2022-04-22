<?php
namespace Core\Exceptions;

class ServerErrorException extends ResponseException
{
    public function __construct(string $message = 'Server Error.', int $code = 2500)
    {
        parent::__construct(500, $message, $code);
    }
}