<?php

namespace ContatoSeguro\Tests\Feature;

use ContatoSeguro\Tests\Factories\AdminUserFactory;
use ContatoSeguro\Tests\Factories\CategoryFactory;
use ContatoSeguro\Tests\Factories\ProductFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class ProductTest extends FeatureTestCase
{
    public function testListingProductsReturnsEachProductOnce(): void
    {
        $productId = ProductFactory::create(categoryIds: [
            CategoryFactory::create(['title' => 'furniture']),
            CategoryFactory::create(['title' => 'house']),
        ]);

        $listedIds = array_column($this->listProducts(), 'id');

        $this->assertSame(1, array_count_values($listedIds)[$productId]);
    }

    public function testListingProductsShowsAllCategoriesOfTheProduct(): void
    {
        $productId = ProductFactory::create(categoryIds: [
            CategoryFactory::create(['title' => 'furniture']),
            CategoryFactory::create(['title' => 'house']),
        ]);

        $categories = array_column($this->listProducts(), 'categories', 'id')[$productId];

        $this->assertSame(['furniture', 'house'], array_column($categories, 'title'));
    }

    public function testListingProductsShowsProductsOfTheUsersCompany(): void
    {
        $productId = ProductFactory::create();
        $userId = AdminUserFactory::create();

        $listedIds = array_column($this->listProducts($userId), 'id');

        $this->assertContains($productId, $listedIds);
    }

    public function testListingProductsHidesProductsOfOtherCompanies(): void
    {
        $productId = ProductFactory::create();
        $userId = AdminUserFactory::create(['company_id' => 2]);

        $listedIds = array_column($this->listProducts($userId), 'id');

        $this->assertNotContains($productId, $listedIds);
    }

    public function testShowingAProductShowsAllItsCategories(): void
    {
        $productId = ProductFactory::create(categoryIds: [
            CategoryFactory::create(['title' => 'furniture']),
            CategoryFactory::create(['title' => 'house']),
        ]);

        $product = $this->showProduct($productId);

        $this->assertSame(['furniture', 'house'], array_column($product['categories'], 'title'));
    }

    public function testShowingAProductShowsItsDefaultCategory(): void
    {
        $productId = ProductFactory::create(categoryIds: [
            CategoryFactory::create(['title' => 'phone', 'company_id' => null]),
        ]);

        $product = $this->showProduct($productId);

        $this->assertSame(['phone'], array_column($product['categories'], 'title'));
    }

    private function listProducts(int $adminUserId = 1): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/products')
            ->withHeader('admin_user_id', (string) $adminUserId);

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