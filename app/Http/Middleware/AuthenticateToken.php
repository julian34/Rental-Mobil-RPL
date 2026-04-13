<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AuthenticateToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if ($token) {
            $user = User::where('api_token', $token)->first();
            if ($user) {
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
                auth()->setUser($user);
            }
        }

        return $next($request);
    }
}
