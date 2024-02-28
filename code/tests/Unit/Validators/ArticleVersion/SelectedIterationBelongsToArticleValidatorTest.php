<?php
declare(strict_types=1);

namespace Tests\Unit\Validators\Test;

use App\Contracts\Repositories\Wiki\ArticleIterationRepositoryContract;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use App\Validators\ArticleVersion\SelectedIterationBelongsToArticleValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class SelectedIterationBelongsToArticleValidatorTest
 * @package Tests\Unit\Validators\Test
 */
final class SelectedIterationBelongsToArticleValidatorTest extends TestCase
{
    /**
     * @var CustomMockInterface|ArticleIterationRepositoryContract
     */
    private $repository;

    /**
     * @var CustomMockInterface|Request
     */
    private $request;

    /**
     * @var SelectedIterationBelongsToArticleValidator
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = mock(ArticleIterationRepositoryContract::class);
        $this->request = mock(Request::class);

        $this->validator = new SelectedIterationBelongsToArticleValidator(
            $this->request,
            $this->repository,
        );
    }

    public function testValidatePassesQuestionOptionIdNotSet(): void
    {
        $this->assertTrue($this->validator->validate('article_iteration_id', null));
    }

    public function testValidateFailsQuestionIdNotSet(): void
    {
        $this->request->shouldReceive('route')->once()->with('article', null)->andReturn(null);

        $this->assertFalse($this->validator->validate('article_iteration_id', 332));
    }

    public function testValidateFailsQuestionOptionNotFound(): void
    {
        $article = new Article();
        $article->id = 453;
        $this->request->shouldReceive('route')->once()->with('article', null)->andReturn($article);
        $this->repository->shouldReceive('findOrFail')->once()->andThrow(ModelNotFoundException::class);

        $this->assertFalse($this->validator->validate('article_iteration_id', 332));
    }

    public function testValidateFailsQuestionOptionAndQuestionIdDoesNotMatch(): void
    {
        $article = new Article();
        $article->id = 453;
        $this->request->shouldReceive('route')->once()->with('article', null)->andReturn($article);
        $this->repository->shouldReceive('findOrFail')->once()->andReturn(new ArticleIteration([
            'article_id' => 454,
        ]));

        $this->assertFalse($this->validator->validate('article_iteration_id', 332));
    }

    public function testValidatePasses(): void
    {
        $article = new Article();
        $article->id = 453;
        $this->request->shouldReceive('route')->once()->with('article', null)->andReturn($article);
        $this->repository->shouldReceive('findOrFail')->once()->andReturn(new ArticleIteration([
            'article_id' => 453,
        ]));

        $this->assertTrue($this->validator->validate('article_iteration_id', 332));
    }
}
