<?php

namespace Contatoseguro\TesteBackend\Repository;

use Contatoseguro\TesteBackend\Repository\Contracts\ProductLogRepositoryInterface;

class ProductLogRepository implements ProductLogRepositoryInterface
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function insertOne($productId, $adminUserId, $action)
    {
        $stm = $this->pdo->prepare('
            INSERT INTO product_log (product_id, admin_user_id, action)
            VALUES (:product_id, :admin_user_id, :action)
        ');

        return $stm->execute(['product_id' => $productId, 'admin_user_id' => $adminUserId, 'action' => $action]);
    }

    public function getByProductId($productId): array
    {
        $stm = $this->pdo->prepare('SELECT * FROM product_log WHERE product_id = :product_id');
        $stm->execute(['product_id' => $productId]);

        return $stm->fetchAll();
    }
}