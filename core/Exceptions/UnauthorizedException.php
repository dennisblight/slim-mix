<?php
namespace Core\Exceptions;

class UnauthorizedException extends ResponseException
{
    public function __construct(string $message = 'Unauthorized.', int $code = 1401)
    {
        parent::__construct(401, $message, $code);
    }
}