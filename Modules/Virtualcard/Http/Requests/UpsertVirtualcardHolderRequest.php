<?php

namespace Modules\Virtualcard\Http\Requests;

use Modules\Virtualcard\Enums\{
    Genders,
    CardHolderTypes,
    VerificationDocumentTypes
};
use App\Models\User;
use App\Rules\CheckValidFile;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpsertVirtualcardHolderRequest extends FormRequest
{
    public function getTheUser(): User
    {
        return User::where('id', $this->userId)->firstOrFail();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'userId' => ['required', 'exists:users,id'],
            'cardHolderType' =>[
                'required',
                new Enum(CardHolderTypes::class),
            ],
            'businessName' => 'required_if:cardHolderType,' . CardHolderTypes::BUSINESS->value . '|max:30|min:3',
            'businessIdNumber' => 'required_if:cardHolderType,' . CardHolderTypes::BUSINESS->value . '|max:50|min:3',

            'gender' => [
                'required_if:cardHolderType,' . CardHolderTypes::INDIVIDUAL->value,
                new Enum(Genders::class),
                'string',
                'max:11'
            ],
            'dateOfBirth' => [
                'required_if:cardHolderType,' . CardHolderTypes::INDIVIDUAL->value,
            ],
            'verificationDocumentType' => [
                'required_if:cardHolderType,' . CardHolderTypes::INDIVIDUAL->value,
                new Enum(VerificationDocumentTypes::class),
                'max:30',
                "string"
            ],
            'verificationDocumentIdNumber' => [
                'required_if:cardHolderType,' . CardHolderTypes::INDIVIDUAL->value,
                'max:30'
            ],
            'verificationDocumentImageFront' => [
                'required_if:cardHolderType,' . CardHolderTypes::INDIVIDUAL->value,
                new CheckValidFile(getFileExtensions(6), true)
            ],
            'verificationDocumentImageBack' => [
                'required_if:cardHolderType,' . CardHolderTypes::INDIVIDUAL->value,
                new CheckValidFile(getFileExtensions(6), true)
            ],

            'firstName' => 'required|alpha_num|max:30',
            'lastName' => 'required|alpha_num|max:30',
            'country' => 'required|string|max:60',
            'state' => 'nullable|max:2',
            'city' => 'required|string|max:60',
            'postalCode' => 'required|string|max:30',
            'address' => 'required|max: 50',
            'status' => 'sometimes|required'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
