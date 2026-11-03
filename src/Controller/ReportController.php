<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Repository\AdminUserRepository;
use Contatoseguro\TesteBackend\Repository\CategoryRepository;
use Contatoseguro\TesteBackend\Repository\CompanyRepository;
use Contatoseguro\TesteBackend\Repository\ProductLogRepository;
use Contatoseguro\TesteBackend\Repository\ProductRepository;
use Contatoseguro\TesteBackend\Service\CompanyService;
use Contatoseguro\TesteBackend\Service\ProductService;
use Contatoseguro\TesteBackend\Service\ReportService;
use Contatoseguro\TesteBackend\View\HtmlTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReportController
{
    private ReportService $service;

    public function __construct()
    {
        $pdo = DB::connect();

        $this->service = new ReportService(
            new ProductService(
                new ProductRepository($pdo),
                new ProductLogRepository($pdo),
                new CategoryRepository($pdo),
                new AdminUserRepository($pdo)
            ),
            new CompanyService(new CompanyRepository($pdo))
        );
    }

    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $adminUserId = $request->getHeader('admin_user_id')[0];

        $report = HtmlTable::render($this->service->generate($adminUserId));

        $response->getBody()->write($report);
        return $response->withStatus(200)->withHeader('Content-Type', 'text/html');
    }
}