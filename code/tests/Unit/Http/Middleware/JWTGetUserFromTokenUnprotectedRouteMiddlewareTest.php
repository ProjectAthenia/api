<?php
declare(strict_types=1);

namespace Tests\Unit\Http\Middleware;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Middleware\JWTGetUserFromTokenUnprotectedRouteMiddleware;
use Tests\TestCase;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

/**
 * Class JWTGetUserFromTokenUnprotectedRouteMiddlewareTest
 * @package Tests\Unit\Http\Middleware
 */
class JWTGetUserFromTokenUnprotectedRouteMiddlewareTest extends TestCase
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

        $middleware = new JWTGetUserFromTokenUnprotectedRouteMiddleware($app, $auth);

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
        $auth->shouldReceive('getToken')->once()->andReturn(false);

        $middleware = new JWTGetUserFromTokenUnprotectedRouteMiddleware($app, $auth);

        $closure = function($param) use ($request) {
            $this->assertSame($request, $param);
        };

        $middleware->handle($request, $closure);
    }
}
