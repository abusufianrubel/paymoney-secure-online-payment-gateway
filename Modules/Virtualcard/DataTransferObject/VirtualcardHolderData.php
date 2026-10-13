<?php

namespace Modules\Virtualcard\DataTransferObject;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Modules\Virtualcard\Http\Requests\UpsertVirtualcardHolderRequest;

class VirtualcardHolderData
{
    public function __construct (
        public readonly User $user,
        public readonly string $cardHolderType,
        public readonly ?string $businessName,
        public readonly ?string $businessIdNumber,
        public readonly ?string $gender,
        public readonly ?string $dateOfBirth,
        public readonly ?string $verificationDocumentType,
        public readonly ?string $verificationDocumentIdNumber,
        public readonly ?UploadedFile $verificationDocumentImageFront,
        public readonly ?UploadedFile $verificationDocumentImageBack,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $country,
        public readonly ?string $state,
        public readonly string $city,
        public readonly string $postalCode,
        public readonly string $address,
        public readonly ?string $status
    ) {}

public static function fromRequest(UpsertVirtualcardHolderRequest $request): self
    {
        return new static(
            $request->getTheUser(),
            $request->cardHolderType,
            $request->businessName,
            $request->businessIdNumber,
            $request->gender,
            $request->dateOfBirth,
            $request->verificationDocumentType,
            $request->verificationDocumentIdNumber,
            $request->verificationDocumentImageFront,
            $request->verificationDocumentImageBack,
            $request->firstName,
            $request->lastName,
            $request->country,
            $request->state,
            $request->city,
            $request->postalCode,
            $request->address,
            $request->status
        );
    }
}
