<?php

namespace ContatoSeguro\Tests\Factories;

use Contatoseguro\TesteBackend\Config\DB;

abstract class Factory
{
    protected const TABLE = '';

    abstract protected static function defaults(): array;

    public static function create(array $attributes = []): int
    {
        $data = array_merge(static::defaults(), $attributes);
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $pdo = DB::connect();
        $pdo->prepare('INSERT INTO ' . static::TABLE . " ({$columns}) VALUES ({$placeholders})")
            ->execute(array_values($data));

        return (int)$pdo->lastInsertId();
    }
}
