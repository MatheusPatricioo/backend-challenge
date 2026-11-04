<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Repository\AdminUserRepository;
use Contatoseguro\TesteBackend\Repository\CategoryRepository;
use Contatoseguro\TesteBackend\Service\CategoryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryController
{
    private CategoryService $service;

    public function __construct()
    {
        $pdo = DB::connect();
        $this->service = new CategoryService(new CategoryRepository($pdo), new AdminUserRepository($pdo));
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        $categories = $this->service->getAll($adminUserId, $request->getQueryParams()['lang'] ?? null);
        $response->getBody()->write(json_encode($categories));
        return $response->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];
        $category = $this->service->getOne($adminUserId, $args['id'], $request->getQueryParams()['lang'] ?? null);

        $response->getBody()->write(json_encode($category));
        return $response->withStatus(200);
    }

    public function insertOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->insertOne($body, $adminUserId)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function updateOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->updateOne($args['id'], $body, $adminUserId)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function deleteOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        if ($this->service->deleteOne($args['id'], $adminUserId)) {
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }
    }

    public function insertTranslations(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        $this->service->insertTranslations($adminUserId, $args['id'], $request->getParsedBody()['translations']);

        return $response->withStatus(201);
    }
}
