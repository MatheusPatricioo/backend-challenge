<?php

namespace ContatoSeguro\Tests\Factories;

class CommentFactory extends Factory
{
    protected const TABLE = 'product_comment';

    protected static function defaults(): array
    {
        return ['admin_user_id' => 1, 'content' => 'test comment'];
    }
}
