<?php

/**
 * @package TokenSendRequest
 * @author tehcvillage <support@techvill.org>
 * @contributor Ashraful Rasel <[ashraful.techvill@gmail.com]>
 * @created 12-12-2022
 */

 namespace Modules\TatumIo\Http\Requests;

use App\Http\Requests\CustomFormRequest;

class TokenSendRequest extends CustomFormRequest
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
            'receiverAddress' => 'required',
            'amount' => 'required',
            'walletCurrencyCode' => 'required',
            'network' => 'required'
        ];
    }


    public function messages()
    {
        return [
            'receiverAddress' => __('Receiver Address'),
            'senderAddress' => __('Sender Address'),
            'walletCurrencyCode' => __('Network'),
            'network' => __('Network'),
            'amount' => __('Amount'),
        ];
    }
}
