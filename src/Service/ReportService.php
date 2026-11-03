<?php

namespace Contatoseguro\TesteBackend\Service;

class ReportService
{
    public function __construct(
        private ProductService $productService,
        private CompanyService $companyService
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

        foreach ($products as $i => $product) {
            $companyName = $this->companyService->getOne($product->company_id)->name;

            $productLogs = $this->productService->getLog($product->id);

            $data[$i+1][] = $product->id;
            $data[$i+1][] = $companyName;
            $data[$i+1][] = $product->title;
            $data[$i+1][] = $product->price;
            $data[$i+1][] = implode(', ', array_column($product->categories, 'title'));
            $data[$i+1][] = $product->created_at;
            $data[$i+1][] = $productLogs;
        }

        return $data;
    }
}