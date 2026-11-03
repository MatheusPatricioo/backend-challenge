<?php

namespace Contatoseguro\TesteBackend\Repository\Contracts;

interface CategoryRepositoryInterface
{
    public function getAll($companyId);

    public function getOne($companyId, $categoryId);

    public function getByProductIds(array $productIds): array;

    public function insertOne($body, $companyId);

    public function updateOne($id, $body, $companyId);

    public function deleteOne($id, $companyId);
}