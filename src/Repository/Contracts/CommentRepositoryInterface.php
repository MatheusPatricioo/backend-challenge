<?php

namespace Contatoseguro\TesteBackend\Repository\Contracts;

interface CommentRepositoryInterface
{
    public function getOne($id);

    public function getByProductId($productId): array;

    public function insertOne($productId, $adminUserId, $content, $parentId): int;
    public function deleteOne($id, $adminUserId): bool;
}