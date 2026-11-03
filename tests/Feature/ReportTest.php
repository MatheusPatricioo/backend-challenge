<?php

namespace ContatoSeguro\Tests\Feature;

use Contatoseguro\TesteBackend\Config\DB;
use Slim\Psr7\Factory\ServerRequestFactory;

class ReportTest extends FeatureTestCase
{
    public function testReportShowsProductLogsInTheRequestedFormat(): void
    {
        $productId = $this->createProductWithCategories([]);
        $userId = $this->createAdminUser(companyId: 1);
        $this->createProductLog($productId, $userId, 'create', '2023-12-01 12:50:30');
        $this->createProductLog($productId, $userId, 'update', '2023-12-11 13:51:40');
        $this->createProductLog($productId, $userId, 'delete', '2023-12-21 14:52:50');

        $report = $this->generateReport();

        $this->assertStringContainsString(
            "(test user, Criação, 01/12/2023 12:50:30),\n" .
            "(test user, Atualização, 11/12/2023 13:51:40),\n" .
            "(test user, Remoção, 21/12/2023 14:52:50)",
            $report
        );
    }

    public function testReportShowsUnknownUserForLogsOfMissingUsers(): void
    {
        $productId = $this->createProductWithCategories([]);
        $this->createProductLog($productId, 999, 'create', '2023-12-01 12:50:30');

        $report = $this->generateReport();

        $this->assertStringContainsString('(Usuário desconhecido, Criação, 01/12/2023 12:50:30)', $report);
    }

    private function createProductLog(int $productId, int $adminUserId, string $action, string $timestamp): void
    {
        DB::connect()
            ->prepare('INSERT INTO product_log (product_id, admin_user_id, action, timestamp) VALUES (?, ?, ?, ?)')
            ->execute([$productId, $adminUserId, $action, $timestamp]);
    }

    private function generateReport(): string
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/report')
            ->withHeader('admin_user_id', '1');

        return (string) $this->app->handle($request)->getBody();
    }

    public function testReportEscapesHtmlInCategoryTitles(): void
    {
        $title = '<script>alert(1)</script>';
        $this->createProductWithCategories([$title]);

        $report = $this->generateReport();

        $this->assertStringContainsString(htmlspecialchars($title), $report);
    }
}