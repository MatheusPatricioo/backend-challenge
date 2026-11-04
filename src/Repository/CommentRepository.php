<?php

namespace Contatoseguro\TesteBackend\Repository;

use Contatoseguro\TesteBackend\Repository\Contracts\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function getOne($id)
    {
        $stm = $this->pdo->prepare('SELECT * FROM product_comment WHERE id = :id');
        $stm->execute(['id' => $id]);

        return $stm->fetch();
    }

    public function getByProductId($productId): array
    {
        $stm = $this->pdo->prepare('
            SELECT pc.id, pc.parent_id, au.name AS user,
                CASE WHEN pc.deleted_at IS NULL THEN pc.content END AS content,
                pc.created_at, pc.deleted_at,
                (SELECT COUNT(*) FROM product_comment_like l WHERE l.comment_id = pc.id) AS likes
            FROM product_comment pc
            LEFT JOIN admin_user au ON au.id = pc.admin_user_id
            WHERE pc.product_id = :product_id
            ORDER BY pc.created_at, pc.id
        ');
        $stm->execute(['product_id' => $productId]);

        return $stm->fetchAll();
    }

    public function insertOne($productId, $adminUserId, $content, $parentId): int
    {
        $stm = $this->pdo->prepare('
            INSERT INTO product_comment (product_id, admin_user_id, parent_id, content)
            VALUES (:product_id, :admin_user_id, :parent_id, :content)
        ');
        $stm->execute([
            'product_id' => $productId,
            'admin_user_id' => $adminUserId,
            'parent_id' => $parentId,
            'content' => $content,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function deleteOne($id, $adminUserId): bool
    {
        $stm = $this->pdo->prepare('
            UPDATE product_comment
            SET deleted_at = CURRENT_TIMESTAMP
            WHERE id = :id
            AND admin_user_id = :admin_user_id
            AND deleted_at IS NULL
        ');
        $stm->execute(['id' => $id, 'admin_user_id' => $adminUserId]);

        return $stm->rowCount() === 1;
    }

    public function insertLike($commentId, $adminUserId): bool
    {
        $stm = $this->pdo->prepare('
            INSERT INTO product_comment_like (comment_id, admin_user_id)
            VALUES (:comment_id, :admin_user_id)
            ON CONFLICT DO NOTHING
        ');
        $stm->execute(['comment_id' => $commentId, 'admin_user_id' => $adminUserId]);

        return $stm->rowCount() === 1;
    }
}
