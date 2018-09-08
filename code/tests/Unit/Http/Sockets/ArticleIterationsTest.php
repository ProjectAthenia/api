<?php
declare(strict_types=1);

namespace Tests\Unit\Http\Sockets;

use App\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Contracts\Repositories\Wiki\IterationRepositoryContract;
use App\Exceptions\AuthenticationException;
use App\Http\Sockets\ArticleIterations;
use App\Models\User\User;
use App\Models\Wiki\Article;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Ratchet\ConnectionInterface;
use Tests\CustomMockInterface;
use Tests\TestCase;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class ArticleIterationTest
 * @package Tests\Unit\Http\Sockets
 */
class ArticleIterationsTest extends TestCase
{
    /**
     * @var CustomMockInterface|ArticleRepositoryContract
     */
    private $articleRepository;

    /**
     * @var CustomMockInterface|IterationRepositoryContract
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

    public function setUp()
    {
        parent::setUp();
        $this->articleRepository = mock(ArticleRepositoryContract::class);
        $this->iterationRepository = mock(IterationRepositoryContract::class);
        $this->jwtAuth = mock(JWTAuth::class);
        $this->socket = new ArticleIterations($this->articleRepository, $this->iterationRepository, $this->jwtAuth);
    }

    public function testParseAuthHeaderFailsNoHeader()
    {
        $httpRequest = mock(RequestInterface::class);

        $httpRequest->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(false);

        $this->expectException(AuthenticationException::class);

        $this->socket->parseAuthHeader($httpRequest);
    }

    public function testParseAuthHeaderFailsEmptyHeader()
    {
        $httpRequest = mock(RequestInterface::class);

        $httpRequest->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true);
        $httpRequest->shouldReceive('getHeader')->once()->with('Authorization')->andReturn([]);

        $this->expectException(AuthenticationException::class);

        $this->socket->parseAuthHeader($httpRequest);
    }

    public function testParseAuthHeaderFailsInvalidHeader()
    {
        $httpRequest = mock(RequestInterface::class);

        $httpRequest->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true);
        $httpRequest->shouldReceive('getHeader')->once()->with('Authorization')->andReturn(['Bearer']);

        $this->expectException(AuthenticationException::class);

        $this->socket->parseAuthHeader($httpRequest);
    }

    public function testParseAuthHeaderSuccess()
    {
        $httpRequest = mock(RequestInterface::class);

        $httpRequest->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true);
        $httpRequest->shouldReceive('getHeader')->once()->with('Authorization')->andReturn(['Bearer token']);

        $this->assertEquals('token', $this->socket->parseAuthHeader($httpRequest));
    }

    public function testAuthenticationUserFailsParseHeaderFailure()
    {
        $httpRequest = mock(RequestInterface::class);

        $httpRequest->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true);
        $httpRequest->shouldReceive('getHeader')->once()->with('Authorization')->andReturn(['Bearer']);

        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $httpRequest;

        $this->expectException(AuthenticationException::class);

        $this->socket->authenticateUser($conn);
    }

    public function testAuthenticationUserFailsAuthenticateFails()
    {
        $httpRequest = mock(RequestInterface::class);

        $httpRequest->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true);
        $httpRequest->shouldReceive('getHeader')->once()->with('Authorization')->andReturn(['Bearer token']);

        $this->jwtAuth->shouldReceive('setToken')->once()->with('token');
        $this->jwtAuth->shouldReceive('authenticate')->once()->andReturn(null);

        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $httpRequest;

        $this->expectException(AuthenticationException::class);

        $this->socket->authenticateUser($conn);
    }

    public function testAuthenticationUserSuccess()
    {
        $httpRequest = mock(RequestInterface::class);

        $httpRequest->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true);
        $httpRequest->shouldReceive('getHeader')->once()->with('Authorization')->andReturn(['Bearer token']);

        $user = new User();
        $user->id = 545;

        $this->jwtAuth->shouldReceive('setToken')->once()->with('token');
        $this->jwtAuth->shouldReceive('authenticate')->once()->andReturn($user);

        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $httpRequest;

        $this->assertEquals($user, $this->socket->authenticateUser($conn));
    }

    public function testValidateArticleFailsNoArticleID()
    {
        /** @var CustomMockInterface|RequestInterface $request */
        $request = mock(RequestInterface::class);
        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $request;

        /** @var CustomMockInterface|UriInterface $uri */
        $uri = mock(UriInterface::class);

        $uri->shouldReceive('getQuery')->once()->andReturn('something=false');

        $request->shouldReceive('getUri')->once()->andReturn($uri);

        $this->expectException(\Exception::class);

        $this->socket->validateArticle($conn);
    }

    public function testValidateArticleFailsNoArticleNotFound()
    {
        /** @var CustomMockInterface|RequestInterface $request */
        $request = mock(RequestInterface::class);
        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $request;

        /** @var CustomMockInterface|UriInterface $uri */
        $uri = mock(UriInterface::class);

        $uri->shouldReceive('getQuery')->once()->andReturn('article=1');

        $request->shouldReceive('getUri')->once()->andReturn($uri);

        $this->articleRepository->shouldReceive('findOrFail')->once()->with(1)
            ->andThrow(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        $this->socket->validateArticle($conn);
    }

    public function testValidateArticleSucceeds()
    {
        /** @var CustomMockInterface|RequestInterface $request */
        $request = mock(RequestInterface::class);
        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $request;

        /** @var CustomMockInterface|UriInterface $uri */
        $uri = mock(UriInterface::class);

        $uri->shouldReceive('getQuery')->once()->andReturn('article=1');

        $request->shouldReceive('getUri')->once()->andReturn($uri);

        $article = new Article();
        $article->id = 1;

        $this->articleRepository->shouldReceive('findOrFail')->once()->with(1)
            ->andReturn($article);

        $result = $this->socket->validateArticle($conn);

        $this->assertArraySubset(['model' => $article], $result);
        $this->assertCount(0, $result['connections']);
    }

    public function testOnOpen()
    {
        $httpRequest = mock(RequestInterface::class);

        $httpRequest->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true);
        $httpRequest->shouldReceive('getHeader')->once()->with('Authorization')->andReturn(['Bearer token']);

        $user = new User();
        $user->id = 545;

        $this->jwtAuth->shouldReceive('setToken')->once()->with('token');
        $this->jwtAuth->shouldReceive('authenticate')->once()->andReturn($user);

        /** @var CustomMockInterface|UriInterface $uri */
        $uri = mock(UriInterface::class);

        $uri->shouldReceive('getQuery')->once()->andReturn('article=1');

        $httpRequest->shouldReceive('getUri')->once()->andReturn($uri);

        $article = new Article();
        $article->id = 1;

        $this->articleRepository->shouldReceive('findOrFail')->once()->with(1)
            ->andReturn($article);

        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $httpRequest;

        $this->socket->onOpen($conn);
    }

    public function testOnClose()
    {
        $httpRequest = mock(RequestInterface::class);

        /** @var CustomMockInterface|UriInterface $uri */
        $uri = mock(UriInterface::class);

        $uri->shouldReceive('getQuery')->once()->andReturn('article=1');

        $httpRequest->shouldReceive('getUri')->once()->andReturn($uri);

        $article = new Article();
        $article->id = 1;

        $this->articleRepository->shouldReceive('findOrFail')->once()->with(1)
            ->andReturn($article);

        /** @var CustomMockInterface|ConnectionInterface $conn */
        $conn = mock(ConnectionInterface::class);
        $conn->httpRequest = $httpRequest;

        $this->socket->onClose($conn);
    }

    public function testOnError()
    {
        $conn = mock(ConnectionInterface::class);

        $conn->shouldReceive('close')->once();

        $this->socket->onError($conn, new \Exception());
    }
}