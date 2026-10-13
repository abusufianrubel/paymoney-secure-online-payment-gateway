<?php

namespace Modules\Virtualcard\Actions;

use Modules\Virtualcard\Entities\VirtualcardHolder;
use Modules\Virtualcard\DataTransferObject\VirtualcardHolderData;

class UpsertVirtualcardHolderAction
{
    public function execute(VirtualcardHolder $virtualcardHolder, VirtualcardHolderData $virtualcardHolderData): VirtualcardHolder
    {
        if ($virtualcardHolderData->verificationDocumentImageFront) {
            $frontImageResponse = uploadImage($virtualcardHolderData->verificationDocumentImageFront, 'Modules/Virtualcard/Resources/assets/attachments/virtual_card_holders/');

            if ($frontImageResponse['status'] === true) {
                $virtualcardHolder->verification_document_image_front = $frontImageResponse['file_name'];
            }
        }

        if ($virtualcardHolderData->verificationDocumentImageBack) {
            $backImageResponse = uploadImage($virtualcardHolderData->verificationDocumentImageBack, 'Modules/Virtualcard/Resources/assets/attachments/virtual_card_holders/');

            if ($backImageResponse['status'] === true) {
                $virtualcardHolder->verification_document_image_back = $backImageResponse['file_name'];
            }
        }

        $virtualcardHolder->user_id = $virtualcardHolderData->user?->id;
        $virtualcardHolder->card_holder_type = $virtualcardHolderData->cardHolderType;
        $virtualcardHolder->business_name = $virtualcardHolderData->businessName;
        $virtualcardHolder->business_id_number = $virtualcardHolderData->businessIdNumber;
        $virtualcardHolder->gender = $virtualcardHolderData->gender;
        $virtualcardHolder->date_of_birth = setDateForDb($virtualcardHolderData->dateOfBirth);
        $virtualcardHolder->verification_document_type = $virtualcardHolderData->verificationDocumentType;
        $virtualcardHolder->postal_code = $virtualcardHolderData->postalCode;
        $virtualcardHolder->address = $virtualcardHolderData->address;
        $virtualcardHolder->verification_document_id_number = $virtualcardHolderData->verificationDocumentIdNumber;
        $virtualcardHolder->first_name = $virtualcardHolderData->firstName;
        $virtualcardHolder->last_name = $virtualcardHolderData->lastName;
        $virtualcardHolder->country = $virtualcardHolderData->country;
        $virtualcardHolder->state = strtoupper($virtualcardHolderData->state);
        $virtualcardHolder->city = $virtualcardHolderData->city;
        $virtualcardHolder->save();

        return $virtualcardHolder;
    }

}
