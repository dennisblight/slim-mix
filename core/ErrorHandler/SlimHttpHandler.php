<?php
namespace Core\ErrorHandler;

use Throwable;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;

class SlimHttpHandler implements ErrorHandlerInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface
    {
        if($exception instanceof HttpMethodNotAllowedException)
        {
            $allowedMethods = $exception->getAllowedMethods();
            if(count($allowedMethods) == 1 && $allowedMethods[0] == 'OPTIONS')
            {
                return $this->__invoke(
                    $request,
                    new HttpNotFoundException($request),
                    $displayErrorDetails,
                    $logErrors,
                    $logErrorDetails
                );
            }
        }

        $payload = [
            'code'    => $exception->getCode() + 1000,
            'message' => $exception->getMessage(),
        ];

        if($exception->getCode() >= 500)
        {
            $payload['code'] += 1000;
        }

        if($exception instanceof HttpMethodNotAllowedException)
        {
            $payload['allowedMethods'] = $exception->getAllowedMethods();
        }

        $response = new Response\JsonResponse($payload);

        return $response->withStatus($exception->getCode());
    }
}
