<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        Log::info('Checking role middleware', [
            'user' => $request->user(),
            'required_roles' => $roles
        ]);

        if (!$request->user()) {
            Log::warning('User not authenticated');
            return redirect('http://project.test');
        }

        $userRole = $request->user()->vai_tro;
        Log::info('User role', ['role' => $userRole]);

        if (in_array($userRole, $roles)) {
            Log::info('Role check passed', ['role' => $userRole]);
            return $next($request);
        }

        Log::warning('Role check failed', [
            'user_role' => $userRole,
            'required_roles' => $roles
        ]);

        return redirect('http://project.test')->with('error', 'Bạn không có quyền truy cập trang này.');
    }
} 