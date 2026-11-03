<?php

namespace Contatoseguro\TesteBackend\Repository\Contracts;

interface ProductLogRepositoryInterface
{
    public function insertOne($productId, $adminUserId, $action);

    public function getByProductId($productId): array;
}