<?php

namespace Modules\KycVerification\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class IdentityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'verification_type' => $this->verification_type ?? null,
            'existing_file_id' => $this->file_id ?? null,
            'identity_type' => $this->identity_type ?? null,
            'identity_number' => $this->identity_number ?? null,
            'verification_file' => $this->verificationFile(),
            'status' => $this->status ?? null,
        ];
    }

    private function verificationFile()
    {
        return !empty($this->file_id) && file_exists('public/' . config('kycverification.kyc_document_path') . 'identity-proof-files/'. $this->file?->filename)
            ? asset('public/' . config('kycverification.kyc_document_path') . 'identity-proof-files/'. $this->file?->filename)
            : null;
    }
}
