<?php

namespace Modules\Virtualcard\Http\Controllers\Api;

use Modules\Virtualcard\Entities\{
    Virtualcard,
    WebhookTransaction
};
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Modules\Virtualcard\Http\Resources\WebhookTransactionResource;
use Modules\Virtualcard\Http\Resources\VirtuacardTransactionResource;

class VirtualcardTransactionApiController extends Controller
{
    public function index($virtualcardId) 
    {
         try {

            $virtualcard = Virtualcard::find($virtualcardId);

            if (!$virtualcard) {
                return $this->notFoundResponse(__('The card does not found.'));
            }

            if ($virtualcard->virtualcardProvider?->type == 'Manual') {

                $transactions = Transaction::where(function($query) use ($virtualcard) {
                        $query->whereHas('virtualcardWithdrawal', function ($query) use ($virtualcard) {
                            $query->where('virtualcard_id', $virtualcard->id)
                                ->where('user_id', auth()->id());
                        })
                        ->orWhereHas('virtualcardTopup', function ($query) use ($virtualcard) {
                            $query->where('virtualcard_id', $virtualcard->id)
                                ->where('user_id', auth()->id());
                        });
                    })
                    ->whereIn('transaction_type_id', [Virtualcard_Topup, Virtualcard_Withdrawal])
                    ->orderBy('id', 'desc')
                    ->take(10)
                    ->get();

                $transactionResources = VirtuacardTransactionResource::collection($transactions);

            } else {

                $transactions = WebhookTransaction::with('virtualcard')->where('virtualcard_id', $virtualcard->id)
                                    ->where('user_id', auth()->id())
                                    ->orderBy('id', 'desc')
                                    ->take(10)
                                    ->get();
                

                $transactionResources = WebhookTransactionResource::collection($transactions);
            }

            return $this->successResponse([
                'transactions' => $transactionResources
            ]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function virtualcardTransactionDetails($virtualcardId, $transactionId)
    {
        try {

            $virtualcard = Virtualcard::find($virtualcardId);

            if (!$virtualcard) {
                return $this->notFoundResponse(__('The card does not found.'));
            }

            if ($virtualcard->virtualcardProvider?->type == 'Manual') {

                $transactions = Transaction::where(function($query) use ($virtualcard) {
                        $query->whereHas('virtualcardWithdrawal', function ($query) use ($virtualcard) {
                            $query->where('virtualcard_id', $virtualcard->id)
                                ->where('user_id', auth()->id());
                        })
                        ->orWhereHas('virtualcardTopup', function ($query) use ($virtualcard) {
                            $query->where('virtualcard_id', $virtualcard->id)
                                ->where('user_id', auth()->id());
                        });
                    })
                    ->where('id', $transactionId)
                    ->whereIn('transaction_type_id', [Virtualcard_Topup, Virtualcard_Withdrawal])
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$transactions) {
                    return $this->notFoundResponse(__('The transaction does not found.'));
                }

                $transactionResources = new VirtuacardTransactionResource($transactions);

            } else {
            
                $transactions = WebhookTransaction::with('virtualcard')->where('virtualcard_id', $virtualcard->id)
                                    ->where('user_id', auth()->id())
                                    ->orderBy('id', 'desc')
                                    ->where('id', $transactionId)
                                    ->first();

                if (!$transactions) {
                    return $this->notFoundResponse(__('The transaction does not found.'));
                }
                
                $transactionResources = new WebhookTransactionResource($transactions);
            }

            return $this->successResponse([
                'transactions' => $transactionResources
            ]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }
}