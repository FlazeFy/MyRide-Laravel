<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        foreach ($roles as $role) {
            if ($user->tokenCan($role)) return $next($request);
        }

        return response()->json([
            'message' => 'Sorry, you are not authorized to access this request'
        ], 403);
    }
}