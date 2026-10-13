@extends('user.layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/virtual-card.min.css') }}">
@endpush

@section('content')

    @if (preference('card_kyc') == 'Yes' && kycRequired()['status'] == \Illuminate\Http\Response::HTTP_BAD_REQUEST)
        <div class="alert alert-warning" role="alert">
            {{ kycRequired()['message'] }}
        </div>
    @endif
    <div class="text-center">
        <p class="mb-0 gilroy-Semibold f-26 text-dark theme-tran r-f-20">{{ __('Topups') }}</p>
        <p class="mb-0 gilroy-medium text-gray-100 f-16 r-f-12 mt-2 tran-title">
            {{ __('List of all the topups you have') }}</p>
    </div>

    <!-- Filtered Section -->
    @if (\Illuminate\Support\Facades\Request::is('virtualcard/topups') && \Illuminate\Support\Facades\Request::query())
        @if($virtualcardTopups->count() > 0)
            <!-- Filter Section -->
            <div class="mt-22 mt-sm-4">
                <form method="get">
                    <div>
                        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center pb-26">
                            <div class="d-flex flex-wrap align-items-center pb-xl-0">


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


                            <!-- currency -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="currency" name="filter[currency]">
                                        <option value="">{{ __('Currency') }}</option>
                                        @foreach($virtualcardCurrencies as $currency)
                                            <option value="{{ $currency->currency_code }}" {{ $filter && $filter['currency'] == $currency->currency_code ? 'selected' : '' }}>{{ $currency->currency_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Brands -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="brand" name="filter[brand]">
                                        <option value="">{{ __('Brand') }}</option>
                                        @foreach($virtualcardBrands as $brand)
                                            <option value="{{ $brand->card_brand }}" {{ $filter && $filter['brand'] == $brand->card_brand ? 'selected' : '' }}>{{ $brand->card_brand }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="status" name="filter[status]">
                                        <option value="">{{ __('Status') }}</option>
                                        @foreach($virtualcardTopupStatuses as $status)
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
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('user.topup.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn ">
                                <span class="f-14 leading-29 gilroy-medium"> + {{ __('New Topup') }}</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Filter Section End-->

            <div>
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
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Fees') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Total') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Status') }}</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($virtualcardTopups as $key => $topup)

                            @php
                                $fees  = $topup->percentage_fees + $topup->fixed_fees;
                                $total = $fees + $topup->requested_fund;
                            @endphp
                            <tr class="bg-white">

                                <td class="w-270p d-flex align-items-center">
                                    @if ($topup->virtualcard?->card_number && $topup->virtualcard?->card_brand == 'Visa Card')
                                        {!! virtualcardSvgIcons('visa_card_icon') !!}
                                    @elseif ($topup->virtualcard?->card_number && $topup->virtualcard?->card_brand == 'Master Card')
                                        {!! virtualcardSvgIcons('master_card_icon') !!}
                                    @endif
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $topup->virtualcard?->card_number ? maskCardNumberForLogo($topup->virtualcard?->card_number) : '-' }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ optional($topup->virtualcard)->currency_code }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($topup->requested_fund, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($fees, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($total, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                </td>
                                <td>
                                    <span class="f-12 gilroy-medium custom-badge d-flex justify-content-center align-items-center px-0 {{ getColor($topup->fund_approval_status) }}  r-mt-open">{{ $topup->fund_approval_status }}</span>
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
                                                    <p class="mb-0 mt-28 text-dark gilroy-medium f-15 r-f-12 r-mt-18 text-center">{{ __('Topup Amount') }}</p>
                                                    <p class="mb-0 text-dark gilroy-Semibold f-24 leading-29 r-f-26 text-center l-s2 mt-10">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($topup->requested_fund, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                                    <p class="mb-0 mt-18 text-gray-100 gilroy-medium f-13 leading-20 r-f-14 text-center">{{ dateFormat($topup->created_at) }}</p>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('user.topup.print', $topup->id) }}" class="infoBtn-print cursor-pointer f-14 gilroy-medium text-dark mt-35 d-flex justify-content-center align-items-center" target="__blank">
                                                            {!! svgIcons('printer') !!}&nbsp;
                                                            <span>{{ __('Print') }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="ml-20 trans-details">
                                                    <p class="mb-0 mt-9 text-dark dark-5B f-20 gilroy-Semibold transac-title">{{ __('Transaction Details') }}</p>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Wallet') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($topup->virtualcard)->currency_code }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ 'Card Brand' }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($topup->virtualcard)->card_brand }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Transaction ID') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($topup->transaction)->uuid }}</p>
                                                        </div>

                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Transaction Fee') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">
                                                                    {{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($fees, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Card Number') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ maskCardNumber(optional($topup->virtualcard)->card_number) }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Status') }}</p>
                                                            <p id="status_{{ $topup->id }}" class="mb-0 mt-5p {{ getColor($topup->fund_approval_status) }} gilroy-medium f-15 leading-22 r-text">{{ getStatus($topup->fund_approval_status) }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="hr-border w-100 mb-0"></p>
                                                    <div class="row gx-sm-5">

                                                        <!-- Amount -->
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 dark-B87 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ 'Topup' }}&nbsp;{{ __('Amount') }}</p>
                                                            <p class="mb-0 mt-5p text-dark dark-CDO gilroy-medium f-15 leading-22 r-text">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($topup->requested_fund, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                                        </div>

                                                        <!-- Total Amount -->
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 dark-B87 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Total Amount') }}</p>
                                                            <p class="mb-0 mt-5p text-dark dark-CDO gilroy-medium f-15 leading-22 r-text"> {{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($total, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                                        </div>
                                                        @if ($topup->cancellation_reason  && $topup->fund_approval_status == 'Cancelled')
                                                                <div class="row gx-sm-5">
                                                                    <div class="col-12">
                                                                        <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Cancellation Reason') }}</p>
                                                                        <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $topup->cancellation_reason }}</p>
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
            <div class="mt-22 mt-sm-4">
                <form method="get">
                    <div>
                        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center pb-26">
                            <div class="d-flex flex-wrap align-items-center pb-xl-0">


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


                            <!-- currency -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="currency" name="filter[currency]">
                                        <option value="">{{ __('Currency') }}</option>
                                        @foreach($virtualcardCurrencies as $currency)
                                            <option value="{{ $currency->currency_code }}" {{ $filter && $filter['currency'] == $currency->currency_code ? 'selected' : '' }}>{{ $currency->currency_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Brands -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="brand" name="filter[brand]">
                                        <option value="">{{ __('Brand') }}</option>
                                        @foreach($virtualcardBrands as $brand)
                                            <option value="{{ $brand->card_brand }}" {{ $filter && $filter['brand'] == $brand->card_brand ? 'selected' : '' }}>{{ $brand->card_brand }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="status" name="filter[status]">
                                        <option value="">{{ __('Status') }}</option>
                                        @foreach($virtualcardTopupStatuses as $status)
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
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('user.topup.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn ">
                                <span class="f-14 leading-29 gilroy-medium"> + {{ __('New Topup') }}</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="notfound mt-16 bg-white p-4 shadow">
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-26">
                    <div class="image-notfound">
                        <img src="{{ asset('public/dist/images/not-found.png') }}" class="img-fluid">
                    </div>
                    <div class="text-notfound">
                        <p class="mb-0 f-20 leading-25 gilroy-medium text-dark">{{ __('Sorry!') }} {{ __('No data found.') }}</p>
                        <p class="mb-0 f-16 leading-24 gilroy-regular text-gray-100 mt-12">{{ __('The requested data does not exist for this feature overview.') }}</p>
                        <div class="d-flex mt-14 r-mt-22 align-items-center">
                            <a href="{{ route('user.topup.create') }}" class="bg-primary text-white Add-new-btn w-176 addnew text-center green-btn ">
                                <span class="f-14 leading-29 gilroy-medium"> {{ __('Topup Now') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    <!-- Unfiltered Section -->
    @elseif (\Illuminate\Support\Facades\Request::is('virtualcard/topups') && ! \Illuminate\Support\Facades\Request::query())
        @if($virtualcardTopups->count() > 0)
            <!-- Filter Section -->
            <div class="mt-22 mt-sm-4">
                <form method="get">
                    <div>
                        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center pb-26">
                            <div class="d-flex flex-wrap align-items-center pb-xl-0">


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

                            <!-- currency -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="currency" name="filter[currency]">
                                        <option value="">{{ __('Currency') }}</option>
                                        @foreach($virtualcardCurrencies as $currency)
                                            <option value="{{ $currency->currency_code }}" {{ $filter && $filter['currency'] == $currency->currency_code ? 'selected' : '' }}>{{ $currency->currency_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Brands -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="brand" name="filter[brand]">
                                        <option value="">{{ __('Brand') }}</option>
                                        @foreach($virtualcardBrands as $brand)
                                            <option value="{{ $brand->card_brand }}" {{ $filter && $filter['brand'] == $brand->card_brand ? 'selected' : '' }}>{{ $brand->card_brand }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="me-2">
                                <div class="param-ref filter-ref w-135 h-45">
                                    <select class="select2 f-13" data-minimum-results-for-search="Infinity" id="status" name="filter[status]">
                                        <option value="">{{ __('Status') }}</option>
                                        @foreach($virtualcardTopupStatuses as $status)
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
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('user.topup.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn ">
                                <span class="f-14 leading-29 gilroy-medium"> + {{ __('New Topup') }}</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Filter Section End-->

            <div>
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
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Fees') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Total') }}</p>
                                </th>
                                <th class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Status') }}</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($virtualcardTopups as $key => $topup)

                            @php
                                $fees  = $topup->percentage_fees + $topup->fixed_fees;
                                $total = $fees + $topup->requested_fund;
                            @endphp
                            <tr class="bg-white">

                                <td class="w-270p d-flex align-items-center">
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {!! $topup->virtualcard?->card_number ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $topup->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($topup->virtualcard?->card_number) : '-' !!}
                                    </p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ optional($topup->virtualcard)->currency_code }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($topup->requested_fund, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($fees, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($total, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                </td>
                                <td>
                                    <span
                                        class="f-12 gilroy-medium custom-badge d-flex justify-content-center align-items-center px-0 {{ getColor($topup->fund_approval_status) }}  r-mt-open">{{ $topup->fund_approval_status }}</span>
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
                                                    <p class="mb-0 mt-28 text-dark gilroy-medium f-15 r-f-12 r-mt-18 text-center">{{ __('Topup Amount') }}</p>
                                                    <p class="mb-0 text-dark gilroy-Semibold f-24 leading-29 r-f-26 text-center l-s2 mt-10">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($topup->requested_fund, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                                    <p class="mb-0 mt-18 text-gray-100 gilroy-medium f-13 leading-20 r-f-14 text-center">{{ dateFormat($topup->created_at) }}</p>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('user.topup.print', $topup->id) }}" class="infoBtn-print cursor-pointer f-14 gilroy-medium text-dark mt-35 d-flex justify-content-center align-items-center" target="__blank">
                                                            {!! svgIcons('printer') !!}&nbsp;
                                                            <span>{{ __('Print') }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="ml-20 trans-details">
                                                    <p class="mb-0 mt-9 text-dark dark-5B f-20 gilroy-Semibold transac-title">{{ __('Transaction Details') }}</p>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Wallet') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($topup->virtualcard)->currency_code }}</p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ 'Card Brand' }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($topup->virtualcard)->card_brand }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Transaction ID') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ optional($topup->transaction)->uuid }}</p>
                                                        </div>

                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Transaction Fee') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">
                                                                    {{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($fees, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row gx-sm-5">
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Card Number') }}</p>
                                                            <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">
                                                                {!! $topup->virtualcard?->card_number ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $topup->virtualcard?->card_brand)) . '_icon') .  maskCardNumberForLogo($topup->virtualcard?->card_number) : '-' !!}
                                                            </p>
                                                        </div>
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Status') }}</p>
                                                            <p id="status_{{ $topup->id }}" class="mb-0 mt-5p {{ getColor($topup->fund_approval_status) }} gilroy-medium f-15 leading-22 r-text">{{ getStatus($topup->fund_approval_status) }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="hr-border w-100 mb-0"></p>
                                                    <div class="row gx-sm-5">

                                                        <!-- Amount -->
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 dark-B87 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ 'Topup' }}&nbsp;{{ __('Amount') }}</p>
                                                            <p class="mb-0 mt-5p text-dark dark-CDO gilroy-medium f-15 leading-22 r-text">{{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($topup->requested_fund, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                                        </div>

                                                        <!-- Total Amount -->
                                                        <div class="col-6">
                                                            <p class="mb-0 mt-4 text-gray-100 dark-B87 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Total Amount') }}</p>
                                                            <p class="mb-0 mt-5p text-dark dark-CDO gilroy-medium f-15 leading-22 r-text"> {{ moneyFormat(currencyDetails(optional($topup->virtualcard)->currency_code)->symbol, formatNumber($total, currencyDetails(optional($topup->virtualcard)->currency_code)->id)) }}</p>
                                                        </div>
                                                        @if ($topup->cancellation_reason  && $topup->fund_approval_status == 'Cancelled')
                                                            <div class="row gx-sm-5">
                                                                <div class="col-12">
                                                                    <p class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Cancellation Reason') }}</p>
                                                                    <p class="mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $topup->cancellation_reason }}</p>
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
                        @if (count($virtualCards))
                            <div class="d-flex mt-14 r-mt-22 align-items-center">
                                <a href="{{ route('user.topup.create') }}" class="bg-primary text-white Add-new-btn w-176 addnew text-center green-btn ">
                                    <span class="f-14 leading-29 gilroy-medium"> {{ __('Topup Now') }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif
    <div class="mt-4">
        {{ $virtualcardTopups->links('vendor.pagination.bootstrap-5') }}
    </div>

@endsection

@push('js')
<script src="{{ asset('public/dist/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
<script src="{{ asset('public/user/customs/js/daterange-select.js') }}"></script>
<script type="text/javascript">
    'use strict';
    var filterUrl = '{{ route("user.topup.index") }}';
    var sessionDateFormateType = "{{ Session::get('date_format_type') }}";
    var startDate = "{!! isset($filter['from']) ? setDateForDb($filter['from']) : '' !!}";
    var endDate = "{!! isset($filter['to']) ? setDateForDb($filter['to']) : '' !!}";
    let dateRangePickerText = "{{ __('Pick a date range') }}";
</script>
<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/user/virtualcard.min.js') }}"></script>

@endpush
