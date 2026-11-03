<?php

namespace ContatoSeguro\Tests\Feature;

use Contatoseguro\TesteBackend\Config\DB;
use ContatoSeguro\Tests\Factories\CategoryFactory;
use ContatoSeguro\Tests\Factories\CategoryTranslationFactory;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use ContatoSeguro\Tests\Factories\ProductFactory;

class CategoryTranslationTest extends FeatureTestCase
{
    public function testCreatingTranslationsSavesThem(): void
    {
        $categoryId = CategoryFactory::create(['title' => 'house']);

        $response = $this->createTranslations($categoryId, [
            ['lang_code' => 'en', 'label' => 'home'],
            ['lang_code' => 'pt', 'label' => 'casa'],
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(['en' => 'home', 'pt' => 'casa'], $this->translationsOf($categoryId));
    }

    public function testCreatingRepeatedTranslationsSavesNone(): void
    {
        $categoryId = CategoryFactory::create();

        $response = $this->createTranslations($categoryId, [
            ['lang_code' => 'en', 'label' => 'home'],
            ['lang_code' => 'pt', 'label' => 'casa'],
            ['lang_code' => 'pt', 'label' => 'lar'],
        ]);

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame([], $this->translationsOf($categoryId));
    }

    public function testCreatingATranslationThatAlreadyExistsSavesNone(): void
    {
        $categoryId = CategoryFactory::create();
        CategoryTranslationFactory::create(['category_id' => $categoryId, 'lang_code' => 'pt', 'label' => 'casa']);

        $response = $this->createTranslations($categoryId, [
            ['lang_code' => 'en', 'label' => 'home'],
            ['lang_code' => 'pt', 'label' => 'lar'],
        ]);

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame(['pt' => 'casa'], $this->translationsOf($categoryId));
    }

    public function testCreatingTranslationsForACategoryOfAnotherCompanyReturnsNotFound(): void
    {
        $categoryId = CategoryFactory::create(['company_id' => 2]);

        $response = $this->createTranslations($categoryId, [
            ['lang_code' => 'pt', 'label' => 'casa'],
        ]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $this->translationsOf($categoryId));
    }

    public function testShowingACategoryReturnsItInTheRequestedLanguage(): void
    {
        $categoryId = $this->createHouse(['pt' => 'casa']);

        $title = $this->categoryTitle("/categories/{$categoryId}?lang=pt");

        $this->assertSame('casa', $title);
    }

    public function testShowingACategoryWithoutTranslationForTheLanguageReturnsItInEnglish(): void
    {
        $categoryId = $this->createHouse(['en' => 'home']);

        $title = $this->categoryTitle("/categories/{$categoryId}?lang=es");

        $this->assertSame('home', $title);
    }

    public function testShowingACategoryWithoutLanguageReturnsItInEnglish(): void
    {
        $categoryId = $this->createHouse(['en' => 'home', 'pt' => 'casa']);

        $title = $this->categoryTitle("/categories/{$categoryId}");

        $this->assertSame('home', $title);
    }

    public function testShowingACategoryWithoutEnglishTranslationReturnsItsTitle(): void
    {
        $categoryId = $this->createHouse([]);

        $title = $this->categoryTitle("/categories/{$categoryId}?lang=pt");

        $this->assertSame('house', $title);
    }

    public function testListingCategoriesReturnsThemInTheRequestedLanguage(): void
    {
        $categoryId = $this->createHouse(['pt' => 'casa']);

        $titles = array_column($this->getJson('/categories?lang=pt'), 'title', 'id');

        $this->assertSame('casa', $titles[$categoryId]);
    }

    public function testListingProductsReturnsCategoriesInTheRequestedLanguage(): void
    {
        $productId = ProductFactory::create(categoryIds: [$this->createHouse(['pt' => 'casa'])]);

        $categories = array_column($this->getJson('/products?lang=pt'), 'categories', 'id')[$productId];

        $this->assertSame('casa', $categories[0]['title']);
    }

    public function testShowingAProductReturnsCategoriesInTheRequestedLanguage(): void
    {
        $productId = ProductFactory::create(categoryIds: [$this->createHouse(['pt' => 'casa'])]);

        $product = $this->getJson("/products/{$productId}?lang=pt");

        $this->assertSame('casa', $product['categories'][0]['title']);
    }

    private function createTranslations(int $categoryId, array $translations): ResponseInterface
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', "/categories/{$categoryId}")
            ->withHeader('admin_user_id', '1')
            ->withParsedBody(['translations' => $translations]);

        return $this->app->handle($request);
    }

    private function translationsOf(int $categoryId): array
    {
        $stm = DB::connect()->prepare('SELECT lang_code, label FROM category_translation WHERE category_id = ? ORDER BY lang_code');
        $stm->execute([$categoryId]);

        return $stm->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    private function createHouse(array $translations): int
    {
        $categoryId = CategoryFactory::create(['title' => 'house']);

        foreach ($translations as $langCode => $label) {
            CategoryTranslationFactory::create(['category_id' => $categoryId, 'lang_code' => $langCode, 'label' => $label]);
        }

        return $categoryId;
    }

    private function categoryTitle(string $uri): string
    {
        return $this->getJson($uri)[0]['title'];
    }

    private function getJson(string $uri): array
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', $uri)
            ->withHeader('admin_user_id', '1');

        return json_decode((string)$this->app->handle($request)->getBody(), true);
    }
}