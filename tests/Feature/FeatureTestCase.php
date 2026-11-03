<?php

namespace ContatoSeguro\Tests\Feature;

use Contatoseguro\TesteBackend\Config\DB;
use PHPUnit\Framework\TestCase;
use Slim\App;

abstract class FeatureTestCase extends TestCase
{
    protected App $app;

    protected function setUp(): void
    {
        $database = sys_get_temp_dir() . '/test.sqlite3';

        DB::disconnect();
        copy(__DIR__ . '/../../db/db-backup.sqlite3', $database);
        putenv("DB_PATH={$database}");

        $this->app = require __DIR__ . '/../../src/Config/app.php';
    }

    protected function createProductWithCategories(array $categoryTitles): int
    {
        $pdo = DB::connect();

        $pdo->exec("INSERT INTO product (company_id, title, price, active) VALUES (1, 'test product', 10, 1)");
        $productId = (int) $pdo->lastInsertId();

        foreach ($categoryTitles as $title) {
            $pdo->prepare('INSERT INTO category (company_id, title, active) VALUES (1, ?, 1)')->execute([$title]);
            $pdo->prepare('INSERT INTO product_category (product_id, cat_id) VALUES (?, ?)')->execute([$productId, $pdo->lastInsertId()]);
        }

        return $productId;
    }
}