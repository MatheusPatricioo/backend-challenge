<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Repository\AdminUserRepository;
use Contatoseguro\TesteBackend\Repository\CommentRepository;
use Contatoseguro\TesteBackend\Repository\ProductRepository;
use Contatoseguro\TesteBackend\Service\CommentService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CommentController
{
    private CommentService $service;

    public function __construct()
    {
        $pdo = DB::connect();

        $this->service = new CommentService(
            new CommentRepository($pdo),
            new ProductRepository($pdo),
            new AdminUserRepository($pdo)
        );
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        $comments = $this->service->getAll($adminUserId, (int) $args['id']);

        if ($comments === null) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($comments));
        return $response->withStatus(200);
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];
        $body = $request->getParsedBody();

        $commentId = $this->service->insertOne($adminUserId, (int) $args['id'], $body['content'], $body['parent_id'] ?? null);

        if ($commentId === null) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode(['id' => $commentId]));
        return $response->withStatus(201);
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if (!$this->service->deleteOne($adminUserId, (int) $args['id'])) {
            return $response->withStatus(404);
        }

        return $response->withStatus(204);
    }
}