<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Token abilities/scopes available
     */
    protected const TOKEN_ABILITIES = [
        'tasks:read' => 'Read tasks',
        'tasks:write' => 'Create and update tasks',
        'tasks:delete' => 'Delete tasks',
        'categories:read' => 'Read categories',
        'categories:write' => 'Create and update categories',
        'categories:delete' => 'Delete categories',
    ];

    /**
     * Login and create API token with scopes
     * 
     * @bodyParam email string required User email address. Example: user@example.com
     * @bodyParam password string required User password. Example: password123
     * @bodyParam device_name string optional Device name for the token. Example: My iPhone
     * @bodyParam abilities array optional Array of token abilities. Example: ["tasks:read", "tasks:write"]
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string|max:255',
            'abilities' => 'nullable|array',
            'abilities.*' => 'string|in:' . implode(',', array_keys(self::TOKEN_ABILITIES)),
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Token scopes - use provided abilities or default to full access
        $abilities = $request->abilities ?? ['*']; // Full access by default

        $tokenName = $request->device_name ?? 'api-token';
        $token = $user->createToken($tokenName, $abilities);

        return response()->json([
            'user' => $user->only(['id', 'name', 'username', 'email']),
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
        ], 200);
    }

    /**
     * Get authenticated user
     * 
     * Requires: Bearer token authentication
     */
    public function user(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'username', 'email']),
            'token_abilities' => $token?->abilities ?? [],
            'auth_type' => $token ? 'token' : 'session',
        ]);
    }

    /**
     * Get all tokens for authenticated user
     * 
     * Requires: Bearer token authentication
     */
    public function tokens(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at?->toDateTimeString(),
                'expires_at' => $token->expires_at?->toDateTimeString(),
                'created_at' => $token->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'tokens' => $tokens,
            'available_abilities' => self::TOKEN_ABILITIES,
        ]);
    }

    /**
     * Revoke current token
     * 
     * Requires: Bearer token authentication
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Token revoked successfully.',
        ]);
    }

    /**
     * Revoke all tokens for authenticated user
     * 
     * Requires: Bearer token authentication
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'All tokens revoked successfully.',
        ]);
    }

    /**
     * Revoke specific token by ID
     * 
     * Requires: Bearer token authentication
     * 
     * @urlParam tokenId integer required Token ID to revoke. Example: 1
     */
    public function revokeToken(Request $request, int $tokenId): JsonResponse
    {
        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return response()->json([
                'message' => 'Token not found.',
            ], 404);
        }

        $token->delete();

        return response()->json([
            'message' => 'Token revoked successfully.',
        ]);
    }

    /**
     * Get available token abilities/scopes
     */
    public function abilities(): JsonResponse
    {
        return response()->json([
            'abilities' => self::TOKEN_ABILITIES,
        ]);
    }
}
