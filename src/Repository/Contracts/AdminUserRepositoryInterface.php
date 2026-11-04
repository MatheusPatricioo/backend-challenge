<?php

namespace Contatoseguro\TesteBackend\Repository\Contracts;

interface AdminUserRepositoryInterface
{
    public function getCompanyId($adminUserId);
}
