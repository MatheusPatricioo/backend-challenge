<?php

namespace ContatoSeguro\Tests\Feature;

use Contatoseguro\TesteBackend\Config\DB;
use ContatoSeguro\Tests\Factories\CategoryFactory;
use ContatoSeguro\Tests\Factories\CategoryTranslationFactory;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ServerRequestFactory;

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
}