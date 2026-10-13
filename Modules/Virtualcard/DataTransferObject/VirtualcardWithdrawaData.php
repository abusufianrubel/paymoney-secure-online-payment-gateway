<?php

namespace Modules\Virtualcard\DataTransferObject;

use App\Models\{
    User,
    Wallet
};
use Modules\Virtualcard\Entities\Virtualcard;
use Modules\Virtualcard\Http\Requests\User\CreateVirtualcardWithdrawalRequest;

class VirtualcardWithdrawaData
{
    public function __construct (
        public readonly Virtualcard $virtualcard,
        public readonly User $user,
        public readonly Wallet $wallet,
        public readonly float $requestedFund,
        public readonly ?float $percentageFees,
        public readonly ?float $fixedFees,
        public readonly ?string $fundRequestTime,
        public readonly ?string $fundReleaseTime,
        public readonly ?string $cancellationReason,
        public readonly ?string $fundApprovalStatus,
    ) {}

    public static function fromRequest(CreateVirtualcardWithdrawalRequest $request): self
    {
        return new static(
            $request->getVirtualcard(),
            $request->getTheUser(),
            $request->getWallet(),
            $request->requestedFund,
            $request->percentageFees,
            $request->fixedFees,
            $request->fundRequestTime,
            $request->fundReleaseTime,
            $request->cancellationReason,
            $request->fundApprovalStatus,
        );
    }
}
