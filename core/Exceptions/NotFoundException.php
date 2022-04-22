<?php
namespace Core\Exceptions;

class NotFoundException extends ResponseException
{
    public function __construct(string $message = 'Not found.', int $code = 1404)
    {
        parent::__construct(404, $message, $code);
    }
}