<?php

namespace Contatoseguro\TesteBackend\Repository;

use Contatoseguro\TesteBackend\Dto\ProductFilters;
use Contatoseguro\TesteBackend\Repository\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function getAll($companyId, ProductFilters $filters = new ProductFilters()): array
    {
        $orderBy = match ($filters->sort) {
            'created_at' => 'created_at, id',
            '-created_at' => 'created_at DESC, id DESC',
            default => 'id',
        };

        $stm = $this->pdo->prepare("
            SELECT *
            FROM product
            WHERE company_id = :company_id
              AND (:active IS NULL OR active = :active)
              AND (:category_id IS NULL OR id IN (SELECT product_id FROM product_category WHERE cat_id = :category_id))
              AND (:min_stock IS NULL OR stock >= :min_stock)
            ORDER BY {$orderBy}
        ");
        $stm->execute([
            'company_id' => $companyId,
            'active' => $filters->active,
            'category_id' => $filters->categoryId,
            'min_stock' => $filters->minStock,
        ]);

        return $stm->fetchAll();
    }

    public function getOne($id)
    {
        $stm = $this->pdo->prepare('SELECT * FROM product WHERE id = :id');
        $stm->execute(['id' => $id]);

        return $stm->fetch();
    }

    public function insertOne($body): int
    {
        $stm = $this->pdo->prepare('
            INSERT INTO product (company_id, title, price, active, stock)
            VALUES (:company_id, :title, :price, :active, :stock)
        ');
        $stm->execute([
            'company_id' => $body['company_id'],
            'title' => $body['title'],
            'price' => $body['price'],
            'active' => (int) $body['active'],
            'stock' => $body['stock'] ?? 0,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateOne($id, $body)
    {
        $stm = $this->pdo->prepare('
            UPDATE product
            SET company_id = :company_id, title = :title, price = :price, active = :active,
                stock = COALESCE(:stock, stock)
            WHERE id = :id
        ');

        return $stm->execute([
            'company_id' => $body['company_id'],
            'title' => $body['title'],
            'price' => $body['price'],
            'active' => (int) $body['active'],
            'stock' => $body['stock'] ?? null,
            'id' => $id,
        ]);
    }

    public function deleteOne($id)
    {
        $stm = $this->pdo->prepare('DELETE FROM product WHERE id = :id');

        return $stm->execute(['id' => $id]);
    }

    public function insertCategory($productId, $categoryId)
    {
        $stm = $this->pdo->prepare('INSERT INTO product_category (product_id, cat_id) VALUES (:product_id, :cat_id)');

        return $stm->execute(['product_id' => $productId, 'cat_id' => $categoryId]);
    }

    public function updateCategory($productId, $categoryId)
    {
        $stm = $this->pdo->prepare('UPDATE product_category SET cat_id = :cat_id WHERE product_id = :product_id');

        return $stm->execute(['cat_id' => $categoryId, 'product_id' => $productId]);
    }

    public function deleteCategories($productId)
    {
        $stm = $this->pdo->prepare('DELETE FROM product_category WHERE product_id = :product_id');

        return $stm->execute(['product_id' => $productId]);
    }
}