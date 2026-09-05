<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Unauthorized. Missing token.'], 401);
        }

        $tokenHash = hash('sha256', $token);

        $user = User::query()
            ->where('api_token', $tokenHash)
            ->where('is_admin', true)
            ->first();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized. Invalid admin token.'], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
