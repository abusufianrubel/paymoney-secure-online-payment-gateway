<?php

namespace Modules\Virtualcard\Exports;

use Modules\Virtualcard\Entities\VirtualcardTopup;
use Maatwebsite\Excel\Concerns\{
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
};

class VirtualcardTopupExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from = !empty(request()->from) ? setDateForDb(request()->from) : null;
        $to = !empty(request()->to) ? setDateForDb(request()->to) : null;
        $status = isset(request()->status) ? request()->status : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $brand = isset(request()->brand) ? request()->brand : null;
        $user = isset(request()->user_id) ? request()->user_id : null;
        $virtualcardtopup = (new VirtualcardTopup())->getVirtualcardTopupsList($from, $to, $status, $currency, $brand, $user)->orderBy('id', 'desc');

        return $virtualcardtopup;
    }

    public function headings(): array
    {
        return [
            __('Card Brand'),
            __('User'),
            __('Card Number'),
            __('Currency Code'),
            __('Amount'),
            __('Fees'),
            __('Total'),
            __('Status'),
            __('Date'),
        ];
    }

    public function map($topup): array
    {
        $fees  = $topup->percentage_fees + $topup->fixed_fees;
        $total = $fees + $topup->requested_fund;
        return [
            optional($topup->virtualcard)->card_brand,
            getColumnValue($topup->user),
            $topup->virtualcard?->card_number ? maskCardNumberForLogo($topup->virtualcard?->card_number) : '-',
            optional($topup->virtualcard)->currency_code ?? '-',
            formatNumber($topup->requested_fund, currencyDetails(optional($topup->virtualcard)->currency_code)->id) ?? '-',
            formatNumber($fees, currencyDetails(optional($topup->virtualcard)->currency_code)->id) ?? '-',
            formatNumber($total, currencyDetails(optional($topup->virtualcard)->currency_code)->id) ?? '-',
            getStatus($topup->fund_approval_status),
            dateFormat($topup->created_at),
        ];
    }

    public function styles($transfer)
    {
        $transfer->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('G:H')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('1')->getFont()->setBold(true);
    }
}
