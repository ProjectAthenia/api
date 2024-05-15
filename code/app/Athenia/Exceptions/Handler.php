<?php
declare(strict_types=1);

namespace App\Athenia\Exceptions;

use App\Athenia\Exceptions\JWT\TokenMissingException;
use App\Athenia\Exceptions\JWT\TokenUserNotFoundException;
use Cartalyst\Stripe\Exception\CardErrorException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
        CardErrorException::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response
     */
    public function render($request, Throwable $exception)
    {
        list($status, $response) = $this->parseException($exception);

        // if we're in debug mode, add extra information for us
        if (config('app.debug')) {
            $response['exception_class'] = get_class($exception);
            $response['exception_message'] = $exception->getMessage();
            $response['exception_trace'] = $exception->getTrace();
        }

        // Return a JSON response with the response array and status code
        return response()->json($response, $status);
    }

    /**
     * Parse the exception for the status code and the message
     *
     * @param Throwable $exception
     * @return array
     */
    protected function parseException(Throwable $exception): array
    {
        $status = 500;
        $response = [
            'message' => 'Sorry, something went wrong.'
        ];

        switch (true) {
            case $exception instanceof JWTException:
                $response['message'] = $exception->getMessage();
                $status = 401;
                break;

            case $exception instanceof TokenMissingException:
            case $exception instanceof TokenUserNotFoundException:
                $response['message'] = $exception->getMessage();
                $status = $exception->getCode();
                break;

            case $exception instanceof AuthorizationException:
                $status = 403;
                $response['details'] = $exception->getMessage();
                break;

            case $exception instanceof HttpException:
                $response['message'] = $exception instanceof NotFoundHttpException ?
                    'This path was not found.' : $exception->getMessage();
                $status = $exception->getStatusCode();
                break;

            case $exception instanceof ValidationException:
                $response['errors'] = $exception->errors();
                $status = 400;
                break;

            case $exception instanceof ModelNotFoundException:
                $response['message'] = 'This item was not found.';
                $status = 404;
                break;
        }

        return [$status, $response];
    }
}
