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

    public function getByProductIds(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($productIds), '?'));

        $stm = $this->pdo->prepare("
            SELECT pl.*, au.name AS admin_user_name
            FROM product_log pl
            LEFT JOIN admin_user au ON au.id = pl.admin_user_id
            WHERE pl.product_id IN ({$placeholders})
            ORDER BY pl.timestamp, pl.id
        ");
        $stm->execute(array_values($productIds));

        $logsByProduct = [];
        foreach ($stm->fetchAll() as $log) {
            $logsByProduct[$log->product_id][] = $log;
        }

        return $logsByProduct;
    }
}
