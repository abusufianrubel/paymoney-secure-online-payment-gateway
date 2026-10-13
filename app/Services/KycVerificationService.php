<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\{DocumentVerification, User};
use App\Services\Sms\AddressOrIdentityVerificationSmsService;
use App\Services\Mail\AddressOrIdentityVerificationMailService;

class KycVerificationService
{
    /**
     * Process KYC verification update.
     *
     * This method updates the status of a given DocumentVerification and,
     * if approved, updates the user's identity or address verification status.
     * It also sends notification emails and SMS based on the outcome.
     *
     * @param \App\Models\DocumentVerification $verification The verification object to update.
     * @param \Illuminate\Http\Request $request The request containing the new status.
     * @return array An array containing the status and message of the operation.
     */
    public function updateKycVerification($verification, $request)
    {
        try {
            // Begin database transaction
            DB::beginTransaction();

            // Find the document verification record
            $documentVerification = DocumentVerification::find($verification->id);

            // Update the status
            $documentVerification->status = $request->status;
            $documentVerification->save();

            // If the verification is approved, update the user's verification status
            if ($request->status == 'approved') {
                $user = User::find($verification->user_id);
                // Update identity verification status if applicable
                $user->identity_verified = $verification->verification_type == 'identity' ? true : $user->identity_verified;
                // Update address verification status if applicable
                $user->address_verified = $verification->verification_type == 'address' ? true : $user->address_verified;
                $user->save();
            }

            // Commit the transaction
            DB::commit();

            // Prepare and send notifications if a user exists
            if (!empty($user)) {
                $data = [
                    'status' => $request->status,
                    'type' => ucwords($verification->verification_type),
                ];

                // Send email notification
                (new AddressOrIdentityVerificationMailService)->send($user, $data);

                // Send SMS notification if the user's phone is formatted
                if (!empty($user->formattedPhone)) {
                    (new AddressOrIdentityVerificationSmsService)->send($user, $data);
                }
            }

            // Return success response
            return [
                'status' => true,
                'message' => __('The :x has been successfully verified.', ['x' => $verification->verification_type])
            ];
        } catch (Exception $exception) {
            // Rollback transaction in case of an error
            DB::rollBack();

            // Return error response
            return [
                'status' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }
}
