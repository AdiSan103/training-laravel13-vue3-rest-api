<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CheckToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token tidak ada'], 401);
        }

        // this is just an example, you can implement your own logic to validate the token

        // $accessToken = PersonalAccessToken::findToken($token);

        // if (!$accessToken) {
        //     return response()->json(['message' => $accessToken ], 401);
        // }

        // Set user ke request supaya bisa dipakai di controller
        // $request->setUserResolver(fn() => $accessToken->tokenable);

        return $next($request);
    }
}
