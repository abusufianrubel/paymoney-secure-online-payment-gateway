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
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Modules\Virtualcard\Entities\Virtualcard;

class VirtualcardsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $virtualcards =  QueryBuilder::for(Virtualcard::with([
            'virtualcardProvider',
            'virtualcardCategory',
            'virtualcardHolder'
        ]))
        ->allowedFilters([
            'card_type',
            'status',
            AllowedFilter::exact('brand', 'card_brand'),
            AllowedFilter::exact('currency', 'currency_code'),
            AllowedFilter::callback('from', function ($query, $value) {
                $query->where('created_at', '>=', Carbon::parse(setDateForDb($value))->startOfDay() );
            }),
            AllowedFilter::callback('to', function ($query, $value) {
                $query->where('created_at', '<=', Carbon::parse(setDateForDb($value))->endOfDay() );
            }),
        ])
        ->orderBy('id', 'desc')
        ->whereNotIn('status', ['Pending', 'Declined'])
        ->take(1100);
        return $virtualcards;
    }

    public function headings(): array
    {
        return [
            __('Card Holder'),
            __('Number'),
            __('Brand'),
            __('Currency'),
            __('Providers'),
            __('Expired'),
            __('Assigned'),
            __('Status'),
        ];
    }

    public function map($virtualcard): array
    {
        return [
            cardTitle($virtualcard->virtualcardHolder),

            $virtualcard->card_number ? maskCardNumberForLogo($virtualcard?->card_number) : '-',

            $virtualcard->card_brand,

            $virtualcard->currency_code,

            $virtualcard->virtualcardProvider?->name,

            $virtualcard->expiry_year ?Carbon::createFromDate($virtualcard->expiry_year, $virtualcard->expiry_month, 1) ->format('m-Y') : '-',

            dateFormat($virtualcard->created_at),

            $virtualcard->status
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
