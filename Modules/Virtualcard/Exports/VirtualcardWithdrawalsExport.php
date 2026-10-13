<?php

namespace Modules\Virtualcard\Exports;

use Maatwebsite\Excel\Concerns\{
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Modules\Virtualcard\Entities\VirtualcardWithdrawal;

class VirtualcardWithdrawalsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $virtualcardWithdrawals = QueryBuilder::for(VirtualcardWithdrawal::with('virtualcard', 'user'))
                                    ->allowedFilters([
                                        AllowedFilter::exact('status', 'fund_approval_status'),
                                        AllowedFilter::exact('currency', 'virtualcard.currency_code'),
                                        AllowedFilter::exact('first_name', ),
                                        AllowedFilter::callback('from', function ($query, $value) {
                                            $query->where('created_at', '>=', Carbon::parse(setDateForDb($value))->startOfDay());
                                        }),
                                        AllowedFilter::callback('to', function ($query, $value) {
                                            $query->where('created_at', '<=', Carbon::parse(setDateForDb($value))->endOfDay());
                                        }),
                                        AllowedFilter::callback('user', function ($query, $value) {
                                            $query->whereHas('user', function ($query) use ($value) {
                                                $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%$value%");
                                            });
                                        }),
                                    ])
                                    ->orderBy('id', 'desc')
                                    ->take(1100);

        return $virtualcardWithdrawals;
    }

    public function headings(): array
    {
        return [
            __('User'),
            __('Card'),
            __('Amount'),
            __('Fees'),
            __('Total'),
            __('Currency'),
            __('Requested'),
            __('Completed'),
            __('Status'),
        ];
    }

    public function map($virtualcardWithdrawal): array
    {
        return [
            getColumnValue($virtualcardWithdrawal->user),
             
            $virtualcardWithdrawal->virtualcard?->card_number 
                ? maskCardNumberForLogo($virtualcardWithdrawal->virtualcard?->card_number) 
                : '-' ,

            moneyFormat(
                $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                formatNumber(
                    $virtualcardWithdrawal->requested_fund,
                    $virtualcardWithdrawal->virtualcard?->currency()?->id
                ),
            ),

            moneyFormat(
                $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                formatNumber(
                    $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id
                ),
            ),

            moneyFormat(
                $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                formatNumber(
                    $virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees,
                    $virtualcardWithdrawal->virtualcard?->currency()?->id
                )
            ),

            $virtualcardWithdrawal->virtualcard?->currency_code,

            dateFormat($virtualcardWithdrawal->fund_request_time),

            dateFormat($virtualcardWithdrawal->fund_release_time),

            $virtualcardWithdrawal->fund_approval_status
        ];
    }

    public function styles($transfer)
    {
        $transfer->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('G:H')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('I')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('1')->getFont()->setBold(true);
    }
}
