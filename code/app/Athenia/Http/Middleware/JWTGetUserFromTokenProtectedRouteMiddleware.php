<?php
declare(strict_types=1);

namespace App\Athenia\Http\Middleware;

use App\Exceptions\JWT;
use Illuminate\Foundation\Application;
use PHPOpenSourceSaver\JWTAuth\JWTAuth;

/**
 * Class JWTGetUserFromTokenProtectedRouteMiddleware
 * @package App\Http\Middleware
 */
class JWTGetUserFromTokenProtectedRouteMiddleware
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
     * @param \PHPOpenSourceSaver\JWTAuth\JWTAuth $auth
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
     * @throws \App\Athenia\Exceptions\JWT\TokenMissingException
     * @throws \App\Athenia\Exceptions\JWT\TokenUserNotFoundException
     */
    public function handle($request, \Closure $next)
    {
        if ($this->app->environment() != 'testing') {
            if (!$this->auth->setRequest($request)->getToken()) {
                throw new \App\Athenia\Exceptions\JWT\TokenMissingException('Missing JWT Token', 400);
            }

            if (!$this->auth->authenticate()) {
                throw new \App\Athenia\Exceptions\JWT\TokenUserNotFoundException('JWT User Not Found', 401);
            }
        }
        
        return $next($request);
    }
}
