<?php

namespace ContatoSeguro\Tests\Factories;

class ProductLogFactory extends Factory
{
    protected const TABLE = 'product_log';

    protected static function defaults(): array
    {
        return ['action' => 'create'];
    }
}
