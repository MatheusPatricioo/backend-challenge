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
}