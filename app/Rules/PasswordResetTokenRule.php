<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\PotentiallyTranslatedString;

class PasswordResetTokenRule implements ValidationRule
{
    protected ?string $email;

    /**
     * Constructor receives the email to check the token against.
     */
    public function __construct(?string $email)
    {
        $this->email = $email;
    }

    /**
     * Run the validation rule to check password reset token.
     * It verifies that token exists in the table and not expired (60 mins default)
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Step 1: Validate the email format and presence before using it
        $emailValidator = Validator::make(
            ['email' => $this->email],
            ['email' => 'required|email|exists:password_reset_tokens,email'],
        );

        if ($emailValidator->fails()) {
            return;
        }

        // Step 2: Look for the password reset record for this email
        $record = DB::table('password_reset_tokens')->where('email', $this->email)->first();

        // Step 3: Check the token against the hashed version
        // If record doesn't exist or token doesn't match or expired (default configured expiration time)
        if (! $record
            || ! Hash::check($value, $record->token)
            || Carbon::parse($record->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()
        ) {
            $fail('The password reset token is invalid or has expired.');
        }
    }
}
