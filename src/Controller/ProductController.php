<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Dto\ProductFilters;
use Contatoseguro\TesteBackend\Model\Product;
use Contatoseguro\TesteBackend\Repository\AdminUserRepository;
use Contatoseguro\TesteBackend\Repository\CategoryRepository;
use Contatoseguro\TesteBackend\Repository\ProductLogRepository;
use Contatoseguro\TesteBackend\Repository\ProductRepository;
use Contatoseguro\TesteBackend\Service\CategoryService;
use Contatoseguro\TesteBackend\Service\ProductService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductController
{
    private ProductService $service;
    private CategoryService $categoryService;

    public function __construct()
    {
        $pdo = DB::connect();

        $this->service = new ProductService(
            new ProductRepository($pdo),
            new ProductLogRepository($pdo),
            new CategoryRepository($pdo),
            new AdminUserRepository($pdo)
        );
        $this->categoryService = new CategoryService(new CategoryRepository($pdo), new AdminUserRepository($pdo));
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];
        $query = $request->getQueryParams();

        $products = $this->service->getAll($adminUserId, ProductFilters::fromQueryParams($query), $query['lang'] ?? null);
        $response->getBody()->write(json_encode($products));
        return $response->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $product = Product::hydrateByFetch($this->service->getOne($args['id']));

        $lang = $request->getQueryParams()['lang'] ?? null;
        $product->setCategories($this->categoryService->getByProductIds([$product->id], $lang)[$product->id] ?? []);

        $response->getBody()->write(json_encode($product));
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
}
