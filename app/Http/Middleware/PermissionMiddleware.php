<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class PermissionMiddleware {
    public function handle(Request $request, Closure $next, ...$permissions) {
        $user = $request->user();
        if (!$user || !$user->active) abort(403);
        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) abort(403);
        }
        return $next($request);
    }
}
