<?php

namespace Contatoseguro\TesteBackend\Controller;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Repository\CompanyRepository;
use Contatoseguro\TesteBackend\Service\CompanyService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CompanyController
{
    private CompanyService $service;

    public function __construct()
    {
        $this->service = new CompanyService(new CompanyRepository(DB::connect()));
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(json_encode($this->service->getAll()));
        return $response->withStatus(200);
    }

    public function getOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $response->getBody()->write(json_encode($this->service->getOne($args['id'])));
        return $response->withStatus(200);
    }
}
