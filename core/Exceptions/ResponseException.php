<?php
namespace Core\Exceptions;

class ResponseException extends \Exception
{
    private $statusCode;

    public function __construct(int $statusCode, string $message, int $code = 2000)
    {
        parent::__construct($message, $code);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getPayload(): array
    {
        return [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }
}