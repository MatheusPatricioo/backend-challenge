<?php

namespace ContatoSeguro\Tests\Feature;

use Contatoseguro\TesteBackend\Config\DB;
use ContatoSeguro\Tests\Factories\CommentFactory;
use ContatoSeguro\Tests\Factories\ProductFactory;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ServerRequestFactory;

class CommentTest extends FeatureTestCase
{
    public function testCommentingOnAProductSavesTheComment(): void
    {
        $productId = ProductFactory::create();

        $response = $this->postComment($productId, ['content' => 'Great product']);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(['Great product'], array_column($this->commentsOf($productId), 'content'));
    }

    public function testReplyingToACommentSavesTheReply(): void
    {
        $productId = ProductFactory::create();
        $commentId = CommentFactory::create(['product_id' => $productId]);

        $response = $this->postComment($productId, ['content' => 'I agree', 'parent_id' => $commentId]);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([null, $commentId], array_column($this->commentsOf($productId), 'parent_id'));
    }

    public function testCommentingOnAProductOfAnotherCompanyReturnsNotFound(): void
    {
        $productId = ProductFactory::create(['company_id' => 2]);

        $response = $this->postComment($productId, ['content' => 'Great product']);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $this->commentsOf($productId));
    }

    public function testReplyingToACommentOfAnotherProductReturnsNotFound(): void
    {
        $productId = ProductFactory::create();
        $otherProductCommentId = CommentFactory::create(['product_id' => ProductFactory::create()]);

        $response = $this->postComment($productId, ['content' => 'I agree', 'parent_id' => $otherProductCommentId]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([], $this->commentsOf($productId));
    }

    private function postComment(int $productId, array $body): ResponseInterface
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', "/products/{$productId}/comments")
            ->withHeader('admin_user_id', '1')
            ->withParsedBody($body);

        return $this->app->handle($request);
    }

    private function commentsOf(int $productId): array
    {
        $stm = DB::connect()->prepare('SELECT content, parent_id FROM product_comment WHERE product_id = ? ORDER BY id');
        $stm->execute([$productId]);

        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function testListingCommentsNestsEachReplyUnderItsComment(): void
    {
        $productId = ProductFactory::create();
        $commentId = CommentFactory::create(['product_id' => $productId, 'content' => 'Does the battery last?']);
        $replyId = CommentFactory::create(['product_id' => $productId, 'parent_id' => $commentId, 'content' => 'All day']);
        CommentFactory::create(['product_id' => $productId, 'parent_id' => $replyId, 'content' => 'Mine too']);

        $comments = json_decode((string)$this->listComments($productId)->getBody(), true);

        $this->assertCount(1, $comments);
        $this->assertSame('Mine too', $comments[0]['replies'][0]['replies'][0]['content']);
    }

    public function testListingCommentsOfAProductOfAnotherCompanyReturnsNotFound(): void
    {
        $productId = ProductFactory::create(['company_id' => 2]);

        $response = $this->listComments($productId);

        $this->assertSame(404, $response->getStatusCode());
    }

    private function listComments(int $productId): ResponseInterface
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', "/products/{$productId}/comments")
            ->withHeader('admin_user_id', '1');

        return $this->app->handle($request);
    }
}