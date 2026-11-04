<?php

namespace ContatoSeguro\Tests\Factories;

class CategoryFactory extends Factory
{
    protected const TABLE = 'category';

    protected static function defaults(): array
    {
        return ['company_id' => 1, 'title' => 'test category', 'active' => 1];
    }
}
