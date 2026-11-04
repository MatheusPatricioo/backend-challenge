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

    public function testListingProductsFiltersActiveProducts(): void
    {
        $activeId = ProductFactory::create(['active' => 1]);
        $inactiveId = ProductFactory::create(['active' => 0]);

        $listedIds = array_column($this->listProducts(query: ['active' => '1']), 'id');

        $this->assertContains($activeId, $listedIds);
        $this->assertNotContains($inactiveId, $listedIds);
    }

    public function testListingProductsFiltersInactiveProducts(): void
    {
        $activeId = ProductFactory::create(['active' => 1]);
        $inactiveId = ProductFactory::create(['active' => 0]);

        $listedIds = array_column($this->listProducts(query: ['active' => '0']), 'id');

        $this->assertContains($inactiveId, $listedIds);
        $this->assertNotContains($activeId, $listedIds);
    }

    public function testListingProductsFiltersByCategory(): void
    {
        $categoryId = CategoryFactory::create();
        $productInCategoryId = ProductFactory::create(categoryIds: [$categoryId]);
        $otherProductId = ProductFactory::create();

        $listedIds = array_column($this->listProducts(query: ['category_id' => (string)$categoryId]), 'id');

        $this->assertContains($productInCategoryId, $listedIds);
        $this->assertNotContains($otherProductId, $listedIds);
    }

    public function testListingProductsSortsByOldestFirst(): void
    {
        $oldestId = ProductFactory::create(['created_at' => '2000-01-01 00:00:00']);

        $listedIds = array_column($this->listProducts(query: ['sort' => 'created_at']), 'id');

        $this->assertSame($oldestId, $listedIds[0]);
    }

    public function testListingProductsSortsByNewestFirst(): void
    {
        $newestId = ProductFactory::create(['created_at' => '2099-01-01 00:00:00']);

        $listedIds = array_column($this->listProducts(query: ['sort' => '-created_at']), 'id');

        $this->assertSame($newestId, $listedIds[0]);
    }

    public function testListingProductsFiltersByMinimumStock(): void
    {
        $enoughStockId = ProductFactory::create(['stock' => 5]);
        $lowStockId = ProductFactory::create(['stock' => 4]);

        $listedIds = array_column($this->listProducts(query: ['min_stock' => '5']), 'id');

        $this->assertContains($enoughStockId, $listedIds);
        $this->assertNotContains($lowStockId, $listedIds);
    }

    public function testShowingAProductShowsItsStock(): void
    {
        $productId = ProductFactory::create(['stock' => 7]);

        $product = $this->showProduct($productId);

        $this->assertSame(7, $product['stock']);
    }

    public function testCreatingAProductWithoutStockSetsItToZero(): void
    {
        $this->sendProduct('POST', '/products', ['title' => 'product without stock']);

        $stocks = array_column($this->listProducts(), 'stock', 'title');

        $this->assertSame(0, $stocks['product without stock']);
    }

    public function testCreatingAProductSavesItsStock(): void
    {
        $this->sendProduct('POST', '/products', ['title' => 'product with stock', 'stock' => 15]);

        $stocks = array_column($this->listProducts(), 'stock', 'title');

        $this->assertSame(15, $stocks['product with stock']);
    }

    public function testUpdatingAProductSavesItsStock(): void
    {
        $productId = ProductFactory::create(['stock' => 5]);

        $this->sendProduct('PUT', "/products/{$productId}", ['stock' => 20]);

        $stocks = array_column($this->listProducts(), 'stock', 'id');
        $this->assertSame(20, $stocks[$productId]);
    }

    public function testUpdatingAProductWithoutStockKeepsIt(): void
    {
        $productId = ProductFactory::create(['stock' => 5]);

        $this->sendProduct('PUT', "/products/{$productId}");

        $stocks = array_column($this->listProducts(), 'stock', 'id');
        $this->assertSame(5, $stocks[$productId]);
    }

    public function testListingProductsWithAnInvalidFilterReturnsBadRequest(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/products')
            ->withHeader('admin_user_id', '1')
            ->withQueryParams(['active' => 'abc']);

        $response = $this->app->handle($request);

        $this->assertSame(400, $response->getStatusCode());
    }

    private function listProducts(int $adminUserId = 1, array $query = []): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/products')
            ->withHeader('admin_user_id', (string)$adminUserId)
            ->withQueryParams($query);

        return json_decode((string)$this->app->handle($request)->getBody(), true);
    }

    private function showProduct(int $productId): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', "/products/{$productId}")
            ->withHeader('admin_user_id', '1');

        return json_decode((string)$this->app->handle($request)->getBody(), true);
    }

    private function sendProduct(string $method, string $uri, array $fields = []): void
    {
        $body = array_merge(
            ['company_id' => 1, 'title' => 'test product', 'price' => 10, 'active' => true, 'category_id' => 1],
            $fields
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest($method, $uri)
            ->withHeader('admin_user_id', '1')
            ->withParsedBody($body);

        $this->app->handle($request);
    }
}
