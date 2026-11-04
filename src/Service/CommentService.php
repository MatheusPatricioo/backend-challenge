<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Exception\ConflictException;
use Contatoseguro\TesteBackend\Exception\NotFoundException;
use Contatoseguro\TesteBackend\Exception\ValidationException;
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

    public function getAll($adminUserId, int $productId): array
    {
        $this->ensureProductBelongsToUser($adminUserId, $productId);

        return $this->buildHierarchy($this->commentRepository->getByProductId($productId));
    }

    public function insertOne($adminUserId, int $productId, ?string $content, ?int $parentId): int
    {
        if (trim((string) $content) === '') {
            throw new ValidationException('Content is required.');
        }

        $this->ensureProductBelongsToUser($adminUserId, $productId);

        if ($parentId !== null) {
            $this->ensureCommentBelongsToProduct($parentId, $productId);
        }

        return $this->commentRepository->insertOne($productId, $adminUserId, $content, $parentId);
    }

    public function deleteOne($adminUserId, int $commentId): void
    {
        if (!$this->commentRepository->deleteOne($commentId, $adminUserId)) {
            throw new NotFoundException('Comment not found.');
        }
    }

    public function likeOne($adminUserId, int $commentId): void
    {
        $comment = $this->commentRepository->getOne($commentId);

        if (!$comment) {
            throw new NotFoundException('Comment not found.');
        }

        $this->ensureProductBelongsToUser($adminUserId, $comment->product_id);

        if (!$this->commentRepository->insertLike($commentId, $adminUserId)) {
            throw new ConflictException('Comment already liked.');
        }
    }

    private function ensureProductBelongsToUser($adminUserId, int $productId): void
    {
        $product = $this->productRepository->getOne($productId);

        if (!$product || $product->company_id !== $this->adminUserRepository->getCompanyId($adminUserId)) {
            throw new NotFoundException('Product not found.');
        }
    }

    private function ensureCommentBelongsToProduct(int $commentId, int $productId): void
    {
        $comment = $this->commentRepository->getOne($commentId);

        if (!$comment || $comment->product_id !== $productId) {
            throw new NotFoundException('Comment not found.');
        }
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