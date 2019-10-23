<?php
declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\Handler;
use App\Exceptions\ValidationException;
use Tests\TestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class HandlerTest
 * @package Tests\Unit\Exceptions
 */
class HandlerTest extends TestCase 
{
    public function testDebugTrueHasTraceInfoInResponse()
    {
        config(['app.debug' => true]);
        $handler = new Handler($this->app);

        $request = new \Illuminate\Http\Request();
        $request->headers->set('Accept', 'application/json');
        $exception = new ValidationException();

        $responseJson = $handler->render($request, $exception)->content();
        $this->assertStringContainsString('trace', $responseJson);
    }

    public function testDebugFalseNoTraceInfoInResponse()
    {
        $handler = new Handler($this->app);

        $request = new \Illuminate\Http\Request();
        $request->headers->set('Accept', 'application/json');
        $exception = new ValidationException();

        $responseJson = $handler->render($request, $exception)->content();
        $this->assertStringNotContainsString('trace', $responseJson);
    }
    
    public function testMessageSetSpecialForNotFoundHttpException()
    {
        $handler = new Handler($this->app);

        $request = new \Illuminate\Http\Request();
        $request->headers->set('Accept', 'application/json');
        $exception = new NotFoundHttpException();

        $responseJson = $handler->render($request, $exception)->content();
        $this->assertJsonStringEqualsJsonString(json_encode(['message'=>'This path was not found.']), $responseJson);
    }
    
    public function testModelNotFoundDisplaysCustomMessage()
    {
        $handler = new Handler($this->app);

        $request = new \Illuminate\Http\Request();
        $request->headers->set('Accept', 'application/json');
        $exception = new ModelNotFoundException();

        $responseJson = $handler->render($request, $exception)->content();
        $this->assertJsonStringEqualsJsonString(json_encode(['message'=>'This item was not found.']), $responseJson);
    }
}