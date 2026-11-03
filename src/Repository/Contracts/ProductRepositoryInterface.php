<?php

namespace Contatoseguro\TesteBackend\Repository\Contracts;

interface ProductRepositoryInterface
{
    public function getAll($companyId): array;

    public function getOne($id);

    public function insertOne($body): int;

    public function updateOne($id, $body);

    public function deleteOne($id);

    public function insertCategory($productId, $categoryId);

    public function updateCategory($productId, $categoryId);

    public function deleteCategories($productId);
}