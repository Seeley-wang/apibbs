<?php

namespace App\Providers;

use Dingo\Api\Auth\Provider\Authorization;
use Dingo\Api\Routing\Route;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PassportDingoProvider extends Authorization
{
    protected $auth;
    protected $guard = 'api';

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth->guard($this->guard);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the providers authorization method.
     *
     * @return string
     */
    public function getAuthorizationMethod()
    {
        // TODO: Implement getAuthorizationMethod() method.
        return 'Bearer';
    }

    /**
     * Authenticate the request and return the authenticated user instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Dingo\Api\Routing\Route $route
     *
     * @return mixed
     */
    public function authenticate(Request $request, Route $route)
    {
        // TODO: Implement authenticate() method.
        if (!$user = $this->auth->user()) {
            throw new UnauthorizedHttpException(get_class($this), 'Unable to authenticate with invalid API key and token.');
        }

        return $user;
    }
}
