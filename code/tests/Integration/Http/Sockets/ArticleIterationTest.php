<?php
declare(strict_types=1);

namespace Tests\Integration\Http\Sockets;

use App\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Contracts\Repositories\Wiki\IterationRepositoryContract;
use App\Http\Sockets\ArticleIterations;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\Iteration;
use App\Repositories\Wiki\ArticleRepository;
use App\Repositories\Wiki\IterationRepository;
use App\Services\StringHelperService;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

/**
 * Class ArticleIterationTest
 * @package Tests\Integration\Http\Sockets
 */
class ArticleIterationTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleRepositoryContract
     */
    private $articleRepository;

    /**
     * @var IterationRepositoryContract
     */
    private $iterationRepository;

    /**
     * @var CustomMockInterface|JWTAuth
     */
    private $jwtAuth;

    /**
     * @var ArticleIterations
     */
    private $socket;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->articleRepository = new ArticleRepository(new Article(), $this->getGenericLogMock());
        $this->iterationRepository = new IterationRepository(new Iteration(), $this->getGenericLogMock());
        $this->jwtAuth = mock(JWTAuth::class);
        $this->socket = new ArticleIterations(
            $this->articleRepository,
            $this->iterationRepository,
            $this->jwtAuth,
            new StringHelperService(),
        );
    }

    public function testRunAction()
    {
        $user = User::factory()->create();
        /** @var Article $article */
        $article = Article::factory()->create();
        /** @var Iteration $initialIteration */
        Iteration::factory()->create([
            'content' => "Test content with a \n line break",
            'article_id' => $article->id,
        ]);

        $msg = [
            'action' => 'nothing',
        ];

        $this->assertNull($this->socket->runAction($user, $article, $msg));

        $msg = [
            'action' => 'remove',
            'start_position' => 2,
            'length' => 3,
        ];

        $this->assertEquals($article->id, $this->socket->runAction($user, $article, $msg)->id);
    }

    public function testHandleRemoveAction()
    {
        $user = User::factory()->create();
        /** @var Article $article */
        $article = Article::factory()->create();
        /** @var Iteration $initialIteration */
        Iteration::factory()->create([
            'content' => "Test content with a \n line break",
            'article_id' => $article->id,
        ]);

        $this->assertEquals("Test content with a \n line break", $article->last_iteration_content);

        $this->assertNull($this->socket->handleRemoveAction($user, $article, []));

        $msg = [
            'start_position' => 2,
            'length' => 3,
        ];
        $result = $this->socket->handleRemoveAction($user, $article, $msg);

        $this->assertEquals($result->id, $article->id);
        $this->assertEquals("Tecontent with a \n line break", $result->last_iteration_content);

        $msg = [
            'start_position' => 15,
            'length' => 6,
        ];
        $result = $this->socket->handleRemoveAction($user, $article, $msg);

        $this->assertEquals($result->id, $article->id);
        $this->assertEquals("Tecontent with ne break", $result->last_iteration_content);
    }

    public function testHandleAddAction()
    {
        $user = User::factory()->create();
        /** @var Article $article */
        $article = Article::factory()->create();
        /** @var Iteration $initialIteration */
        Iteration::factory()->create([
            'content' => "Test content with a \n line break",
            'article_id' => $article->id,
        ]);

        $this->assertEquals("Test content with a \n line break", $article->last_iteration_content);

        $this->assertNull($this->socket->handleAddAction($user, $article, []));

        $msg = [
            'start_position' => 20,
            'content' => 'hello ',
        ];
        $result = $this->socket->handleAddAction($user, $article, $msg);

        $this->assertEquals($result->id, $article->id);
        $this->assertEquals("Test content with a hello \n line break", $result->last_iteration_content);

        $msg = [
            'start_position' => 0,
            'content' => 'New sentence at the front. ',
        ];
        $result = $this->socket->handleAddAction($user, $article, $msg);

        $this->assertEquals($result->id, $article->id);
        $this->assertEquals("New sentence at the front. Test content with a hello \n line break", $result->last_iteration_content);

        $msg = [
            'start_position' => 100,
            'content' => ' now it ends.',
        ];
        $result = $this->socket->handleAddAction($user, $article, $msg);

        $this->assertEquals($result->id, $article->id);
        $this->assertEquals("New sentence at the front. Test content with a hello \n line break now it ends.", $result->last_iteration_content);
    }

    public function testHandleReplaceAction()
    {
        $user = User::factory()->create();
        /** @var Article $article */
        $article = Article::factory()->create();
        /** @var Iteration $initialIteration */
        Iteration::factory()->create([
            'content' => "Test content with a \n line break",
            'article_id' => $article->id,
        ]);

        $this->assertEquals("Test content with a \n line break", $article->last_iteration_content);

        $this->assertNull($this->socket->handleAddAction($user, $article, []));

        $msg = [
            'start_position' => 20,
            'content' => 'hello.',
            'length' => 12
        ];
        $result = $this->socket->handleReplaceAction($user, $article, $msg);

        $this->assertEquals($result->id, $article->id);
        $this->assertEquals("Test content with a hello.", $result->last_iteration_content);

        $msg = [
            'start_position' => 5,
            'content' => 'greeting',
            'length' => 7,
        ];
        $result = $this->socket->handleReplaceAction($user, $article, $msg);

        $this->assertEquals($result->id, $article->id);
        $this->assertEquals("Test greeting with a hello.", $result->last_iteration_content);
    }
}
