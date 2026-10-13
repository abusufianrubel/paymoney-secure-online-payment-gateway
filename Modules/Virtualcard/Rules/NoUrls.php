<?php

namespace Modules\Virtualcard\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoUrls implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/\b(?:https?|ftp):\/\/\S+\b/', $value)) {
            $fail(__("The :x field should not contain URLs.", ['x' => $attribute]));
        }
    }
}
