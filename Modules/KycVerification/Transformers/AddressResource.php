<?php

namespace Modules\KycVerification\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'verification_type' => $this->verification_type ?? null,
            'existing_file_id' => $this->file_id ?? null,
            'verification_file' => $this->verificationFile(),
            'status' => $this->status ?? null,
        ];
    }

    private function verificationFile()
    {
        return !empty($this->file_id) && file_exists('public/' . config('kycverification.kyc_document_path') . 'address-proof-files/' . $this->file?->filename)
            ? asset('public/' . config('kycverification.kyc_document_path') . 'address-proof-files/' . $this->file?->filename)
            : null;
    }
}
