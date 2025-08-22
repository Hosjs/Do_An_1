<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        $roleList = collect($roles)
            ->flatMap(function ($role) {
                return explode('|', $role);
            })->unique();

        if (!$user || !$user->roles->pluck('name')->intersect($roleList)->count()) {
            return response()->json([
                'message' => 'Không có quyền truy cập',
                'your_roles' => $user->roles->pluck('name'),
            ], 403);
        }

        return $next($request);
    }
}