<?php

namespace Modules\Virtualcard\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoSpecialCharacters implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/[\'^£$%&*()}{@#~?><>,.|=_+¬\-!\";:`\[\]\/\\\\]/', $value)) {
            $fail(__("The :x cannot contain special characters.", ['x' => $attribute]));
        }
    }
}
