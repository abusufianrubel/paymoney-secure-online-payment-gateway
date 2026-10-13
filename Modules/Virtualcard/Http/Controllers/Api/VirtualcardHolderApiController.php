<?php

namespace Modules\Virtualcard\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Controller;
use Modules\Virtualcard\Entities\VirtualcardHolder;
use Modules\Virtualcard\Events\VirtualcardApplication;
use Modules\Virtualcard\Actions\UpsertVirtualcardHolderAction;
use Modules\Virtualcard\Http\Resources\VirtuacardHolderResource;
use Modules\Virtualcard\DataTransferObject\VirtualcardHolderData;
use Modules\Virtualcard\Http\Requests\UpsertVirtualcardHolderRequest;

class VirtualcardHolderApiController extends Controller
{
    public function __construct(

        private readonly UpsertVirtualcardHolderAction $upsertCardHolder

    ) {}

    public function index()
    {
        try {
            $holders = VirtualcardHolder::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
            return $this->successResponse([
                'holders' => VirtuacardHolderResource::collection($holders)
            ]);

        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }

    }

    public function details($id)
    {
        try {
            $virtualcardHolder = VirtualcardHolder::find($id);
            if (!$virtualcardHolder) {
                return $this->notFoundResponse(__("Virtual card not found"));
            }
            return $this->successResponse(new VirtuacardHolderResource($virtualcardHolder));
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function store(UpsertVirtualcardHolderRequest $request)
    {
        try {

            if (auth()->user()->virtualcardHolders->where('status', 'Approved')->count() >= preference('holder_limit')) {
                return $this->unprocessableResponse([], __('You have reached the limit of card creation.'));
            }

            $applicationData = $this->upsert($request, new VirtualcardHolder());
            event(new VirtualcardApplication($applicationData));
            return $this->successResponse(new VirtuacardHolderResource($applicationData));
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    private function upsert (

        UpsertVirtualcardHolderRequest $request,
        VirtualcardHolder $virtualcardHolder

    ): VirtualcardHolder {
        $virtualcardHolderData = VirtualcardHolderData::fromRequest($request);
        return $this->upsertCardHolder->execute($virtualcardHolder, $virtualcardHolderData);
    }

}
