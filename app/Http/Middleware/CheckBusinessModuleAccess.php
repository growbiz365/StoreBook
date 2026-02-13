<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Business;

class CheckBusinessModuleAccess
{
    public function handle(Request $request, Closure $next, $moduleName)
    {
        // Get the active business from session
        $activeBusinessId = session('active_business'); // Assuming active business is set in session
        $business = Business::find($activeBusinessId);

        if (!$business) {
            return redirect()->route('home')->with('error', 'No active business found.');
        }

        // Get the modules available for the business
        $modules = $business->modules;

        // Check if the requested module exists in the business's package
        $module = $modules->where('name', $moduleName)->first();

        if (!$module) {
            return redirect()->route('home')->with('error', 'Module not available for this business.');
        }

        // Check if the user has permission to access the module
        if (!Auth::user()->can('view ' . $moduleName)) {
            return redirect()->route('home')->with('error', 'You do not have permission to access this module.');
        }

        return $next($request);
    }
} 