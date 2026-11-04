<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Repository\Contracts\AdminUserRepositoryInterface;

class ReportService
{
    private const ACTIONS = [
        'create' => 'Criação',
        'update' => 'Atualização',
        'delete' => 'Remoção',
    ];

    public function __construct(
        private ProductService $productService,
        private CompanyService $companyService,
        private AdminUserRepositoryInterface $adminUserRepository
    ) {
    }

    public function generate($adminUserId): array
    {
        $data = [];
        $data[] = [
            'Id do produto',
            'Nome da Empresa',
            'Nome do Produto',
            'Valor do Produto',
            'Categorias do Produto',
            'Data de Criação',
            'Logs de Alterações'
        ];

        $products = $this->productService->getAll($adminUserId);
        $companyName = $this->companyService->getOne($this->adminUserRepository->getCompanyId($adminUserId))->name;
        $logsByProduct = $this->productService->getLogs(array_column($products, 'id'));

        foreach ($products as $i => $product) {
            $data[$i + 1][] = $product->id;
            $data[$i + 1][] = $companyName;
            $data[$i + 1][] = $product->title;
            $data[$i + 1][] = $product->price;
            $data[$i + 1][] = implode(', ', array_column($product->categories, 'title'));
            $data[$i + 1][] = $product->created_at;
            $data[$i + 1][] = $this->formatLogs($logsByProduct[$product->id] ?? []);
        }

        return $data;
    }

    private function formatLogs(array $logs): string
    {
        $lines = array_map(fn ($log) => sprintf(
            '(%s, %s, %s)',
            $log->admin_user_name ?? 'Usuário desconhecido',
            self::ACTIONS[$log->action],
            (new \DateTime($log->timestamp))->format('d/m/Y H:i:s')
        ), $logs);

        return implode(",\n", $lines);
    }
}
