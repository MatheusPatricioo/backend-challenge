<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Repository\Contracts\CompanyRepositoryInterface;

class CompanyService
{
    public function __construct(private CompanyRepositoryInterface $companyRepository)
    {
    }

    public function getAll(): array
    {
        return $this->companyRepository->getAll();
    }

    public function getOne($id)
    {
        return $this->companyRepository->getOne($id);
    }
}
