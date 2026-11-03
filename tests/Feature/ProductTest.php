<?php

namespace ContatoSeguro\Tests\Feature;

use Slim\Psr7\Factory\ServerRequestFactory;

class ProductTest extends FeatureTestCase
{
    public function testListingProductsReturnsEachProductOnce(): void
    {
        $productId = $this->createProductWithCategories(['furniture', 'house']);

        $listedIds = array_column($this->listProducts(), 'id');

        $this->assertSame(1, array_count_values($listedIds)[$productId]);
    }

    public function testListingProductsShowsAllCategoriesOfTheProduct(): void
    {
        $productId = $this->createProductWithCategories(['furniture', 'house']);

        $categories = array_column($this->listProducts(), 'categories', 'id')[$productId];

        $this->assertSame(['furniture', 'house'], array_column($categories, 'title'));
    }

    public function testShowingAProductShowsAllItsCategories(): void
    {
        $productId = $this->createProductWithCategories(['furniture', 'house']);

        $product = $this->showProduct($productId);

        $this->assertSame(['furniture', 'house'], array_column($product['categories'], 'title'));
    }

    public function testShowingAProductShowsItsDefaultCategory(): void
    {
        $productId = $this->createProductWithCategories(['phone'], categoryCompanyId: null);

        $product = $this->showProduct($productId);

        $this->assertSame(['phone'], array_column($product['categories'], 'title'));
    }

    private function listProducts(): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/products')
            ->withHeader('admin_user_id', '1');

        return json_decode((string) $this->app->handle($request)->getBody(), true);
    }

    private function showProduct(int $productId): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', "/products/{$productId}")
            ->withHeader('admin_user_id', '1');

        return json_decode((string) $this->app->handle($request)->getBody(), true);
    }
}