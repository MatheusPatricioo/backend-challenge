<?php

namespace ContatoSeguro\Tests\Factories;

class CommentLikeFactory extends Factory
{
    protected const TABLE = 'product_comment_like';

    protected static function defaults(): array
    {
        return ['admin_user_id' => 1];
    }
}