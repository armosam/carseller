<?php

namespace App\Http\Requests\Session;

use App\Rules\PasswordResetTokenRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StorePasswordResetRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:password_reset_tokens,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'token' => new PasswordResetTokenRule($this->input('email')),
        ];
    }
    public function messages(): array
    {
        return [
            'email.exists' => trans('validation.exists'),
        ];
    }
}
