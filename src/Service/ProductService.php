<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Dto\ProductFilters;
use Contatoseguro\TesteBackend\Repository\Contracts\AdminUserRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\CategoryRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\ProductLogRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\ProductRepositoryInterface;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface    $productRepository,
        private ProductLogRepositoryInterface $productLogRepository,
        private CategoryRepositoryInterface   $categoryRepository,
        private AdminUserRepositoryInterface  $adminUserRepository
    )
    {
    }

    public function getAll($adminUserId, ProductFilters $filters = new ProductFilters(), ?string $lang = null): array
    {
        $companyId = $this->adminUserRepository->getCompanyId($adminUserId);

        $products = $this->productRepository->getAll($companyId, $filters);
        $categoriesByProduct = $this->categoryRepository->getByProductIds(array_column($products, 'id'), $lang);

        foreach ($products as $product) {
            $product->categories = $categoriesByProduct[$product->id] ?? [];
        }

        return $products;
    }

    public function getOne($id)
    {
        return $this->productRepository->getOne($id);
    }

    public function insertOne($body, $adminUserId)
    {
        $productId = $this->productRepository->insertOne($body);
        $this->productRepository->insertCategory($productId, $body['category_id']);

        return $this->productLogRepository->insertOne($productId, $adminUserId, 'create');
    }

    public function updateOne($id, $body, $adminUserId)
    {
        $this->productRepository->updateOne($id, $body);
        $this->productRepository->updateCategory($id, $body['category_id']);

        return $this->productLogRepository->insertOne($id, $adminUserId, 'update');
    }

    public function deleteOne($id, $adminUserId)
    {
        $this->productRepository->deleteCategories($id);
        $this->productRepository->deleteOne($id);

        return $this->productLogRepository->insertOne($id, $adminUserId, 'delete');
    }

    public function getLogs(array $productIds): array
    {
        return $this->productLogRepository->getByProductIds($productIds);
    }
}