<?php
namespace Core\Exceptions;

class BadRequestException extends ResponseException
{
    public function __construct(string $message = 'Bad Request.', int $code = 1400)
    {
        parent::__construct(400, $message, $code);
    }
}