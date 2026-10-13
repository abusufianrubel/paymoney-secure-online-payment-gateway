<?php

namespace Modules\KycVerification\Exports;

use App\Models\DocumentVerification;
use Maatwebsite\Excel\Concerns\{
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
};

class VerificationsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        return (new DocumentVerification())->getVerifications()->latest();
    }

    public function headings(): array
    {
        return [
            'Date',
            'User',
            'Provider',
            'Type',
            'Status'
        ];
    }

    public function map($verification): array
    {
        return [
            dateFormat($verification->created_at),
            getColumnValue($verification->user),
            getColumnValue($verification->provider, 'name', ''),
            $verification->verification_type,
            $verification->status
        ];
    }

    public function styles($verification)
    {
        $verification->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $verification->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $verification->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $verification->getStyle('G:H')->getAlignment()->setHorizontal('center');
        $verification->getStyle('I')->getAlignment()->setHorizontal('center');
        $verification->getStyle('1')->getFont()->setBold(true);
    }
}
