<?php

namespace ContatoSeguro\Tests\Factories;

class AdminUserFactory extends Factory
{
    protected const TABLE = 'admin_user';

    protected static function defaults(): array
    {
        return ['company_id' => 1, 'email' => uniqid() . '@test.com', 'name' => 'test user'];
    }
}
