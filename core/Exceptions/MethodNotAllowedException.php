<?php
namespace Core\Exceptions;

class MethodNotAllowedException extends ResponseException
{
    private $allowedMethods;

    public function __construct(array $allowedMethods = [], string $message = 'Method not allowed.', int $code = 1405)
    {
        parent::__construct(405, $message, $code);
        $this->allowedMethods = $allowedMethods;
    }

    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    public function getPayload(): array
    {
        return array_merge(parent::getPayload(), [
            'allowedMethods' => $this->allowedMethods,
        ]);
    }
}