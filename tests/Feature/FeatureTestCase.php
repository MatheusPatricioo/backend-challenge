<?php

namespace ContatoSeguro\Tests\Feature;

use Contatoseguro\TesteBackend\Config\DB;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

abstract class FeatureTestCase extends TestCase
{
    protected App $app;

    protected function setUp(): void
    {
        $database = sys_get_temp_dir() . '/test.sqlite3';

        DB::disconnect();
        copy(__DIR__ . '/../../db/db-backup.sqlite3', $database);
        putenv("DB_PATH={$database}");
        $this->migrate($database);

        $this->app = require __DIR__ . '/../../src/Config/app.php';
    }

    private function migrate(string $database): void
    {
        $config = new Config([
            'paths' => ['migrations' => __DIR__ . '/../../db/migrations'],
            'environments' => [
                'test' => ['adapter' => 'sqlite', 'name' => $database, 'suffix' => ''],
            ],
        ]);

        (new Manager($config, new ArrayInput([]), new NullOutput()))->migrate('test');
    }
}
