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
        if (!$request->user()) {
            return redirect('http://project.test');
        }

        $userRole = $request->user()->vai_tro;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        return redirect('http://project.test')->with('error', 'Bạn không có quyền truy cập trang này.');
    }
}
