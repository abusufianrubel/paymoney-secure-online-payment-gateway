<?php

namespace Modules\Virtualcard\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VirtuacardHolderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'user' => [
                'id' => $this->user->id ?? null,
                'first_name' => $this->user->first_name ?? null,
                'last_name'  => $this->user->last_name ?? null
            ],
            'card_holder_type'   => $this->card_holder_type,
            'business_name'      => $this->business_name,
            'business_id_number' => $this->business_id_number,
            'gender'             => $this->gender,
            'date_of_birth'      => $this->date_of_birth,
            'verification_document_type'      => $this->verification_document_type,
            'verification_document_id_number' => $this->verification_document_id_number,
            'verification_document_image_front'     => $this->verification_document_image_front ? asset('Modules/Virtualcard/Resources/assets/attachments/virtual_card_holders/'. $this->verification_document_image_front) : '',
            'verification_document_image_back'     => $this->verification_document_image_back ? asset('Modules/Virtualcard/Resources/assets/attachments/virtual_card_holders/'. $this->verification_document_image_back) : '',
            'first_name'         => $this->first_name,
            'last_name'          => $this->last_name,
            'country'            => $this->country,
            'state'              => $this->state,
            'city'               => $this->city,
            'postal_code'        => $this->postal_code,
            'address'            => $this->address,
            'status'             => $this->status,
            'created_at'         => dateFormat($this->created_at),
            'updated_at'         => dateFormat($this->updated_at),
        ];

    }

}
