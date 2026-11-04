<?php

namespace ContatoSeguro\Tests\Feature;

use Contatoseguro\TesteBackend\Config\DB;
use ContatoSeguro\Tests\Factories\CommentFactory;
use ContatoSeguro\Tests\Factories\ProductFactory;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use ContatoSeguro\Tests\Factories\CommentLikeFactory;

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

    public function testLikingACommentCountsTheLike(): void
    {
        $productId = ProductFactory::create();
        $commentId = CommentFactory::create(['product_id' => $productId]);

        $response = $this->likeComment($commentId);

        $comments = json_decode((string) $this->listComments($productId)->getBody(), true);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(1, $comments[0]['likes']);
    }

    public function testLikingACommentTwiceReturnsConflict(): void
    {
        $productId = ProductFactory::create();
        $commentId = CommentFactory::create(['product_id' => $productId]);
        CommentLikeFactory::create(['comment_id' => $commentId]);

        $response = $this->likeComment($commentId);

        $comments = json_decode((string) $this->listComments($productId)->getBody(), true);
        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame(1, $comments[0]['likes']);
    }

    public function testLikingACommentOnAProductOfAnotherCompanyReturnsNotFound(): void
    {
        $commentId = CommentFactory::create(['product_id' => ProductFactory::create(['company_id' => 2])]);

        $response = $this->likeComment($commentId);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testCommentingWithoutContentReturnsBadRequest(): void
    {
        $productId = ProductFactory::create();

        $response = $this->postComment($productId, []);

        $this->assertSame(400, $response->getStatusCode());
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

    public function testDeletingMyCommentHidesItsContent(): void
    {
        $productId = ProductFactory::create();
        $commentId = CommentFactory::create(['product_id' => $productId, 'content' => 'Does the battery last?']);

        $response = $this->deleteComment($commentId);

        $comments = json_decode((string) $this->listComments($productId)->getBody(), true);
        $this->assertSame(204, $response->getStatusCode());
        $this->assertNull($comments[0]['content']);
    }

    public function testDeletingACommentKeepsItsReplies(): void
    {
        $productId = ProductFactory::create();
        $commentId = CommentFactory::create(['product_id' => $productId]);
        CommentFactory::create(['product_id' => $productId, 'parent_id' => $commentId, 'content' => 'All day']);

        $this->deleteComment($commentId);

        $comments = json_decode((string) $this->listComments($productId)->getBody(), true);
        $this->assertSame('All day', $comments[0]['replies'][0]['content']);
    }

    public function testDeletingACommentOfAnotherUserReturnsNotFound(): void
    {
        $productId = ProductFactory::create();
        $commentId = CommentFactory::create(['product_id' => $productId, 'admin_user_id' => 2, 'content' => 'Not yours']);

        $response = $this->deleteComment($commentId);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['Not yours'], array_column($this->commentsOf($productId), 'content'));
    }

    private function listComments(int $productId): ResponseInterface
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', "/products/{$productId}/comments")
            ->withHeader('admin_user_id', '1');

        return $this->app->handle($request);
    }

    private function deleteComment(int $commentId): ResponseInterface
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('DELETE', "/comments/{$commentId}")
            ->withHeader('admin_user_id', '1');

        return $this->app->handle($request);
    }

    private function likeComment(int $commentId): ResponseInterface
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', "/comments/{$commentId}/likes")
            ->withHeader('admin_user_id', '1');

        return $this->app->handle($request);
    }
}