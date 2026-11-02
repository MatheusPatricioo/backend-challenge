<?php

namespace ContatoSeguro\Tests\Feature;

use Slim\Psr7\Factory\ServerRequestFactory;

class ProductTest extends FeatureTestCase
{
    public function testListingProductsShowsBlueTrouserInClothingCategory(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/products')
            ->withHeader('admin_user_id', '1');

        $response = $this->app->handle($request);
        $categoriesByProduct = array_column(json_decode((string) $response->getBody(), true), 'category', 'title');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('clothing', $categoriesByProduct['blue trouser']);
    }
}