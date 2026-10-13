<?php

namespace Modules\Virtualcard\DataTransferObject;

use Modules\Virtualcard\Entities\{
    VirtualcardHolder,
    VirtualcardCategory,
    VirtualcardProvider
};
use Modules\Virtualcard\Http\Requests\Admin\IssueVirtualcardRequest;

class VirtualcardData
{
    public function __construct (
        public readonly VirtualcardProvider $virtualcardProvider,
        public readonly VirtualcardCategory $virtualcardCategory,
        public readonly VirtualcardHolder $virtualcardHolder,
        public readonly ?string $cardType,
        public readonly string $cardBrand,
        public readonly string $cardNumber,
        public readonly string $cardCvc,
        public readonly string $currencyCode,
        public readonly ?float $amount,
        public readonly string $cardExpiryDate,
        public readonly ?string $status
    ) {}

public static function fromRequest(IssueVirtualcardRequest $request): self
    {
        return new static(
            $request->getVirtualcardProvider(),
            $request->getVirtualcardCategory(),
            $request->getVirtualcardHolder(),
            $request->cardType,
            $request->cardBrand,
            $request->cardNumber,
            $request->cardCvc,
            $request->currencyCode,
            $request->amount,
            $request->cardExpiryDate,
            $request->status
        );
    }
}
