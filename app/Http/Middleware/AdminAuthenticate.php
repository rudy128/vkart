<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AdminAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('admin.login');
        }
    }
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->check() && $this->auth->user()->role == 2) {
            return;
        }

        $this->unauthenticated($request, $guards);
    }
}
