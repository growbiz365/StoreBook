<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // Ensure we are not rate-limited before trying to authenticate
        $this->ensureIsNotRateLimited();

        // Check for user based on either email or username
        $user = User::where('email', $this->login)
            ->orWhere('username', $this->login)
            ->first();

        // If no user is found or password doesn't match, hit the rate limiter and throw validation error
        if (!$user || !Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'), // Correct translation syntax
            ]);
        }

        // Check if any of the user's businesses are suspended
        // Skip suspension check for Super Admin users
        if (!$user->hasRole('Super Admin')) {
            $suspendedBusinesses = $user->businesses()->where('is_suspended', true)->get();
            if ($suspendedBusinesses->isNotEmpty()) {
                // Store suspended business info in session for the suspension page
                session(['suspended_businesses' => $suspendedBusinesses->pluck('business_name')->toArray()]);
                
                // Redirect to suspension page instead of throwing validation error
                throw ValidationException::withMessages([
                    'login' => 'suspended', // Special flag to indicate suspension
                ]);
            }
        }

        // Check if the user account itself is suspended
        if ($user->isSuspended()) {
            // Store user suspension info in session for the suspension page
            session(['user_suspended' => true]);
            session(['user_suspension_reason' => $user->suspension_reason]);
            session(['user_suspended_at' => $user->suspended_at]);
            session(['user_suspended_by' => $user->suspendedBy ? $user->suspendedBy->name : 'Administrator']);
            
            // Redirect to suspension page instead of throwing validation error
            throw ValidationException::withMessages([
                'login' => 'user_suspended', // Special flag to indicate user suspension
            ]);
        }

        // Login the user if credentials are valid and clear the rate limiter
        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }



    /* public function authenticate(): void
     {
         $this->ensureIsNotRateLimited();

         if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
             RateLimiter::hit($this->throttleKey());

             throw ValidationException::withMessages([
                 'email' => trans('auth.failed'),
             ]);
         }

         RateLimiter::clear($this->throttleKey());
     }*/

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
