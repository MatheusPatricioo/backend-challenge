<?php

namespace Contatoseguro\TesteBackend\Repository;

use Contatoseguro\TesteBackend\Repository\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function getAll($companyId, ?string $lang = null)
    {
        $stm = $this->pdo->prepare('
            SELECT c.id, c.company_id, COALESCE(requested.label, english.label, c.title) AS title, c.active
            FROM category c
            LEFT JOIN category_translation requested ON requested.category_id = c.id AND requested.lang_code = :lang
            LEFT JOIN category_translation english ON english.category_id = c.id AND english.lang_code = \'en\'
            WHERE (c.company_id = :company_id OR c.company_id IS NULL)
        ');
        $stm->execute(['company_id' => $companyId, 'lang' => $lang]);

        return $stm->fetchAll();
    }

    public function getOne($companyId, $categoryId, ?string $lang = null)
    {
        $stm = $this->pdo->prepare('
            SELECT c.id, c.company_id, COALESCE(requested.label, english.label, c.title) AS title, c.active
            FROM category c
            LEFT JOIN category_translation requested ON requested.category_id = c.id AND requested.lang_code = :lang
            LEFT JOIN category_translation english ON english.category_id = c.id AND english.lang_code = \'en\'
            WHERE c.active = 1
            AND (c.company_id = :company_id OR c.company_id IS NULL)
            AND c.id = :id
        ');
        $stm->execute(['company_id' => $companyId, 'id' => $categoryId, 'lang' => $lang]);

        return $stm->fetchAll();
    }

    public function getByProductIds(array $productIds, ?string $lang = null): array
    {
        if ($productIds === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($productIds), '?'));

        $stm = $this->pdo->prepare("
            SELECT pc.product_id, c.id, COALESCE(requested.label, english.label, c.title) AS title
            FROM product_category pc
            INNER JOIN category c ON c.id = pc.cat_id
            LEFT JOIN category_translation requested ON requested.category_id = c.id AND requested.lang_code = ?
            LEFT JOIN category_translation english ON english.category_id = c.id AND english.lang_code = 'en'
            WHERE pc.product_id IN ({$placeholders})
            ORDER BY c.id
        ");
        $stm->execute([$lang, ...array_values($productIds)]);

        $categoriesByProduct = [];
        foreach ($stm->fetchAll() as $row) {
            $categoriesByProduct[$row->product_id][] = ['id' => $row->id, 'title' => $row->title];
        }

        return $categoriesByProduct;
    }

    public function insertOne($body, $companyId)
    {
        $stm = $this->pdo->prepare('
            INSERT INTO category (company_id, title, active)
            VALUES (:company_id, :title, :active)
        ');

        return $stm->execute([
            'company_id' => $companyId,
            'title' => $body['title'],
            'active' => (int) $body['active'],
        ]);
    }

    public function updateOne($id, $body, $companyId)
    {
        $stm = $this->pdo->prepare('
            UPDATE category
            SET title = :title, active = :active
            WHERE id = :id
            AND company_id = :company_id
        ');

        return $stm->execute([
            'title' => $body['title'],
            'active' => (int) $body['active'],
            'id' => $id,
            'company_id' => $companyId,
        ]);
    }

    public function deleteOne($id, $companyId)
    {
        $stm = $this->pdo->prepare('
            DELETE FROM category
            WHERE id = :id
            AND company_id = :company_id
        ');

        return $stm->execute(['id' => $id, 'company_id' => $companyId]);
    }

    public function insertTranslations($categoryId, array $translations): bool
    {
        $stm = $this->pdo->prepare('
            INSERT INTO category_translation (category_id, lang_code, label)
            VALUES (:category_id, :lang_code, :label)
            ON CONFLICT DO NOTHING
        ');

        $this->pdo->beginTransaction();

        foreach ($translations as $translation) {
            $stm->execute([
                'category_id' => $categoryId,
                'lang_code' => $translation['lang_code'],
                'label' => $translation['label'],
            ]);

            if ($stm->rowCount() === 0) {
                $this->pdo->rollBack();

                return false;
            }
        }

        return $this->pdo->commit();
    }
}
