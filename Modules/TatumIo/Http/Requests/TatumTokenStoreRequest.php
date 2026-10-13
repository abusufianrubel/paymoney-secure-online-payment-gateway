<?php

namespace Modules\TatumIo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class TatumTokenStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'name' => 'required|max:50|unique:crypto_tokens',
          'network' => 'required|max:10',
          'logo' => 'image|mimes:jpeg,png,jpg,bmp,ico|max:1024',
          'symbol' => 'required|max:5|not_in:USDT,USDC|unique:crypto_tokens',
          'decimals' => 'required|integer|max:8',
          'total_supply' =>'required|integer',
          'status' => 'required|max:8',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('Please provide a crypto network name'),
            'network.required' => __('Please provide a crypto network code'),
            'symbol.not_in' => 'The :attribute is reserved for stabletoken.',
        ];
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
}
