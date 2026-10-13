<?php

namespace Modules\Virtualcard\Exports;

use Modules\Virtualcard\Entities\VirtualcardHolder;
use Maatwebsite\Excel\Concerns\{
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
};

class VirtualcardHolderExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from = !empty(request()->from) ? setDateForDb(request()->from) : null;
        $to = !empty(request()->to) ? setDateForDb(request()->to) : null;
        $status = isset(request()->status) ? request()->status : 'all';
        $holderType = isset(request()->holderType) ? request()->holderType : 'all';
        $country = isset(request()->country) ? request()->country : 'all';
        $user = isset(request()->user_id) ? request()->user_id : null;
        $virtualcardHolders = (new VirtualcardHolder())->getCardHoldersList($from, $to, $status, $country, $holderType, $user)->orderBy('id', 'desc');

        return $virtualcardHolders;
    }

    public function headings(): array
    {
        return [
            __('Card Type'),
            __('User'),
            __('Name'),
            __('Country'),
            __('Status'),
            __('Date'),
        ];
    }

    public function map($holder): array
    {
        $name = getColumnValue($holder);
        if (\Modules\Virtualcard\Enums\CardHolderTypes::BUSINESS->value == $holder->card_holder_type) {
            $name = $holder->business_name . ' (' . getColumnValue($holder) . ')';
        }
        return [
            ucwords($holder->card_holder_type),
            getColumnValue($holder->user),
            $name ?? '-',
            $holder->country ?? '-',
            getStatus($holder->status),
            dateFormat($holder->created_at),
        ];
    }

    public function styles($transfer)
    {
        $transfer->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('1')->getFont()->setBold(true);
    }
}
