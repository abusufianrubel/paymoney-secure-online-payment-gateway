<?php

namespace Modules\KycVerification\Processor;

use Exception;
use Illuminate\View\View;
use App\Rules\CheckValidFile;
use Illuminate\Support\Facades\DB;
use App\Services\KycVerificationService;
use Modules\KycVerification\Entities\KycProvider;
use App\Models\{User, File, DocumentVerification};
use Modules\KycVerification\Transformers\AddressResource;
use Modules\KycVerification\Contract\VerificationContract;
use Modules\KycVerification\Transformers\IdentityResource;

class ManualVerificationProcessor extends VerificationContract
{
    /**
     * Get the view for manual identity verification.
     */
    public function getUserVerificationView(): View|string
    {
        return view(
            'kycverification::user.verification.identity',
            $this->verificationViewData('identity')
        );
    }

    /**
     * Process manual identity verification.
     *
     * This method will update the user verification status to 'pending' and
     * upload the verification file to the server. If the upload fails, it
     * will rollback the transaction and return an error message.
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function processUserVerification($request): array
    {
        try {
            DB::beginTransaction();

            // Update the user's identity verification status to false
            $user = User::find(auth()->id());
            $user->identity_verified = false;
            $user->save();

            // Process the verification file
            $fileId = $this->processVerificationFile('identity');

            // Get the verification record or create a new one
            $verification = DocumentVerification::where([
                'user_id' => auth()->id(),
                'verification_type' => 'identity',
            ])->first() ?? new DocumentVerification();

            // Update the verification record
            $verification->user_id = auth()->id();
            $verification->provider_id = $request->provider_id;

            if (! empty($request->verification_file)) {
                $verification->file_id = $fileId;
            }

            $verification->verification_type = 'identity';
            $verification->identity_type = $request->identity_type;
            $verification->identity_number = $request->identity_number;
            $verification->status = 'pending';
            $verification->save();

            // Commit the transaction
            DB::commit();

            return [
                'status' => 'success',
                'message' => __('User verification updated successfully'),
                'url' => route('user.kyc.verifications.initiate'),
            ];
        } catch (Exception $exception) {

            // Rollback the transaction on error
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => $exception->getMessage(),
                'url' => route('user.kyc.verifications.initiate'),
            ];
        }
    }

    /**
     * Get the admin verification view.
     * @param App\Models\DocumentVerification $verification
     */
    public function adminVerificationView($verification): View|string
    {
        $data = [
            'menu' => 'kyc_verification',
            'sub_menu' => 'kyc_verifications',
            'verification' => $verification
        ];

        return view('kycverification::admin.verifications.edit', $data);
    }

    /**
     * Update admin verification.
     * @param Illuminate\Http\Request $request
     * @param App\Models\DocumentVerification $verification
     * @return bool
     */
    public function updateAdminVerification($request, $verification): array
    {
        return(new KycVerificationService())->updateKycVerification($verification, $request);
    }

    /**
     * Get the validation rules that apply to the user KYC verification
     */
    public function userVerificationRules(): array
    {
        return [
            'identity_type' => 'required',
            'identity_number' => 'required',
            //the supported files extension for verification file will be ‘jpg', 'jpeg', 'png', 'gif', 'bmp', 'pdf' and by using 8 these value will be returned.
            'verification_file' => ['required', new CheckValidFile(getFileExtensions(8))]
        ];
    }

    /**
     * Get custom attributes for validator errors for user KYC verification.
     */
    public function userVerificationAttributes(): array
    {
        return [
            'identity_type' => __('Identity Type'),
            'identity_number' => __('Identity Number'),
            'verification_file' => __('Identity File')
        ];
    }

    /**
     * Get the module alias
     * @return string
     */
    public function moduleAlias(): string
    {
        return 'kycverification';
    }

    /**
     * Process manual verification file.
     *
     * This method takes the uploaded verification file, stores it in the files
     * database table and returns the id of the newly inserted file.
     *
     * If the user already has a file for the given type (address or identity),
     * the old file is removed after the new file is inserted.
     *
     * @param string $type Type of verification file
     * @return int The id of the newly inserted file
     */
    private function processVerificationFile($type)
    {
        $oldFileName = File::where('id', request()->existing_file_id)->value('filename');
        $fileId = $this->insertUserVerificationFile($type);

        // If the user already has a file for the given type, remove the old file
        // after the new file is inserted.
        if ($fileId && $oldFileName != null) {
            $location = public_path(getKycDocumentPath($type) . $oldFileName);

            if (file_exists($location)) {
                unlink($location);
            }
        }

        return $fileId;
    }

    /**
     * Process manual verification file and store it into files database table.
     *
     * This method handles the uploaded verification file by validating its format,
     * moving it to the appropriate directory, and saving the file information to
     * the database. It returns the ID of the file entry in the database.
     *
     * @param string $type Type of verification file ('address' or 'identity')
     * @return int The ID of the inserted file record
     * @throws Exception If the file format is invalid
     */
    protected function insertUserVerificationFile($type)
    {
        // Retrieve the uploaded file from the request
        $fileName = request()->file('verification_file');
        $originalName = $fileName->getClientOriginalName();
        $uniqueName = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
        $fileExtension = strtolower($fileName->getClientOriginalExtension());

        // Validate file format
        if (!in_array($fileExtension, getFileExtensions(8))) {
            throw new Exception(__('Invalid File Format!'));
        }

        // Determine the upload path based on the file type
        $uploadPath = public_path(getKycDocumentPath($type));

        // Move the file to the designated directory
        $fileName->move($uploadPath, $uniqueName);

        // Retrieve existing file or create a new instance for the file record
        $file = isset(request()->existing_file_id) ? File::find(request()->existing_file_id) : new File();
        $file->user_id = auth()->id();
        $file->filename = $uniqueName;
        $file->originalname = $originalName;
        $file->type = $fileExtension;
        $file->save();

        // Return the ID of the file record
        return $file->id;
    }

    /**
     * Get the view for manual address verification.
     * @return \Illuminate\View\View|string
     */
    public function getAddressVerificationView()
    {
        return view(
            'kycverification::user.verification.address-verify',
            $this->verificationViewData('address')
        );
    }

    /**
     * Process manual address verification.
     *
     * This method sets the user's address verification status to 'pending'
     * and handles the verification file upload. It wraps the entire operation
     * in a database transaction to ensure atomicity.
     *
     * @param \Illuminate\Http\Request $request Incoming request with verification details
     * @return void
     */
    public function processAddressVerification($request)
    {
        // Begin a database transaction
        DB::transaction(function () use ($request) {
            // Retrieve the user and set address_verified to false
            $user = User::find(auth()->id(), ['id', 'address_verified']);
            $user->address_verified = false;
            $user->save();

            // Process and upload the address verification file
            $fileId = $this->processVerificationFile('address');

            // Get or create a new DocumentVerification record for the user
            $addressVerification = DocumentVerification::where([
                'user_id' => auth()->id(),
                'verification_type' => 'address'
            ])->first() ?? new DocumentVerification();

            // Set the necessary fields on the DocumentVerification record
            $addressVerification->user_id = auth()->id();
            $addressVerification->provider_id = $request->provider_id;

            // If a verification file is provided, associate it with the record
            if (!empty($request->verification_file)) {
                $addressVerification->file_id = $fileId;
            }

            // Set the verification type and status
            $addressVerification->verification_type = 'address';
            $addressVerification->status = 'pending';
            $addressVerification->save();
        });
    }

    /**
     * Get the view data for manual identity/address verification.
     * @param string $type
     * @return array
    */
    private function verificationViewData($type)
    {
        return [
            'menu' => 'profile',
            'sub_menu' => 'verification',
            'two_step_verification' => preference('two_step_verification'),
            'provider' => KycProvider::where('alias', 'manual')->first(),
            'verification' => DocumentVerification::where(['user_id' => auth()->id(), 'verification_type' => $type])->first()
        ];
    }

    public function identityRequirements()
    {
        return [
            'verification_type' => [
                'type' => 'hidden',
                'required' => true,
                'input_type' => 'text'
            ],
            'existing_file_id' => [
                'type' => 'hidden',
                'required' => false,
                'input_type' => 'text'
            ],
            'provider_id' => [
                'type' => 'hidden',
                'required' => true,
                'input_type' => 'text'
            ],
            
            'identity_type' => [
                'type' => 'visible',
                'required' => true,
                'input_type' => 'select',
                'options' => [
                    'national_id' => 'National ID',
                    'driving_license' => 'Driving License',
                    'passport' => 'Passport'
                ]
            ],
            'identity_number' => [
                'type' => 'visible',
                'required' => true,
                'input_type' => 'text'
            ],
            'verification_file' => [
                'type' => 'visible',
                'required' => true,
                'input_type' => 'file'
            ]
        ];
    }

    public function addressRequirements()
    {
        return [
            'verification_type' => [
                'type' => 'hidden',
                'required' => true,
                'input_type' => 'text'
            ],
            'existing_file_id' => [
                'type' => 'hidden',
                'required' => false,
                'input_type' => 'text'
            ],
            'provider_id' => [
                'type' => 'hidden',
                'required' => true,
                'input_type' => 'text'
            ],
            'verification_file' => [
                'type' => 'visible',
                'required' => true,
                'input_type' => 'file'
            ]
        ];
    }

    /**
     * Retrieve the user's identity or address verification data.
     * @param string $type Either 'identity' or 'address'
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function verificationData($type)
    {
        $verification = DocumentVerification::where(['user_id' => auth()->id(), 'verification_type' => $type])->first();

        $provider = KycProvider::where('is_default', 'Yes')->select('id', 'name', 'alias')->first();

        $data = [
            'provider' => $provider,
            'verification' => $type == 'address' ? new AddressResource($verification) : new IdentityResource($verification)
        ];

        if ($type == 'identity') {
            $data['identity_types'] = [
                'national_id' => 'National ID',
                'driving_license' => 'Driving License',
                'passport' => 'Passport'
            ];
        }

        return $data;
    }
}
