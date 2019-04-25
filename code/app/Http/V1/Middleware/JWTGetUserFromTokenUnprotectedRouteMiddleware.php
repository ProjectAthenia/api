<?php
declare(strict_types=1);

namespace App\Http\V1\Middleware;

use Illuminate\Foundation\Application;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class JWTGetUserFromTokenUnprotectedRouteMiddleware
 * @package App\Http\V1\Middleware
 */
class JWTGetUserFromTokenUnprotectedRouteMiddleware
{
    /**
     * @var JWTAuth
     */
    protected $auth;

    /**
     * @var Application
     */
    protected $app;

    /**
     * Create a new middleware instance.
     *
     * @param Application $application
     * @param \Tymon\JWTAuth\JWTAuth $auth
     */
    public function __construct(Application $application, JWTAuth $auth)
    {
        $this->app = $application;
        $this->auth = $auth;
    }

    /**
     * Handle incoming request
     *
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if ($this->app->environment() != 'testing') {
            if ($this->auth->setRequest($request)->getToken()) {
                $this->auth->authenticate();
            }
        }

        return $next($request);
    }
}
