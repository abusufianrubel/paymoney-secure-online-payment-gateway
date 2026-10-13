@extends('admin.pdf.app')

@section('title', __('Virtualcard Withdrawals'))

@section('content')
    <div class="mt-30">
        <table class="table">
            <tr class="table-header">
                <td>{{ __('User') }}</td>
                <td>{{ __('Card') }}</td>
                <td>{{ __('Amount') }}</td>
                <td>{{ __('Fees') }}</td>
                <td>{{ __('Total') }}</td>
                <td>{{ __('Currency') }}</td>
                <td>{{ __('Requested') }}</td>
                <td>{{ __('Completed') }}</td>
                <td>{{ __('Status') }}</td>
            </tr>

            @foreach ($virtualcardWithdrawals as $virtualcardWithdrawal)
                <tr class="table-body">
                    <td>{{ getColumnValue($virtualcardWithdrawal->user) }}</td>
                    <td>
                        {!! 
                            $virtualcardWithdrawal->virtualcard?->card_number 
                                ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcardWithdrawal->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($virtualcardWithdrawal->virtualcard?->card_number) 
                                : '-' 
                        !!}
                        
                    </td>
                    <td>
                        {{
                            moneyFormat(
                                $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                                formatNumber(
                                    $virtualcardWithdrawal->requested_fund, 
                                    $virtualcardWithdrawal->virtualcard?->currency()?->id
                                ),
                            ), 
                        }}
                    </td>
                    <td>
                        {{ 
                            moneyFormat(
                                $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                                formatNumber(
                                    $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id
                                ),
                            ),
                        }}
                    </td>
                    <td>
                        {{ 
                            moneyFormat(
                                $virtualcardWithdrawal->virtualcard?->currency()?->symbol,
                                formatNumber(
                                    $virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, 
                                    $virtualcardWithdrawal->virtualcard?->currency()?->id
                                )
                            ),
                        }}
                    </td>
                    <td>{{ $virtualcardWithdrawal->virtualcard?->currency_code }}</td>
                    <td>{{ dateFormat($virtualcardWithdrawal->fund_request_time) }}</td>
                    <td>{{ dateFormat($virtualcardWithdrawal->fund_release_time) }}</td>
                    <td>{{ $virtualcardWithdrawal->fund_approval_status }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection


            