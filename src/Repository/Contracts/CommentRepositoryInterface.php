<?php

namespace Contatoseguro\TesteBackend\Repository\Contracts;

interface CommentRepositoryInterface
{
    public function getOne($id);

    public function insertOne($productId, $adminUserId, $content, $parentId): int;
}