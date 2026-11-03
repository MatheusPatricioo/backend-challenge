<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Repository\Contracts\AdminUserRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\CommentRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\ProductRepositoryInterface;

class CommentService
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private ProductRepositoryInterface $productRepository,
        private AdminUserRepositoryInterface $adminUserRepository
    ) {
    }

    public function insertOne($adminUserId, int $productId, string $content, ?int $parentId): ?int
    {
        if (!$this->productBelongsToUser($adminUserId, $productId)) {
            return null;
        }

        if ($parentId !== null && !$this->commentBelongsToProduct($parentId, $productId)) {
            return null;
        }

        return $this->commentRepository->insertOne($productId, $adminUserId, $content, $parentId);
    }

    private function productBelongsToUser($adminUserId, int $productId): bool
    {
        $product = $this->productRepository->getOne($productId);

        return $product && $product->company_id === $this->adminUserRepository->getCompanyId($adminUserId);
    }

    private function commentBelongsToProduct($commentId, int $productId): bool
    {
        $comment = $this->commentRepository->getOne($commentId);

        return $comment && $comment->product_id === $productId;
    }
}