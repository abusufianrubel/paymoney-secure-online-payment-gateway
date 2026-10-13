@extends('user.layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/virtual-card.min.css') }}">
@endpush

@section('content')

    @include('user.common.alert')

    <!-- Kyc Alert -->
    @if (preference('card_kyc') == 'Yes' && kycRequired()['status'] == \Illuminate\Http\Response::HTTP_BAD_REQUEST)
        <div class="alert alert-warning" role="alert">
            {{ kycRequired()['message'] }}
        </div>
    @endif
    <!-- Kyc Alert -->

    <div class="text-center">
        <p class="mb-0 gilroy-Semibold f-26 text-dark theme-tran r-f-20">{{ __('Virtual Card Withdrawals') }}</p>
        <p class="mb-0 gilroy-medium text-gray-100 f-16 r-f-12 mt-2 tran-title">
            {{ __('List of all the withdrawals you have') }}</p>
    </div>

    <!-- Filtered Section -->
    @if (\Illuminate\Support\Facades\Request::is('virtualcard/withdrawals') && \Illuminate\Support\Facades\Request::query())
        @if($virtualcardWithdrawals->count() > 0)
            <div>

                <!-- Filter Section -->
                <div class="mt-22 mt-sm-4">
                    <form action="" method="get">
                        <div class="filter-panel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-26">
                                <div class="d-flex flex-wrap align-items-center pb-xl-0">

                                <!-- Date Range Hidden Filter -->
                                <input id="startfrom" type="hidden" name="filter[from]" value="{{ isset($filter) ? $filter['from'] : '' }}">
                                <input id="endto" type="hidden" name="filter[to]" value="{{ isset($filter) ? $filter['to'] : '' }}">
                                <!-- Date Range -->
                                <div class="me-2">
                                    <div id="daterange-btn" class="param-ref filter-ref h-45 custom-daterangepicker">
                                        {!! getModulesSvgIcon('calender') !!}
                                        <p class="mb-0 gilroy-medium f-13 px-2">{{ __('Pick a date range') }}</p>
                                        {!! getModulesSvgIcon('down_arrow') !!}
                                    </div>
                                </div>

                                <!-- Brand -->
                                <div class="me-2">
                                    <div class="param-ref filter-ref w-135 h-45">
                                        <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="brand" name="filter[brand]">
                                            <option value="">{{ __('Brand') }}</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->card_brand }}" {{ $filter && $filter['brand'] == $brand->card_brand ? 'selected' : '' }}>{{ $brand->card_brand }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Currency -->
                                <div class="me-2">
                                    <div class="param-ref filter-ref w-135 h-45">
                                        <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="currency" name="filter[currency]">
                                            <option value="">{{ __('Currency') }}</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->currency_code }}" {{ $filter && $filter['currency'] == $currency->currency_code ? 'selected' : '' }}>{{ $currency->currency_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="me-2">
                                    <div class="param-ref filter-ref w-135 h-45">
                                        <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="status" name="filter[status]">
                                            <option value="">{{ __('Status') }}</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->fund_approval_status }}" {{ $filter && $filter['status'] == $status->fund_approval_status ? 'selected' : '' }}>{{ $status->fund_approval_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="filter-btn f-14 gilroy-medium leading-17">{{ __('Filter') }}</button>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="d-flex align-items-center justify-content-end">
                                <a href="{{ route('user.virtualcard_withdrawal.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn ">
                                <span class="f-14 leading-29 gilroy-medium"> + {{ __('Withdraw') }}</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Filter Section -->

                <div class="table-responsive table-scrolbar thin-scrollbar">
                    <table class="merchant-payments table-curved table recent_activity table-bordered mt-4">
                        <thead>
                            <tr class="payment-parent-section-title component-table-one">
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Card Number') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Currency') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Amount') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Fee') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Total') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Request Time') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Release Time') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Status') }}</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($virtualcardWithdrawals as $key => $virtualcardWithdrawal)
                            <tr class="bg-white">
                                
                                <td class="w-270p d-flex align-items-center">
                                    @if ($virtualcardWithdrawal->virtualcard?->card_number && $virtualcardWithdrawal->virtualcard?->card_brand == 'Visa Card')
                                        {!! virtualcardSvgIcons('visa_card_icon') !!}
                                    @elseif ($virtualcardWithdrawal->virtualcard?->card_number && $virtualcardWithdrawal->virtualcard?->card_brand == 'Master Card')
                                        {!! virtualcardSvgIcons('master_card_icon') !!}
                                    @endif
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcardWithdrawal->virtualcard?->card_number ? maskCardNumberForLogo($virtualcardWithdrawal->virtualcard?->card_number) : '-' }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcardWithdrawal->virtualcard?->currency_code }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start whitespace-nowrap">{{ dateFormat($virtualcardWithdrawal->fund_request_time) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcardWithdrawal->fund_release_time ? dateFormat($virtualcardWithdrawal->fund_release_time) : '-' }}</p>
                                </td>
                                <td>
                                    <span class="f-12 gilroy-medium custom-badge d-flex justify-content-center align-items-center px-0 {{ getColor($virtualcardWithdrawal->fund_approval_status) }} r-mt-open">{{ $virtualcardWithdrawal->fund_approval_status }}</span>
                                </td>
                                <td class="text-end">
                                    <a class="arrow-hovers cursor-pointer transaction-arrow text-right" data-bs-toggle="modal" data-bs-target="#transaction-Info-{{ $key }}">
                                        {!! getModulesSvgIcon('right_arrow') !!}
                                    </a>
                                </td>
                            </tr>

                            <!-- Transaction Modal -->
                            <div class="modal fade modal-overly" id="transaction-Info-{{ $key }}" tabindex="-1" aria-hidden="true">
                                <div class="transac modal-dialog modal-dialog-centered modal-lg res-dialog">
                                    <div class="modal-content modal-transac transaction-modal">
                                        <div class="modal-body modal-themeBody">
                                            <div class="d-flex position-relative modal-res">
                                                <button type="button" class="cursor-pointer close-btn" data-bs-dismiss="modal" aria-label="Close">
                                                    <svg class="position-absolute close-btn text-gray-100" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.24408 5.24408C5.56951 4.91864 6.09715 4.91864 6.42259 5.24408L10 8.82149L13.5774 5.24408C13.9028 4.91864 14.4305 4.91864 14.7559 5.24408C15.0814 5.56951 15.0814 6.09715 14.7559 6.42259L11.1785 10L14.7559 13.5774C15.0814 13.9028 15.0814 14.4305 14.7559 14.7559C14.4305 15.0814 13.9028 15.0814 13.5774 14.7559L10 11.1785L6.42259 14.7559C6.09715 15.0814 5.56951 15.0814 5.24408 14.7559C4.91864 14.4305 4.91864 13.9028 5.24408 13.5774L8.82149 10L5.24408 6.42259C4.91864 6.09715 4.91864 5.56951 5.24408 5.24408Z" fill="currentColor" />
                                                    </svg>
                                                </button>
                                                <div class="deposit-transac d-flex flex-column justify-content-center p-4 text-wrap">
                                                    <div class="d-flex justify-content-center text-primary align-items-center transac-img">
                                                        <img src="{{ image('', 'Mts') }}" alt="{{ __('Transaction') }}" class="img-fluid">
                                                    </div>
                                                    <p class="mb-0 mt-28 text-dark gilroy-medium f-15 r-f-12 r-mt-18 text-center">{{ __('Withdrawal Amount') }}</p>
                                                    <p class="mb-0 text-dark gilroy-Semibold f-24 leading-29 r-f-26 text-center l-s2 mt-10">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                                    <p class="mb-0 mt-18 text-gray-100 gilroy-medium f-13 leading-20 r-f-14 text-center">{{ dateFormat($virtualcardWithdrawal->created_at) }}</p>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('user.virtualcard_withdrawal.pdf', $virtualcardWithdrawal) }}" class="infoBtn-print cursor-pointer f-14 gilroy-medium text-dark mt-35 d-flex justify-content-center align-items-center" target="__blank">
                                                            {!! svgIcons('printer') !!}&nbsp;
                                                            <span>{{ __('Print') }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="ml-20 trans-details">
                                                    <p class="mb-0 mt-9 text-dark dark-5B f-20 gilroy-Semibold transac-title">{{ __('Withdrawal Details') }}</p>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ 'Brand' }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($virtualcardWithdrawal->virtualcard)->card_brand }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Currency') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($virtualcardWithdrawal->virtualcard)->currency_code }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Card Number') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">
                                                                {!! $virtualcardWithdrawal->virtualcard?->card_number ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcardWithdrawal->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($virtualcardWithdrawal->virtualcard?->card_number) : '-' !!}
                                                            </p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Transaction Fee') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ 'asdf' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Transaction ID') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $virtualcardWithdrawal->transaction->uuid }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Status') }}</p>
                                                            <p id="status_" class="mb-0 mt-5p {{ getColor($virtualcardWithdrawal->fund_approval_status) }} gilroy-medium f-15 leading-22 r-text">{{ getStatus($virtualcardWithdrawal->fund_approval_status) }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="hr-border w-100 mb-0"></p>
                                                    <div class="row gx-sm-5">

                                                        <!-- Amount -->
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 dark-B87 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Withdrawal') }}&nbsp;{{ __('Amount') }}</p>
                                                            <p class="mb-0 mt-5p text-dark dark-CDO gilroy-medium f-15 leading-22 r-text">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                                        </div>

                                                        <!-- Total Amount -->
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 dark-B87 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Total Amount') }}</p>
                                                            <p class="mb-0 mt-5p text-dark dark-CDO gilroy-medium f-15 leading-22 r-text"> {{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                                        </div>

                                                        @if ($virtualcardWithdrawal->cancellation_reason)
                                                            <div class="row gx-sm-5">
                                                                <div class="col-12">
                                                                    <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Cancellation Reason') }}</p>
                                                                    <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $virtualcardWithdrawal->cancellation_reason }}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else

            <!-- Filter Section -->
            <div class="mt-22 mt-sm-4">
                <form action="" method="get">
                    <div class="filter-panel">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-26">
                            <div class="d-flex flex-wrap align-items-center pb-xl-0">

                            <!-- Date Range Hidden Filter -->
                            <input id="startfrom" type="hidden" name="filter[from]" value="{{ isset($filter) ? $filter['from'] : '' }}">
                            <input id="endto" type="hidden" name="filter[to]" value="{{ isset($filter) ? $filter['to'] : '' }}">
                            <!-- Date Range -->
                            <div class="me-2">
                                <div id="daterange-btn" class="param-ref filter-ref h-45 custom-daterangepicker">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8 1C8.55229 1 9 1.44772 9 2V3H15V2C15 1.44772 15.4477 1 16 1C16.5523 1 17 1.44772 17 2V3.00163C17.4755 3.00489 17.891 3.01471 18.2518 3.04419C18.8139 3.09012 19.3306 3.18868 19.816 3.43597C20.5686 3.81947 21.1805 4.43139 21.564 5.18404C21.8113 5.66937 21.9099 6.18608 21.9558 6.74817C22 7.28936 22 7.95372 22 8.75868V17.2413C22 18.0463 22 18.7106 21.9558 19.2518C21.9099 19.8139 21.8113 20.3306 21.564 20.816C21.1805 21.5686 20.5686 22.1805 19.816 22.564C19.3306 22.8113 18.8139 22.9099 18.2518 22.9558C17.7106 23 17.0463 23 16.2413 23H7.75868C6.95372 23 6.28936 23 5.74817 22.9558C5.18608 22.9099 4.66937 22.8113 4.18404 22.564C3.43139 22.1805 2.81947 21.5686 2.43597 20.816C2.18868 20.3306 2.09012 19.8139 2.04419 19.2518C1.99998 18.7106 1.99999 18.0463 2 17.2413V8.7587C1.99999 7.95373 1.99998 7.28937 2.04419 6.74817C2.09012 6.18608 2.18868 5.66937 2.43597 5.18404C2.81947 4.43139 3.43139 3.81947 4.18404 3.43597C4.66937 3.18868 5.18608 3.09012 5.74818 3.04419C6.10898 3.01471 6.52454 3.00489 7 3.00163V2C7 1.44772 7.44772 1 8 1ZM7 5.00176C6.55447 5.00489 6.20463 5.01356 5.91104 5.03755C5.47262 5.07337 5.24842 5.1383 5.09202 5.21799C4.7157 5.40973 4.40973 5.71569 4.21799 6.09202C4.1383 6.24842 4.07337 6.47262 4.03755 6.91104C4.00078 7.36113 4 7.94342 4 8.8V9H20V8.8C20 7.94342 19.9992 7.36113 19.9624 6.91104C19.9266 6.47262 19.8617 6.24842 19.782 6.09202C19.5903 5.7157 19.2843 5.40973 18.908 5.21799C18.7516 5.1383 18.5274 5.07337 18.089 5.03755C17.7954 5.01356 17.4455 5.00489 17 5.00176V6C17 6.55228 16.5523 7 16 7C15.4477 7 15 6.55228 15 6V5H9V6C9 6.55228 8.55229 7 8 7C7.44772 7 7 6.55228 7 6V5.00176ZM20 11H4V17.2C4 18.0566 4.00078 18.6389 4.03755 19.089C4.07337 19.5274 4.1383 19.7516 4.21799 19.908C4.40973 20.2843 4.7157 20.5903 5.09202 20.782C5.24842 20.8617 5.47262 20.9266 5.91104 20.9624C6.36113 20.9992 6.94342 21 7.8 21H16.2C17.0566 21 17.6389 20.9992 18.089 20.9624C18.5274 20.9266 18.7516 20.8617 18.908 20.782C19.2843 20.5903 19.5903 20.2843 19.782 19.908C19.8617 19.7516 19.9266 19.5274 19.9624 19.089C19.9992 18.6389 20 18.0566 20 17.2V11Z" fill="currentColor" />
                                    </svg>
                                    <p class="mb-0 gilroy-medium f-13 px-2">{{ __('Pick a date range') }}</p>
                                    <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.40165 3.23453C1.6403 2.99588 2.02723 2.99588 2.26589 3.23453L5.50043 6.46908L8.73498 3.23453C8.97363 2.99588 9.36057 2.99588 9.59922 3.23453C9.83788 3.47319 9.83788 3.86012 9.59922 4.09877L5.93255 7.76544C5.6939 8.00409 5.30697 8.00409 5.06831 7.76544L1.40165 4.09877C1.16299 3.86012 1.16299 3.47319 1.40165 3.23453Z" fill="currentColor" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Brand -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="brand" name="filter[brand]">
                                        <option value="">{{ __('Brand') }}</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->card_brand }}" {{ $filter && $filter['brand'] == $brand->card_brand ? 'selected' : '' }}>{{ $brand->card_brand }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Currency -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="currency" name="filter[currency]">
                                        <option value="">{{ __('Currency') }}</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->currency_code }}" {{ $filter && $filter['currency'] == $currency->currency_code ? 'selected' : '' }}>{{ $currency->currency_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="status" name="filter[status]">
                                        <option value="">{{ __('Status') }}</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->fund_approval_status }}" {{ $filter && $filter['status'] == $status->fund_approval_status ? 'selected' : '' }}>{{ $status->fund_approval_status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="filter-btn f-14 gilroy-medium leading-17">{{ __('Filter') }}</button>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="d-flex align-items-center justify-content-end">
                            <a href="{{ route('user.virtualcard_withdrawal.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn ">
                            <span class="f-14 leading-29 gilroy-medium"> + {{ __('Withdraw') }}</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Filter Section -->

            <div class="notfound mt-16 bg-white p-4 shadow">
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-26">
                    <div class="image-notfound">
                        <img src="{{ asset('public/dist/images/not-found.png') }}" class="img-fluid">
                    </div>
                    <div class="text-notfound">
                        <p class="mb-0 f-20 leading-25 gilroy-medium text-dark">{{ __('Sorry!') }} {{ __('No data found.') }}</p>
                        <p class="mb-0 f-16 leading-24 gilroy-regular text-gray-100 mt-12">{{ __('The requested data does not exist for this feature overview.') }}</p>
                    </div>
                </div>
            </div>
        @endif

    <!-- Unfiltered Section -->
    @elseif (\Illuminate\Support\Facades\Request::is('virtualcard/withdrawals') && ! \Illuminate\Support\Facades\Request::query())
        @if($virtualcardWithdrawals->count() > 0)
            <div>

                <!-- Filter Section -->
                <div class="mt-22 mt-sm-4">
                    <form action="" method="get">
                        <div class="filter-panel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-26">
                                <div class="d-flex flex-wrap align-items-center pb-xl-0">

                                <!-- Date Range Hidden Filter -->
                                <input id="startfrom" type="hidden" name="filter[from]" value="{{ isset($filter) ? $filter['from'] : '' }}">
                                <input id="endto" type="hidden" name="filter[to]" value="{{ isset($filter) ? $filter['to'] : '' }}">
                                <!-- Date Range -->
                                <div class="me-2">
                                    <div id="daterange-btn" class="param-ref filter-ref h-45 custom-daterangepicker">
                                        {!! getModulesSvgIcon('calender') !!}
                                        <p class="mb-0 gilroy-medium f-13 px-2">{{ __('Pick a date range') }}</p>
                                        {!! getModulesSvgIcon('down_arrow') !!}
                                    </div>
                                </div>

                                <!-- Brand -->
                                <div class="me-2">
                                    <div class="param-ref filter-ref w-135 h-45">
                                        <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="brand" name="filter[brand]">
                                            <option value="">{{ __('Brand') }}</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->card_brand }}" {{ $filter && $filter['brand'] == $brand->card_brand ? 'selected' : '' }}>{{ $brand->card_brand }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Currency -->
                                <div class="me-2">
                                    <div class="param-ref filter-ref w-135 h-45">
                                        <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="currency" name="filter[currency]">
                                            <option value="">{{ __('Currency') }}</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->currency_code }}" {{ $filter && $filter['currency'] == $currency->currency_code ? 'selected' : '' }}>{{ $currency->currency_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="me-2">
                                    <div class="param-ref filter-ref w-135 h-45">
                                        <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="status" name="filter[status]">
                                            <option value="">{{ __('Status') }}</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->fund_approval_status }}" {{ $filter && $filter['status'] == $status->fund_approval_status ? 'selected' : '' }}>{{ $status->fund_approval_status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="filter-btn f-14 gilroy-medium leading-17">{{ __('Filter') }}</button>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="d-flex align-items-center justify-content-end">
                                <a href="{{ route('user.virtualcard_withdrawal.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn ">
                                <span class="f-14 leading-29 gilroy-medium"> + {{ __('Withdraw') }}</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Filter Section -->

                <div class="table-responsive table-scrolbar thin-scrollbar">
                    <table class="merchant-payments table-curved table recent_activity table-bordered mt-4">
                        <thead>
                            <tr class="payment-parent-section-title component-table-one">
                                
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Card Number') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Currency') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Amount') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Fee') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Total') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Request Time') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Release Time') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Status') }}</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($virtualcardWithdrawals as $key => $virtualcardWithdrawal)
                            <tr class="bg-white">
                                
                                <td class="w-270p d-flex align-items-center">
                                    @if ($virtualcardWithdrawal->virtualcard?->card_number && $virtualcardWithdrawal->virtualcard?->card_brand == 'Visa Card')
                                        {!! virtualcardSvgIcons('visa_card_icon') !!}
                                    @elseif ($virtualcardWithdrawal->virtualcard?->card_number && $virtualcardWithdrawal->virtualcard?->card_brand == 'Master Card')
                                        {!! virtualcardSvgIcons('master_card_icon') !!}
                                    @endif
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcardWithdrawal->virtualcard?->card_number ? maskCardNumberForLogo($virtualcardWithdrawal->virtualcard?->card_number) : '-' }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcardWithdrawal->virtualcard?->currency_code }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start whitespace-nowrap">{{ dateFormat($virtualcardWithdrawal->fund_request_time) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcardWithdrawal->fund_release_time ? dateFormat($virtualcardWithdrawal->fund_release_time) : '-' }}</p>
                                </td>
                                <td>
                                    <span class="f-12 gilroy-medium custom-badge d-flex justify-content-center align-items-center px-0 {{ getColor($virtualcardWithdrawal->fund_approval_status) }} r-mt-open">{{ $virtualcardWithdrawal->fund_approval_status }}</span>
                                </td>
                                <td class="text-end">
                                    <a class="arrow-hovers cursor-pointer transaction-arrow text-right" data-bs-toggle="modal" data-bs-target="#transaction-Info-{{ $key }}">
                                        <svg class="nscaleX-1" width="12" height="12" viewBox="0 0 12 12" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.5312 1.52861C3.27085 1.78896 3.27085 2.21107 3.5312 2.47141L7.0598 6.00001L3.5312 9.52861C3.27085 9.78895 3.27085 10.2111 3.5312 10.4714C3.79155 10.7318 4.21366 10.7318 4.47401 10.4714L8.47401 6.47141C8.73436 6.21106 8.73436 5.78895 8.47401 5.52861L4.47401 1.52861C4.21366 1.26826 3.79155 1.26826 3.5312 1.52861Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>

                            <!-- Transaction Modal -->
                            <div class="modal fade modal-overly" id="transaction-Info-{{ $key }}" tabindex="-1" aria-hidden="true">
                                <div class="transac modal-dialog modal-dialog-centered modal-lg res-dialog">
                                    <div class="modal-content modal-transac transaction-modal">
                                        <div class="modal-body modal-themeBody">
                                            <div class="d-flex position-relative modal-res">
                                                <button type="button" class="cursor-pointer close-btn" data-bs-dismiss="modal" aria-label="Close">
                                                    <svg class="position-absolute close-btn text-gray-100" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.24408 5.24408C5.56951 4.91864 6.09715 4.91864 6.42259 5.24408L10 8.82149L13.5774 5.24408C13.9028 4.91864 14.4305 4.91864 14.7559 5.24408C15.0814 5.56951 15.0814 6.09715 14.7559 6.42259L11.1785 10L14.7559 13.5774C15.0814 13.9028 15.0814 14.4305 14.7559 14.7559C14.4305 15.0814 13.9028 15.0814 13.5774 14.7559L10 11.1785L6.42259 14.7559C6.09715 15.0814 5.56951 15.0814 5.24408 14.7559C4.91864 14.4305 4.91864 13.9028 5.24408 13.5774L8.82149 10L5.24408 6.42259C4.91864 6.09715 4.91864 5.56951 5.24408 5.24408Z" fill="currentColor" />
                                                    </svg>
                                                </button>
                                                <div class="deposit-transac d-flex flex-column justify-content-center p-4 text-wrap">
                                                    <div class="d-flex justify-content-center text-primary align-items-center transac-img">
                                                        <img src="{{ image('', 'Mts') }}" alt="{{ __('Transaction') }}" class="img-fluid">
                                                    </div>
                                                    <p class="mb-0 mt-28 text-dark gilroy-medium f-15 r-f-12 r-mt-18 text-center">{{ __('Withdrawal Amount') }}</p>
                                                    <p class="mb-0 text-dark gilroy-Semibold f-24 leading-29 r-f-26 text-center l-s2 mt-10">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                                    <p class="mb-0 mt-18 text-gray-100 gilroy-medium f-13 leading-20 r-f-14 text-center">{{ dateFormat($virtualcardWithdrawal->created_at) }}</p>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('user.virtualcard_withdrawal.pdf', $virtualcardWithdrawal) }}" class="infoBtn-print cursor-pointer f-14 gilroy-medium text-dark mt-35 d-flex justify-content-center align-items-center" target="__blank">
                                                            {!! svgIcons('printer') !!}&nbsp;
                                                            <span>{{ __('Print') }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="ml-20 trans-details">
                                                    <p class="mb-0 mt-9 text-dark dark-5B f-20 gilroy-Semibold transac-title">{{ __('Withdrawal Details') }}</p>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ 'Brand' }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($virtualcardWithdrawal->virtualcard)->card_brand }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Currency') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($virtualcardWithdrawal->virtualcard)->currency_code }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Card Number') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">

                                                                {!! $virtualcardWithdrawal->virtualcard?->card_number ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcardWithdrawal->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($virtualcardWithdrawal->virtualcard?->card_number) : '-' !!}
                                                        
                                                            </p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Transaction Fee') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ 'asdf' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Transaction ID') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $virtualcardWithdrawal->transaction->uuid }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Status') }}</p>
                                                            <p id="status_" class="mb-0 mt-5p {{ getColor($virtualcardWithdrawal->fund_approval_status) }} gilroy-medium f-15 leading-22 r-text">{{ getStatus($virtualcardWithdrawal->fund_approval_status) }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="hr-border w-100 mb-0"></p>
                                                    <div class="row gx-sm-5">

                                                        <!-- Amount -->
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 dark-B87 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Withdrawal') }}&nbsp;{{ __('Amount') }}</p>
                                                            <p class="mb-0 mt-5p text-dark dark-CDO gilroy-medium f-15 leading-22 r-text">{{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                                        </div>

                                                        <!-- Total Amount -->
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 dark-B87 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Total Amount') }}</p>
                                                            <p class="mb-0 mt-5p text-dark dark-CDO gilroy-medium f-15 leading-22 r-text"> {{ moneyFormat($virtualcardWithdrawal->virtualcard?->currency()?->symbol, formatNumber($virtualcardWithdrawal->requested_fund + $virtualcardWithdrawal->percentage_fees + $virtualcardWithdrawal->fixed_fees, $virtualcardWithdrawal->virtualcard?->currency()?->id)) }}</p>
                                                        </div>

                                                        @if ($virtualcardWithdrawal->cancellation_reason)
                                                            <div class="row gx-sm-5">
                                                                <div class="col-12">
                                                                    <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Cancellation Reason') }}</p>
                                                                    <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $virtualcardWithdrawal->cancellation_reason }}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="notfound mt-16 bg-white p-4 shadow">
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-26">
                    <div class="image-notfound">
                        <img src="{{ asset('public/dist/images/not-found.png') }}" class="img-fluid">
                    </div>
                    <div class="text-notfound">
                        <p class="mb-0 f-20 leading-25 gilroy-medium text-dark">{{ __('Sorry!') }} {{ __('No data found.') }}</p>
                        <p class="mb-0 f-16 leading-24 gilroy-regular text-gray-100 mt-12">{{ __('The requested data does not exist for this feature overview.') }}</p>
                        @if (count($virtualcards)) 
                            <div class="d-flex mt-14 r-mt-22 align-items-center">
                                <a href="{{ route('user.virtualcard_withdrawal.create') }}" class="bg-primary text-white Add-new-btn w-176 addnew text-center green-btn ">
                                    <span class="f-14 leading-29 gilroy-medium"> {{ __('Withdraw Now') }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="mt-4">
        {{ $virtualcardWithdrawals->links('vendor.pagination.bootstrap-5') }}
    </div>

@endsection

@push('js')
<script src="{{ asset('public/dist/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
<script src="{{ asset('public/user/customs/js/daterange-select.min.js') }}"></script>
<script type="text/javascript">
    'use strict';
    var sessionDateFormateType = "{{ Session::get('date_format_type') }}";
    var startDate = "{!! isset($filter['from']) ? setDateForDb($filter['from']) : '' !!}";
    var endDate = "{!! isset($filter['to']) ? setDateForDb($filter['to']) : '' !!}";
    let dateRangePickerText = "{{ __('Pick a date range') }}";
    let filterUrl = "{{ route('user.virtualcard_withdrawal.index') }}";
</script>
<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/virtualcard-withdrawal-index.min.js') }}"></script>

@endpush
