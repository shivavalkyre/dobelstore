<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Http\Controllers\UserLoginController;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
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
            //return redirect()->route('login');
            //return route('login');
            return route ('login');
            //Route::get('api/auth/login',[UserLoginController::class,'do_login']);
            //return response()->json(['error' => 'Unauthenticated.'], 401);
            //return route('error');
            //return
            //return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
