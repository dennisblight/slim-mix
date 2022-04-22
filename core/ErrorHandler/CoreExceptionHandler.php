<?php
namespace Core\ErrorHandler;

use Core\Exceptions\ResponseException;
use Throwable;
use DI\Container;
use Laminas\Diactoros\Response;
use Slim\Interfaces\ErrorHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreExceptionHandler implements ErrorHandlerInterface
{
    /** @var Container */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface
    {
        $payload = $exception instanceof ResponseException
            ? $exception->getPayload()
            : [
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]
        ;

        $statusCode = 500;
        if($exception instanceof ResponseException)
        {
            $statusCode = $exception->getStatusCode();
        }

        $response = new Response\JsonResponse($payload);

        return $response->withStatus($statusCode);
    }
}