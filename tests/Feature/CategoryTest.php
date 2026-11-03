<?php

namespace ContatoSeguro\Tests\Feature;

use Slim\Psr7\Factory\ServerRequestFactory;

class CategoryTest extends FeatureTestCase
{
    public function testListingCategoriesIncludesDefaultCategories(): void
    {
        $this->createCategory('toys', companyId: null);

        $titles = array_column($this->getJson('/categories'), 'title');

        $this->assertContains('toys', $titles);
    }

    public function testListingCategoriesExcludesCategoriesOfOtherCompanies(): void
    {
        $this->createCategory('toys', companyId: 2);

        $titles = array_column($this->getJson('/categories'), 'title');

        $this->assertNotContains('toys', $titles);
    }

    public function testShowingADefaultCategoryReturnsIt(): void
    {
        $categoryId = $this->createCategory('toys', companyId: null);

        $titles = array_column($this->getJson("/categories/{$categoryId}"), 'title');

        $this->assertSame(['toys'], $titles);
    }

    private function getJson(string $uri): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', $uri)
            ->withHeader('admin_user_id', '1');

        return json_decode((string) $this->app->handle($request)->getBody(), true);
    }
}