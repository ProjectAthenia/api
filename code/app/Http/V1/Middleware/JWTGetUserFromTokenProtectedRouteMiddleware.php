<?php
/**
 * Modification of the JWT Middleware Item (by tymon)
 * 
 * Gets the user from the JWT and throws an exception if it cannot
 */
declare(strict_types=1);

namespace App\Http\V1\Middleware;

use App\Exceptions\JWT;
use Illuminate\Foundation\Application;
use Tymon\JWTAuth\JWTAuth;

/**
 * Class JWTGetUserFromTokenProtectedRouteMiddleware
 * @package App\Http\V1\Middleware
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
     * @throws JWT\TokenMissingException
     * @throws JWT\TokenUserNotFoundException
     */
    public function handle($request, \Closure $next)
    {
        if ($this->app->environment() != 'testing') {
            if (!$this->auth->setRequest($request)->getToken()) {
                throw new JWT\TokenMissingException('Missing JWT Token', 400);
            }

            if (!$this->auth->authenticate()) {
                throw new JWT\TokenUserNotFoundException('JWT User Not Found', 401);
            }
        }
        
        return $next($request);
    }
}
