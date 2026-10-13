<?php
namespace Modules\Virtualcard\Http\Requests\User;

use App\Http\Requests\CustomFormRequest;

class ValidateTopupRequest extends CustomFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'virtualCardId'  => 'required|numeric|min:0|not_in:0',
            'amount'       => 'required|numeric',
            'topupWallet' => 'required',
        ];
    }
}
