<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Check if the error is due to business suspension
            if ($e->errors()['login'][0] === 'suspended') {
                return redirect()->route('suspended');
            }
            // Check if the error is due to user suspension
            if ($e->errors()['login'][0] === 'user_suspended') {
                return redirect()->route('user-suspended');
            }
            // Re-throw other validation errors
            throw $e;
        }

        $request->session()->regenerate();

        // Set the user's first business as active business if not already set
        $user = Auth::user();
        if ($user && !session('active_business')) {
            $firstBusiness = $user->businesses()->first();
            if ($firstBusiness) {
                session(['active_business' => $firstBusiness->id]);
                \Log::info('Auto-set active business on login', [
                    'user_id' => $user->id,
                    'business_id' => $firstBusiness->id,
                    'business_name' => $firstBusiness->business_name
                ]);
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
