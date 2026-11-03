<?php

namespace ContatoSeguro\Tests\Feature;

use ContatoSeguro\Tests\Factories\AdminUserFactory;
use ContatoSeguro\Tests\Factories\CategoryFactory;
use ContatoSeguro\Tests\Factories\ProductFactory;
use ContatoSeguro\Tests\Factories\ProductLogFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class ReportTest extends FeatureTestCase
{
    public function testReportShowsProductLogsInTheRequestedFormat(): void
    {
        $productId = ProductFactory::create();
        $userId = AdminUserFactory::create();
        ProductLogFactory::create([
            'product_id' => $productId,
            'admin_user_id' => $userId,
            'action' => 'create',
            'timestamp' => '2023-12-01 12:50:30',
        ]);
        ProductLogFactory::create([
            'product_id' => $productId,
            'admin_user_id' => $userId,
            'action' => 'update',
            'timestamp' => '2023-12-11 13:51:40',
        ]);
        ProductLogFactory::create([
            'product_id' => $productId,
            'admin_user_id' => $userId,
            'action' => 'delete',
            'timestamp' => '2023-12-21 14:52:50',
        ]);

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
        $productId = ProductFactory::create();
        ProductLogFactory::create([
            'product_id' => $productId,
            'admin_user_id' => 999,
            'timestamp' => '2023-12-01 12:50:30',
        ]);

        $report = $this->generateReport();

        $this->assertStringContainsString('(Usuário desconhecido, Criação, 01/12/2023 12:50:30)', $report);
    }

    public function testReportEscapesHtmlInCategoryTitles(): void
    {
        $title = '<script>alert(1)</script>';
        ProductFactory::create(categoryIds: [CategoryFactory::create(['title' => $title])]);

        $report = $this->generateReport();

        $this->assertStringContainsString(htmlspecialchars($title), $report);
    }

    private function generateReport(): string
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/report')
            ->withHeader('admin_user_id', '1');

        return (string) $this->app->handle($request)->getBody();
    }
}