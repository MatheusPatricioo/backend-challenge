<?php

namespace Contatoseguro\TesteBackend\Repository;

use Contatoseguro\TesteBackend\Repository\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function getAll($companyId): array
    {
        $stm = $this->pdo->prepare('
            SELECT *
            FROM product
            WHERE company_id = :company_id
            ORDER BY id
        ');
        $stm->execute(['company_id' => $companyId]);

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
            INSERT INTO product (company_id, title, price, active)
            VALUES (:company_id, :title, :price, :active)
        ');
        $stm->execute([
            'company_id' => $body['company_id'],
            'title' => $body['title'],
            'price' => $body['price'],
            'active' => (int) $body['active'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateOne($id, $body)
    {
        $stm = $this->pdo->prepare('
            UPDATE product
            SET company_id = :company_id, title = :title, price = :price, active = :active
            WHERE id = :id
        ');

        return $stm->execute([
            'company_id' => $body['company_id'],
            'title' => $body['title'],
            'price' => $body['price'],
            'active' => (int) $body['active'],
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