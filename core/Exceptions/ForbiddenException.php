<?php
namespace Core\Exceptions;

class ForbiddenException extends ResponseException
{
    private $errors;

    public function __construct(string $message = 'Forbidden.', int $code = 1403, array $errors = [])
    {
        parent::__construct(403, $message, $code);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getPayload(): array
    {
        return array_merge(parent::getPayload(), [
            'errors' => $this->errors
        ]);
    }
}