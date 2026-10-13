<?php

namespace Modules\Virtualcard\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Modules\Virtualcard\Entities\Virtualcard;
use Modules\Virtualcard\DataTransferObject\VirtualcardData;

class UpsertVirtualcardAction
{
    public function execute(Virtualcard $virtualcard, VirtualcardData $virtualcardData): Virtualcard
    {
        $this->validate($virtualcardData);

        $virtualcard->virtualcard_provider_id = $virtualcardData->virtualcardProvider?->id;
        $virtualcard->virtualcard_category_id = $virtualcardData->virtualcardCategory?->id;
        $virtualcard->virtualcard_holder_id = $virtualcardData->virtualcardHolder?->id;
        $virtualcard->card_type = $virtualcardData->cardType ?? 'Virtual';
        $virtualcard->card_brand = $virtualcardData->cardBrand;
        $virtualcard->card_number = str_replace(' ', '', $virtualcardData->cardNumber);
        $virtualcard->card_cvc = $virtualcardData->cardCvc;
        $virtualcard->currency_code = $virtualcardData->currencyCode;
        $virtualcard->amount = $virtualcardData->amount;
        $virtualcard->expiry_month = Carbon::parse($virtualcardData->cardExpiryDate)->format('m');
        $virtualcard->expiry_year = Carbon::parse($virtualcardData->cardExpiryDate)->format('Y');
        $virtualcard->status = $virtualcardData->status ?? 'Active';
        $virtualcard->save();

        $virtualcard->virtualcardHolder->update(['status' => 'Approved']);

        return $virtualcard;
    }

    private function validate(VirtualcardData $virtualcardData): void
    {
        $rules = [
            'cardNumber' => [
                'required',
                'string',
                'max:19',
                'min:13',
                'regex:/^[0-9]{13,19}$/'
            ],
            'cardCvc' => [
                'required', 'digits_between:3,4',
            ],
        ];

        $validator = Validator::make([
            'cardNumber' => str_replace(' ', '', $virtualcardData->cardNumber),
            'cardCvc' => $virtualcardData->cardCvc
        ], $rules, $this->messages());

        $validator->validate();
    }

    public function messages()
    {
        return [
            'card_number.regex' => __('Card number should only contain numbers.'),
            'card_number.min' => __('Card number must be at least 13 digits.'),
            'card_number.max' => __('Card number must be at most 19 digits.'),
            'cvc.digits_between' => __('CVC must be between 3 and 4 digits.'),
        ];
    }

}
