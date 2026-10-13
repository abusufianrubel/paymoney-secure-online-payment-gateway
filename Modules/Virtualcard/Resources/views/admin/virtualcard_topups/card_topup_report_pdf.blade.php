@extends('admin.pdf.app')

@section('title', __('Virtualcard Topup List pdf'))

@section('content')
    <div class="mt-30">
        <table class="table">
            <tr class="table-header">
                <td>{{ __('Card Brand') }}</td>
                <td>{{ __('User') }}</td>
                <td>{{ __('Card Number') }}</td>
                <td>{{ __('Currency Code') }}</td>
                <td>{{ __('Amount') }}</td>
                <td>{{ __('Fees') }}</td>
                <td>{{ __('Total') }}</td>
                <td>{{ __('Status') }}</td>
                <td>{{ __('Date') }}</td>
            </tr>

            @foreach ($virtualcardtopups as $topup)
            @php
                $fees  = $topup->percentage_fees + $topup->fixed_fees;
                $total = $fees + $topup->requested_fund;
            @endphp
                <tr class="table-body">
                    <td>{{ optional($topup->virtualcard)->card_brand }}</td>
                    <td>{{ getColumnValue($topup->user) }}</td>
                    <td>{!! 
                        $topup->virtualcard?->card_number 
                                ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $topup->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($topup->virtualcard?->card_number) 
                                : '-' 
                    !!}</td>
                    <td>{{ optional($topup->virtualcard)->currency_code ?? '-' }}</td>
                    <td>{{ formatNumber($topup->requested_fund, currencyDetails(optional($topup->virtualcard)->currency_code)->id) ?? '-' }}</td>
                    <td>{{ formatNumber($fees, currencyDetails(optional($topup->virtualcard)->currency_code)->id) ?? '-' }}</td>
                    <td>{{ formatNumber($total, currencyDetails(optional($topup->virtualcard)->currency_code)->id) ?? '-' }}</td>
                    <td>{{ getStatus($topup->fund_approval_status) }}</td>
                    <td>{{ dateFormat($topup->created_at) }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
