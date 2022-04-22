<?php
namespace Core\ErrorHandler;

use DI\Container;
use Laminas\Diactoros\Response;
use Slim\Interfaces\ErrorHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

class FormValidationHandler implements ErrorHandlerInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface
    {
        $payload = [
            'code'    => 1000,
            'message' => $exception->getMessage(),
        ];

        if($exception instanceof NestedValidationException)
        {
            $payload['errors'] = $exception->getMessages();
        }

        $response = new Response\JsonResponse($payload);

        return $response->withStatus(403);
    }
}