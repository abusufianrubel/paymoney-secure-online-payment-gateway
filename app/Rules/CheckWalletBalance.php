<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;

class CheckWalletBalance implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return false;
        }

        $request = app(\Illuminate\Http\Request::class);

        $wallet = Wallet::where(['user_id' => Auth::id(), 'currency_id' => $request->currency_id])->first();

        // Check if the wallet exists and has sufficient balance
        return !empty($wallet) && ($wallet->balance >= $request->amount);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('You do not have enough balance in your wallet.');
    }
}
