<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenAbilities
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$abilities
     */
    public function handle(Request $request, Closure $next, ...$abilities): Response
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $token = $request->user()->currentAccessToken();

        if (!$token) {
            return response()->json([
                'message' => 'Invalid token.',
            ], 401);
        }

        // Handle TransientToken (used in tests with actingAs())
        // TransientToken doesn't have abilities, so we allow full access
        if ($token instanceof \Laravel\Sanctum\TransientToken) {
            return $next($request);
        }

        // Check if token has full access (*) or any of the required abilities
        $tokenAbilities = $token->abilities ?? [];
        
        if (in_array('*', $tokenAbilities)) {
            return $next($request);
        }

        // Check if token has at least one of the required abilities
        $hasAbility = false;
        foreach ($abilities as $ability) {
            if (in_array($ability, $tokenAbilities)) {
                $hasAbility = true;
                break;
            }
        }

        if (!$hasAbility) {
            return response()->json([
                'message' => 'Insufficient permissions. Required abilities: ' . implode(', ', $abilities),
            ], 403);
        }

        return $next($request);
    }
}

