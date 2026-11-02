<?php


namespace ContatoSeguro\Tests\Feature;

use Contatoseguro\TesteBackend\Config\DB;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;

class CompanyTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        $database = sys_get_temp_dir() . '/test.sqlite3';

        DB::disconnect();
        copy(__DIR__ . '/../../db/db-backup.sqlite3', $database);
        putenv("DB_PATH={$database}");

        $this->app = require __DIR__ . '/../../src/Config/app.php';
    }

    public function testListingCompaniesReturnsAllCompanies(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/companies');

        $response = $this->app->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [['id' => 1, 'name' => 'XPTO Ltda.', 'active' => 1]],
            json_decode((string)$response->getBody(), true)
        );
    }
}