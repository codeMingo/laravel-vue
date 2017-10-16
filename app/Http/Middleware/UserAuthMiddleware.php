<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json([
                'status'  => 0,
                'data'    => [],
                'message' => '请登陆后操作',
            ]);
        }
        return $next($request);
    }
}
