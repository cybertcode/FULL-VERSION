<?php

namespace App\Http\Requests\Admin\Notification;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BroadcastNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:1000'],
            'audience' => ['required', Rule::in(['all', 'role'])],
            'role' => ['required_if:audience,role', 'nullable', 'string', Rule::exists('roles', 'name')],
            'send_email' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'título',
            'message' => 'mensaje',
            'audience' => 'destinatarios',
            'role' => 'rol',
        ];
    }
}
