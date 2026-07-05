<?php

namespace App\Http\Requests\Frontend\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function throttleKey(): string
    {
        return mb_strtolower($this->input('email')).'|'.$this->ip();
    }

    public function ensureIsNotRateLimited(): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Intenta de nuevo en {$seconds} segundos.",
            ]);
        }
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::guard('customer')->attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
}
