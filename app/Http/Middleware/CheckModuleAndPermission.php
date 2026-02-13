<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckModuleAndPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Gate::check('module', $permission) || !Gate::check($permission)) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
