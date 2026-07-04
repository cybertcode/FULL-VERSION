<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        $min = (int) (function_exists('setting') ? setting('password_min_length', 8) : 8);
        $requireMixed = function_exists('setting') ? (bool) setting('password_require_mixed', false) : false;
        $requireNumbers = function_exists('setting') ? (bool) setting('password_require_numbers', false) : false;
        $requireSymbols = function_exists('setting') ? (bool) setting('password_require_symbols', false) : false;
        $uncompromised = function_exists('setting') ? (bool) setting('password_check_breach', false) : false;

        $rule = Password::min(max($min, 6));

        if ($requireMixed) {
            $rule = $rule->mixedCase();
        }
        if ($requireNumbers) {
            $rule = $rule->numbers();
        }
        if ($requireSymbols) {
            $rule = $rule->symbols();
        }
        if ($uncompromised) {
            $rule = $rule->uncompromised();
        }

        return ['required', 'string', $rule, 'confirmed'];
    }
}
