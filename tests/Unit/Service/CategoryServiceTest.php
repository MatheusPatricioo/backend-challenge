<?php

namespace ContatoSeguro\Tests\Unit\Service;

use Contatoseguro\TesteBackend\Exception\ConflictException;
use Contatoseguro\TesteBackend\Exception\NotFoundException;
use Contatoseguro\TesteBackend\Repository\Contracts\AdminUserRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\CategoryRepositoryInterface;
use Contatoseguro\TesteBackend\Service\CategoryService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CategoryServiceTest extends TestCase
{
    private CategoryRepositoryInterface&MockObject $categoryRepository;
    private CategoryService $service;

    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $adminUserRepository = $this->createMock(AdminUserRepositoryInterface::class);
        $adminUserRepository->method('getCompanyId')->willReturn(1);

        $this->service = new CategoryService($this->categoryRepository, $adminUserRepository);
    }

    public function testInsertTranslationsSavesTheTranslations(): void
    {
        $translations = [['lang_code' => 'pt', 'label' => 'roupas']];
        $this->categoryRepository->method('getOne')->willReturn((object) ['id' => 4]);

        $this->categoryRepository->expects($this->once())
            ->method('insertTranslations')
            ->with(4, $translations)
            ->willReturn(true);

        $this->service->insertTranslations(adminUserId: 1, categoryId: 4, translations: $translations);
    }

    public function testInsertingTranslationsOfACategoryThatDoesNotExistThrowsNotFound(): void
    {
        $this->categoryRepository->method('getOne')->willReturn(false);

        $this->expectException(NotFoundException::class);

        $this->service->insertTranslations(adminUserId: 1, categoryId: 4, translations: []);
    }

    public function testInsertingARepeatedTranslationThrowsConflict(): void
    {
        $this->categoryRepository->method('getOne')->willReturn((object) ['id' => 4]);
        $this->categoryRepository->method('insertTranslations')->willReturn(false);

        $this->expectException(ConflictException::class);

        $this->service->insertTranslations(adminUserId: 1, categoryId: 4, translations: []);
    }
}
