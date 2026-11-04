<?php

namespace Contatoseguro\TesteBackend\Repository\Contracts;

interface CategoryRepositoryInterface
{
    public function getAll($companyId, ?string $lang = null);

    public function getOne($companyId, $categoryId, ?string $lang = null);

    public function getByProductIds(array $productIds, ?string $lang = null): array;

    public function insertOne($body, $companyId);

    public function updateOne($id, $body, $companyId);

    public function deleteOne($id, $companyId);

    public function insertTranslations($categoryId, array $translations): bool;
}
