<?php

namespace ContatoSeguro\Tests\Unit\Service;

use Contatoseguro\TesteBackend\Exception\ConflictException;
use Contatoseguro\TesteBackend\Exception\NotFoundException;
use Contatoseguro\TesteBackend\Exception\ValidationException;
use Contatoseguro\TesteBackend\Repository\Contracts\AdminUserRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\CommentRepositoryInterface;
use Contatoseguro\TesteBackend\Repository\Contracts\ProductRepositoryInterface;
use Contatoseguro\TesteBackend\Service\CommentService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommentServiceTest extends TestCase
{
    private CommentRepositoryInterface&MockObject $commentRepository;
    private ProductRepositoryInterface&MockObject $productRepository;
    private CommentService $service;

    protected function setUp(): void
    {
        $this->commentRepository = $this->createMock(CommentRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        $adminUserRepository = $this->createMock(AdminUserRepositoryInterface::class);
        $adminUserRepository->method('getCompanyId')->willReturn(1);

        $this->service = new CommentService($this->commentRepository, $this->productRepository, $adminUserRepository);
    }

    public function testGetAllNestsRepliesUnderTheirComments(): void
    {
        $this->productRepository->method('getOne')->willReturn((object) ['company_id' => 1]);
        $this->commentRepository->method('getByProductId')->willReturn([
            (object) ['id' => 1, 'parent_id' => null],
            (object) ['id' => 2, 'parent_id' => 1],
            (object) ['id' => 3, 'parent_id' => 2],
        ]);

        $comments = $this->service->getAll(adminUserId: 1, productId: 10);

        $this->assertCount(1, $comments);
        $this->assertSame(3, $comments[0]->replies[0]->replies[0]->id);
    }

    public function testGetAllOfAProductOfAnotherCompanyThrowsNotFound(): void
    {
        $this->productRepository->method('getOne')->willReturn((object) ['company_id' => 2]);

        $this->expectException(NotFoundException::class);

        $this->service->getAll(adminUserId: 1, productId: 10);
    }

    public function testInsertOneReturnsTheIdOfTheNewComment(): void
    {
        $this->productRepository->method('getOne')->willReturn((object) ['company_id' => 1]);
        $this->commentRepository->method('insertOne')->willReturn(5);

        $commentId = $this->service->insertOne(adminUserId: 1, productId: 10, content: 'Great product', parentId: null);

        $this->assertSame(5, $commentId);
    }

    public function testInsertingAnEmptyCommentThrowsValidation(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->insertOne(adminUserId: 1, productId: 10, content: '   ', parentId: null);
    }

    public function testReplyingToACommentOfAnotherProductThrowsNotFound(): void
    {
        $this->productRepository->method('getOne')->willReturn((object) ['company_id' => 1]);
        $this->commentRepository->method('getOne')->willReturn((object) ['product_id' => 20]);

        $this->expectException(NotFoundException::class);

        $this->service->insertOne(adminUserId: 1, productId: 10, content: 'I agree', parentId: 7);
    }

    public function testDeleteOneDeletesTheComment(): void
    {
        $this->commentRepository->expects($this->once())
            ->method('deleteOne')
            ->with(7, 1)
            ->willReturn(true);

        $this->service->deleteOne(adminUserId: 1, commentId: 7);
    }

    public function testDeletingACommentThatDoesNotExistThrowsNotFound(): void
    {
        $this->commentRepository->method('deleteOne')->willReturn(false);

        $this->expectException(NotFoundException::class);

        $this->service->deleteOne(adminUserId: 1, commentId: 7);
    }

    public function testLikeOneSavesTheLike(): void
    {
        $this->commentRepository->method('getOne')->willReturn((object) ['product_id' => 10]);
        $this->productRepository->method('getOne')->willReturn((object) ['company_id' => 1]);

        $this->commentRepository->expects($this->once())
            ->method('insertLike')
            ->with(7, 1)
            ->willReturn(true);

        $this->service->likeOne(adminUserId: 1, commentId: 7);
    }

    public function testLikingACommentAlreadyLikedThrowsConflict(): void
    {
        $this->commentRepository->method('getOne')->willReturn((object) ['product_id' => 10]);
        $this->productRepository->method('getOne')->willReturn((object) ['company_id' => 1]);
        $this->commentRepository->method('insertLike')->willReturn(false);

        $this->expectException(ConflictException::class);

        $this->service->likeOne(adminUserId: 1, commentId: 7);
    }
}
