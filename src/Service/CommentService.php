<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Repository\Contracts\AdminUserRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\CommentRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\ProductRepositoryInterface;

class CommentService
{
    public function __construct(
        private CommentRepositoryInterface   $commentRepository,
        private ProductRepositoryInterface   $productRepository,
        private AdminUserRepositoryInterface $adminUserRepository
    )
    {
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

    public function getAll($adminUserId, int $productId): ?array
    {
        if (!$this->productBelongsToUser($adminUserId, $productId)) {
            return null;
        }

        return $this->buildHierarchy($this->commentRepository->getByProductId($productId));
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

    private function buildHierarchy(array $allComments, ?int $parentId = null): array
    {
        $comments = array_values(array_filter($allComments, fn($comment) => $comment->parent_id === $parentId));

        foreach ($comments as $comment) {
            $comment->replies = $this->buildHierarchy($allComments, $comment->id);
        }

        return $comments;
    }
}