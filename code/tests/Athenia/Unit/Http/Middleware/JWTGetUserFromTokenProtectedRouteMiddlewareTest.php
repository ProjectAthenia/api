<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Http\Middleware;

use App\Athenia\Exceptions\JWT\TokenMissingException;
use App\Athenia\Exceptions\JWT\TokenUserNotFoundException;
use App\Athenia\Http\Middleware\JWTGetUserFromTokenProtectedRouteMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;
use Tests\TestCase;

/**
 * Class JWTGetUserFromTokenProtectedRouteMiddlewareTest
 * @package Tests\Athenia\Unit\Http\Middleware
 */
final class JWTGetUserFromTokenProtectedRouteMiddlewareTest extends TestCase
{
    public function testHandlePassesAuthenticate(): void
    {
        $app = mock(Application::class);

        $app->shouldReceive('environment')->once()->andReturn('production');

        $request = mock(Request::class);

        $auth = mock(JWTAuth::class);

        $auth->shouldReceive('setRequest')->once()->with($request)->andReturn($auth);
        $auth->shouldReceive('getToken')->once()->andReturn(true);
        $auth->shouldReceive('authenticate')->once()->andReturn(true);

        $middleware = new JWTGetUserFromTokenProtectedRouteMiddleware($app, $auth);

        $closure = function($param) use ($request) {
            $this->assertSame($request, $param);
        };

        $middleware->handle($request, $closure);
    }

    public function testHandleFailsAuthenticate(): void
    {
        $app = mock(Application::class);

        $app->shouldReceive('environment')->once()->andReturn('production');

        $request = mock(Request::class);

        $auth = mock(JWTAuth::class);

        $auth->shouldReceive('setRequest')->once()->with($request)->andReturn($auth);
        $auth->shouldReceive('getToken')->once()->andReturn(true);
        $auth->shouldReceive('authenticate')->once()->andReturn(false);

        $this->expectException(TokenUserNotFoundException::class);

        $middleware = new JWTGetUserFromTokenProtectedRouteMiddleware($app, $auth);

        $closure = function($param) use ($request) {
            $this->assertSame($request, $param);
        };

        $middleware->handle($request, $closure);
    }

    public function testHandleFailsGetToken(): void
    {
        $app = mock(Application::class);

        $app->shouldReceive('environment')->once()->andReturn('production');

        $request = mock(Request::class);

        $auth = mock(JWTAuth::class);

        $auth->shouldReceive('setRequest')->once()->with($request)->andReturn($auth);
        $auth->shouldReceive('getToken')->once()->andReturn(false);

        $this->expectException(TokenMissingException::class);

        $middleware = new JWTGetUserFromTokenProtectedRouteMiddleware($app, $auth);

        $closure = function($param) use ($request) {
            $this->assertSame($request, $param);
        };

        $middleware->handle($request, $closure);
    }
}
