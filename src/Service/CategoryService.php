<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Repository\Contracts\AdminUserRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private AdminUserRepositoryInterface $adminUserRepository
    ) {
    }

    public function getAll($adminUserId)
    {
        return $this->categoryRepository->getAll($this->adminUserRepository->getCompanyId($adminUserId));
    }

    public function getOne($adminUserId, $categoryId)
    {
        return $this->categoryRepository->getOne($this->adminUserRepository->getCompanyId($adminUserId), $categoryId);
    }

    public function getByProductIds(array $productIds): array
    {
        return $this->categoryRepository->getByProductIds($productIds);
    }

    public function insertOne($body, $adminUserId)
    {
        return $this->categoryRepository->insertOne($body, $this->adminUserRepository->getCompanyId($adminUserId));
    }

    public function updateOne($id, $body, $adminUserId)
    {
        return $this->categoryRepository->updateOne($id, $body, $this->adminUserRepository->getCompanyId($adminUserId));
    }

    public function deleteOne($id, $adminUserId)
    {
        return $this->categoryRepository->deleteOne($id, $this->adminUserRepository->getCompanyId($adminUserId));
    }
}