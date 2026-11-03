<?php

namespace Contatoseguro\TesteBackend\Repository\Contracts;

interface CompanyRepositoryInterface
{
    public function getAll(): array;

    public function getOne($id);
}