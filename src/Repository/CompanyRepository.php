<?php

namespace Contatoseguro\TesteBackend\Repository;

use Contatoseguro\TesteBackend\Repository\Contracts\CompanyRepositoryInterface;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function getAll(): array
    {
        $stm = $this->pdo->prepare('SELECT * FROM company');
        $stm->execute();

        return $stm->fetchAll();
    }

    public function getOne($id)
    {
        $stm = $this->pdo->prepare('SELECT * FROM company WHERE id = :id');
        $stm->execute(['id' => $id]);

        return $stm->fetch();
    }
}