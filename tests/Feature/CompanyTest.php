<?php

namespace ContatoSeguro\Tests\Feature;

use Slim\Psr7\Factory\ServerRequestFactory;

class CompanyTest extends FeatureTestCase
{
    public function testListingCompaniesReturnsAllCompanies(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/companies');

        $response = $this->app->handle($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            [['id' => 1, 'name' => 'XPTO Ltda.', 'active' => 1]],
            json_decode((string)$response->getBody(), true)
        );
    }
}