<?php

namespace Modules\Virtualcard\Http\Requests\User;

use App\Models\{
    User,
    Wallet
};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Virtualcard\Entities\Virtualcard;

class CreateVirtualcardWithdrawalRequest extends FormRequest
{
    public function getVirtualcard(): Virtualcard
    {
        return Virtualcard::where('id', $this->virtualcardId)->first();
    }

    public function getTheUser(): User
    {
        return User::where('id', $this->userId)->first();
    }

    public function getWallet(): Wallet
    {
        return Wallet::with('currency:id,code,type,symbol')->where(['id' => $this->withdrawalWallet, 'user_id' => $this->userId])->first();
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'virtualcardId' => 'required|exists:virtualcards,id',
            'userId' => 'required|exists:users,id',
            'requestedFund' => 'required|numeric',
            'percentageFees' => 'sometimes|required',
            'fixedFees' => 'sometimes|required',
            'fundRequestTime' => 'sometimes|required',
            'fundReleaseTime' => 'sometimes|required',
            'cancellationReason' => 'nullable|max:191',
            'fundApprovalStatus' => 'sometimes|required',
            'withdrawalWallet' => [
                'required',
                Rule::exists('wallets', 'id')->where(function($query) {
                    $query->where('user_id', $this->input('userId'));
                })
            ]
        ];

        if ($this->routeIs('user.virtualcard_withdrawal.validate_amount') || $this->routeIs('user.virtualcard_withdrawal.validate_amount_limit')) {
            return [];
        }
        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'requestedFund' => __('The amount field is required.'),
            'virtualcardId' => __('Please select a virtual card.')
        ];
    }
}
