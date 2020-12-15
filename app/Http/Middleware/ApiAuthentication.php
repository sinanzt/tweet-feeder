<?php

namespace App\Http\Middleware;

use App\Models\User;

class ApiAuthentication
{
    public function handle($request, \Closure $next)
    {
        $token = $request->bearerToken();

        if ($token) {
            $user = User::where('token', $token)->first();
            if ($user) {
                auth()->login($user);
                return $next($request);
            }
        }

        return response([
            'message' => 'Unauthorized'
        ], 403);
    }
}