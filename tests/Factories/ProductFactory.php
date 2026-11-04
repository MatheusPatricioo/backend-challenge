<?php

namespace ContatoSeguro\Tests\Factories;

use Contatoseguro\TesteBackend\Config\DB;

class ProductFactory extends Factory
{
    protected const TABLE = 'product';

    protected static function defaults(): array
    {
        return ['company_id' => 1, 'title' => 'test product', 'price' => 10, 'active' => 1];
    }

    public static function create(array $attributes = [], array $categoryIds = []): int
    {
        $productId = parent::create($attributes);

        foreach ($categoryIds as $categoryId) {
            DB::connect()
                ->prepare('INSERT INTO product_category (product_id, cat_id) VALUES (?, ?)')
                ->execute([$productId, $categoryId]);
        }

        return $productId;
    }
}
