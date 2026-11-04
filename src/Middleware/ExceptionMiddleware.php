<?php

namespace Contatoseguro\TesteBackend\Middleware;

use Contatoseguro\TesteBackend\Exception\ConflictException;
use Contatoseguro\TesteBackend\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ExceptionMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (NotFoundException $exception) {
            return $this->errorResponse(404, $exception->getMessage());
        } catch (ConflictException $exception) {
            return $this->errorResponse(409, $exception->getMessage());
        }
    }

    private function errorResponse(int $status, string $message): ResponseInterface
    {
        $response = new Response($status);
        $response->getBody()->write(json_encode(['message' => $message]));

        return $response;
    }
}