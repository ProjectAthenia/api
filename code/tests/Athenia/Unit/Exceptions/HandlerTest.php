<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Exceptions;

use App\Athenia\Exceptions\Handler;
use App\Athenia\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

/**
 * Class HandlerTest
 * @package Tests\Athenia\Unit\Exceptions
 */
final class HandlerTest extends TestCase 
{
    public function testDebugTrueHasTraceInfoInResponse(): void
    {
        config(['app.debug' => true]);
        $handler = new Handler($this->app);

        $request = new \Illuminate\Http\Request();
        $request->headers->set('Accept', 'application/json');
        $exception = new ValidationException();

        $responseJson = $handler->render($request, $exception)->content();
        $this->assertStringContainsString('trace', $responseJson);
    }

    public function testDebugFalseNoTraceInfoInResponse(): void
    {
        $handler = new Handler($this->app);

        $request = new \Illuminate\Http\Request();
        $request->headers->set('Accept', 'application/json');
        $exception = new ValidationException();

        $responseJson = $handler->render($request, $exception)->content();
        $this->assertStringNotContainsString('trace', $responseJson);
    }
    
    public function testMessageSetSpecialForNotFoundHttpException(): void
    {
        $handler = new Handler($this->app);

        $request = new \Illuminate\Http\Request();
        $request->headers->set('Accept', 'application/json');
        $exception = new NotFoundHttpException();

        $responseJson = $handler->render($request, $exception)->content();
        $this->assertJsonStringEqualsJsonString(json_encode(['message'=>'This path was not found.']), $responseJson);
    }
    
    public function testModelNotFoundDisplaysCustomMessage(): void
    {
        $handler = new Handler($this->app);

        $request = new \Illuminate\Http\Request();
        $request->headers->set('Accept', 'application/json');
        $exception = new ModelNotFoundException();

        $responseJson = $handler->render($request, $exception)->content();
        $this->assertJsonStringEqualsJsonString(json_encode(['message'=>'This item was not found.']), $responseJson);
    }
}