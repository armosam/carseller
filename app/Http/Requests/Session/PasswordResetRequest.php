<?php

namespace App\Http\Requests\Session;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PasswordResetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<string>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:App\Models\User,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'Provided :attribute is not allowed.',
        ];
    }
}
