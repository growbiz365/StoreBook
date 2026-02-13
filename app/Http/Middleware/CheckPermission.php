<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();

        // Check if the user has the required permission
        if ($user && $user->can($permission)) {
            return $next($request);
        }

        // Abort with a 403 Forbidden response if permission is not granted
        return abort(403, 'You do not have permission to access this page.');
    }
}
