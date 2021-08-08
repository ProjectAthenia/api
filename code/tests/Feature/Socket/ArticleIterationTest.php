<?php
declare(strict_types=1);

namespace Tests\Feature\Socket;

use App\Http\Sockets\ArticleIterations;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use App\Repositories\Wiki\ArticleRepository;
use App\Repositories\Wiki\IterationRepository;
use App\Services\StringHelperService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Ratchet\ConnectionInterface;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class ArticleIterationTest
 * @package Tests\Feature\Socket
 */
class ArticleIterationTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleIterations
     */
    private $socket;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    /**
     * Array of open connections with an article acting as a key
     *
     * @var CustomMockInterface[]|ConnectionInterface[]
     */
    private $connections;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->jwtAuth = $this->app->make(JWTAuth::class);
        $this->socket = new ArticleIterations(
            new ArticleRepository(new Article(), $this->getGenericLogMock()),
            new IterationRepository(new ArticleIteration(), $this->getGenericLogMock()),
            $this->jwtAuth,
            new StringHelperService(),
        );
        $this->connections = [];
    }

    /**
     * Create a mock connection
     *
     * @param Article $article
     * @return ConnectionInterface|CustomMockInterface
     */
    public function createValidConnection(Article $article)
    {
        if (!isset($this->connections[$article->id])) {
            $this->connections[$article->id] = [];
        }

        /** @var User $user */
        $user = User::factory()->create();

        $httpRequest = mock(RequestInterface::class);

        /** @var CustomMockInterface|UriInterface $uri */
        $uri = mock(UriInterface::class);
        $httpRequest->shouldReceive('getUri')->andReturn($uri);

        $uri->shouldReceive('getQuery')->andReturn('token=' . $this->jwtAuth->fromSubject($user) . '&article=' . $article->id);

        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $httpRequest;
        $this->socket->onOpen($conn);

        $this->connections[$article->id][] = $conn;
        return $conn;
    }

    public function testOnOpenClosesOnError()
    {
        $connection = mock(ConnectionInterface::class);
        $connection->shouldReceive('close');
        $connection->shouldReceive('send');

        $this->socket->onOpen($connection);
    }

    public function testOnOpen()
    {
        $article = Article::factory()->create();

        $this->createValidConnection($article);
    }

    public function testOnMessageClosesOnError()
    {
        $connection = mock(ConnectionInterface::class);
        $connection->shouldReceive('close');
        $connection->shouldReceive('send');

        $this->socket->onMessage($connection, '');
    }

    public function testRemoveActionMessage()
    {
        /**
         * This should not receive any messages, since we are not going to be updating this article
         */
        $this->createValidConnection(Article::factory()->create());

        $article = Article::factory()->create();
        /**
         * Create some old iterations that should not affect the output
         */
        ArticleIteration::factory()->count(5)->create([
            'article_id' => $article->id,
        ]);
        ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'This is a removal test of iugwhw something.',
        ]);
        $listeningConnection = $this->createValidConnection($article);
        $fromConnection = $this->createValidConnection($article);

        $listeningConnection->shouldReceive('send')->once()->with('This is a removal test of something.');

        $message = json_encode([
            'action' => 'remove',
            'start_position' => 26,
            'length' => 7,
        ]);
        $this->socket->onMessage($fromConnection, $message);
    }

    public function testAddActionMessage()
    {
        /**
         * This should not receive any messages, since we are not going to be updating this article
         */
        $this->createValidConnection(Article::factory()->create());

        $article = Article::factory()->create();
        /**
         * Create some old iterations that should not affect the output
         */
        ArticleIteration::factory()->count(5)->create([
            'article_id' => $article->id,
        ]);
        ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'This is an add test of something.',
        ]);
        $listeningConnection = $this->createValidConnection($article);
        $fromConnection = $this->createValidConnection($article);

        $listeningConnection->shouldReceive('send')->once()->with('This is an add test of something new.');

        $message = json_encode([
            'action' => 'add',
            'start_position' => 32,
            'content' => ' new',
        ]);
        $this->socket->onMessage($fromConnection, $message);
    }

    public function testReplaceActionMessage()
    {
        /**
         * This should not receive any messages, since we are not going to be updating this article
         */
        $this->createValidConnection(Article::factory()->create());

        $article = Article::factory()->create();
        /**
         * Create some old iterations that should not affect the output
         */
        ArticleIteration::factory()->count(5)->create([
            'article_id' => $article->id,
        ]);
        ArticleIteration::factory()->create([
            'article_id' => $article->id,
            'content' => 'This is a replace test of this content.',
        ]);
        $listeningConnection = $this->createValidConnection($article);
        $fromConnection = $this->createValidConnection($article);

        $listeningConnection->shouldReceive('send')->once()->with('This is a replace test of new content.');

        $message = json_encode([
            'action' => 'replace',
            'start_position' => 26,
            'content' => 'new',
            'length' => 4,
        ]);
        $this->socket->onMessage($fromConnection, $message);
    }
}
