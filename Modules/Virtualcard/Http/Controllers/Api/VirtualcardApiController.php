<?php

namespace Modules\Virtualcard\Http\Controllers\Api;

use App\Http\Controllers\{
    Controller,
    Users\EmailController
};
use Modules\Virtualcard\Http\Requests\{
    CardNumberVerifyOtpRequest,
    CardNumberRevealOtpRequest,
};
use Exception, Cache;
use Modules\Virtualcard\Entities\Virtualcard;
use Modules\Virtualcard\Http\Resources\VirtuacardResource;

class VirtualcardApiController extends Controller
{

    public function index()
    {
        try {
            $cards = Virtualcard::with([
                            'virtualcardProvider',
                            'virtualcardCategory',
                            'virtualcardHolder',
                            'virtualcardHolder.user'
                        ])
                        ->whereHas('virtualcardHolder.user', function($query) {
                            $query->where('user_id', auth()->id());
                        })
                        ->whereIn('status', ['Active', 'Inactive'])
                        ->orderBy('id', 'desc')
                        ->get();

            return $this->successResponse([
                'cards' => VirtuacardResource::collection($cards)
            ]);

        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }

    }

    public function show($id)
    {
        try {
            $virtualcard = Virtualcard::with([
                                        'virtualcardProvider',
                                        'virtualcardCategory',
                                        'virtualcardHolder',
                                        'virtualcardHolder.user'
                                    ])
                                    ->whereHas('virtualcardHolder.user', function($query) {
                                        $query->where('user_id', auth()->id());
                                    })
                                    ->find($id);
            if (!$virtualcard) {
                return $this->notFoundResponse(__("Virtual card not found"));
            }
            return $this->successResponse(new VirtuacardResource($virtualcard));
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function details($id)
    {
        try {
            $virtualcard = Virtualcard::find($id);
            if (!$virtualcard) {
                return $this->notFoundResponse(__("Virtual card not found"));
            }
            if ($virtualcard->virtualcardHolder?->user_id != auth()->id() || $virtualcard->status == 'Pending') {
                return $this->unauthorizedResponse([], __('You are not authorized to view this data'));
            }

            $transactions = \App\Models\Transaction::where(function($query) use ($virtualcard) {
                            $query->whereHas('virtualcardWithdrawal', function ($query) use ($virtualcard) {
                                // Filter related virtualcard_withdrawals based on virtualcard_id and user_id
                                $query->where('virtualcard_id', $virtualcard->id)
                                    ->where('user_id', auth()->id());
                            })
                            ->orWhereHas('virtualcardTopup', function ($query) use ($virtualcard) {
                                // Filter related virtualcard_topups based on virtualcard_id and user_id
                                $query->where('virtualcard_id', $virtualcard->id)
                                    ->where('user_id', auth()->id());
                            });
                        })
                        ->whereIn('transaction_type_id', [Virtualcard_Topup, Virtualcard_Withdrawal])
                        ->with(['virtualcardWithdrawal', 'virtualcardTopup'])
                        ->orderBy('id', 'desc')
                        ->take(10)
                        ->get();

            return $this->successResponse([
                'virtualcard'  => new VirtuacardResource($virtualcard)
            ]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function sendOtp(CardNumberRevealOtpRequest $request)
    {
        try {
            if (!Virtualcard::where('id', $request->virtualcardId)->exists()) {
                return $this->notFoundResponse(__("Virtual card not found"));
            }
            $otp = rand(100000, 999999);
            $expiresAt = now()->addMinutes(1);
            Cache::put('otp_' . $request->virtualcardId, $otp, $expiresAt);
            $message = __("Your OTP is: :x. This OTP is valid for the next 1 minutes.", ['x' => $otp]);

            // Send OTP via SMS
            if (auth()->user()->formattedPhone) {
                sendSMS(auth()->user()->formattedPhone, $message);
            }

            // Send OTP via Email
            (new EmailController)->sendEmail(auth()->user()->email, __('Show Card OTP'), $message);

            return $this->successResponse(['success' => true, 'otp' => $otp, 'message' => $message]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }

    public function verifyOtp(CardNumberVerifyOtpRequest $request)
    {
        try {
            $cardId = $request->virtualcardId;
            $storedOtp = Cache::get('otp_' . $cardId);
            if (is_null($storedOtp)) {
                return $this->unprocessableResponse([], __("OTP is already expired"));
            }

            if ($storedOtp != $request->otp) {
                return $this->unprocessableResponse([], __("OTP is not correct"));
            }

            $virtualcard = Virtualcard::find($cardId, ['id', 'card_number', 'card_cvc']);
            if (empty($virtualcard)) {
                return $this->notFoundResponse(__("Virtual card not found"));
            }

            return $this->successResponse([
                'success' => true,
                'cardNumber' => $virtualcard->card_number,
                'virtualcardId' => $cardId,
                'cvc' => $virtualcard->card_cvc
            ]);
        } catch (Exception $e) {
            return $this->unprocessableResponse([], $e->getMessage());
        }
    }
}
