<?php
declare(strict_types=1);

namespace Tests\Unit\Http\Middleware;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\LogMiddleware;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

/**
 * Class LogMiddlewareTest
 * @package Tests\Unit\Http\Middleware
 */
class LogMiddlewareTest extends TestCase
{
    public function testTerminate()
    {
        $request = mock(Request::class);

        $request->headers = ['Authorization: Some Auth'];
        $request->shouldReceive('method')->once()->andReturn('GET');
        $request->shouldReceive('fullUrl')->once()->andReturn('test.com');
        $request->shouldReceive('all')->once()->andReturn('{}');
        $request->shouldReceive('ip')->once()->andReturn('129.98.19.54');

        $response = mock(Response::class);

        $response->headers = [];
        $response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $response->shouldReceive('getContent')->once()->andReturn('{}');

        $app = mock(Application::class);

        $app->shouldReceive('environment')->once()->andReturn('production');

        $log = mock(LoggerInterface::class);

        $log->shouldReceive('info')->once()->with('V1', [
            'request' => [
                'method' => 'GET',
                'url' => 'test.com',
                'data' => '{}',
                'headers' => ['Authorization: Some Auth'],
                'ip' => '129.98.19.54'
            ],
            'response' => [
                'status' => 200,
                'headers' => [],
                'content' => '{}'
            ]
        ]);

        $middleware = new LogMiddleware($app, $log);

        $middleware->terminate($request, $response);
    }
}