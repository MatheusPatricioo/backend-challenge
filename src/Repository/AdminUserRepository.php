<?php

namespace Contatoseguro\TesteBackend\Repository;

use Contatoseguro\TesteBackend\Repository\Contracts\AdminUserRepositoryInterface;

class AdminUserRepository implements AdminUserRepositoryInterface
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function getCompanyId($adminUserId)
    {
        $stm = $this->pdo->prepare('SELECT company_id FROM admin_user WHERE id = :id');
        $stm->execute(['id' => $adminUserId]);

        return $stm->fetch()->company_id;
    }
}