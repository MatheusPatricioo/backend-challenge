<?php

namespace ContatoSeguro\Tests\Feature;

use ContatoSeguro\Tests\Factories\CategoryFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class CategoryTest extends FeatureTestCase
{
    public function testListingCategoriesIncludesDefaultCategories(): void
    {
        CategoryFactory::create(['title' => 'toys', 'company_id' => null]);

        $titles = array_column($this->getJson('/categories'), 'title');

        $this->assertContains('toys', $titles);
    }

    public function testListingCategoriesExcludesCategoriesOfOtherCompanies(): void
    {
        CategoryFactory::create(['title' => 'toys', 'company_id' => 2]);

        $titles = array_column($this->getJson('/categories'), 'title');

        $this->assertNotContains('toys', $titles);
    }

    public function testShowingADefaultCategoryReturnsIt(): void
    {
        $categoryId = CategoryFactory::create(['title' => 'toys', 'company_id' => null]);

        $titles = array_column($this->getJson("/categories/{$categoryId}"), 'title');

        $this->assertSame(['toys'], $titles);
    }

    public function testShowingACategoryIgnoresSqlInjection(): void
    {
        $categories = $this->getJson('/categories/4 OR 1=1');

        $this->assertSame([], $categories);
    }

    private function getJson(string $uri): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', $uri)
            ->withHeader('admin_user_id', '1');

        return json_decode((string)$this->app->handle($request)->getBody(), true);
    }
}
