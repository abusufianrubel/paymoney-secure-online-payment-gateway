@extends('user.layouts.app')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/virtual-card.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/virtualcard_otpform.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/libraries/sweetalert/sweetalert.min.css') }}">
@endpush

@section('content')
    <main>
        <!-- Header Section -->
        <div class="mb-4 d-flex justify-content-between dash-left-profile dash-profile-flex-wrap align-items-center">
            <h1 class="gilroy-Semibold f-26 breadcrumb-item capitalize text-dark mt-4">{{ __('Card Details') }}</h1>
              
            @if ($virtualcard->status != 'Inactive') 
            <div class="dash-right-profile d-flex align-items-end">
                    <a href="{{ route('user.topup.create', ['card_id' => $virtualcard->id]) }}" class="btn btn-lg btn-primary w-134">
                        <span class="mb-0 f-14 leading-20 gilroy-medium">{{ $virtualcard->currency()?->symbol . ' ' . __('Topup') }}</span>
                    </a>
                    <a href="{{ route('user.virtualcard_withdrawal.create', ['card_id' => $virtualcard->id]) }}" class="btn btn-lg btn-warning cursor-pointer ml-12 w-150 yellow-btn">
                        <span class="mb-0 f-14 leading-20 gilroy-medium text-dark">{{ $virtualcard->currency()?->symbol }} {{ __('Withdraw') }}</span>
                    </a>
            </div>
           @endif
        </div>
        <!-- End Header Section -->

        @if (!$virtualcard->spendingControls->isEmpty())
            <!-- Card Section -->
            <div class="row g-3">

                <!-- Card -->
                <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-6">
                    <!-- Visa  Card-->
                    @if ($virtualcard->card_brand == \Modules\Virtualcard\Enums\CardBrands::VISACARD->value)
                        <div class="position-relative h-64 rounded-xl p-6 shadow bg-gradient-1"
                            style="background-image:url({{ asset('Modules/Virtualcard/Resources/assets/images/visacard.png') }})">
                            <div class="d-flex align-items-center justify-content-between">
                                {!! virtualcardSvgIcons('card_network') !!}
                                {!! virtualcardSvgIcons('visa_logo') !!}
                            </div>
                            <div id="card-number" class="text-2xl font-semibold text-white gilroy-Semibold">
                                {{ maskCardNumber($virtualcard->card_number) }}</div>
                            <div class="mt-3 grid grid-cols-5 gap-2 text-white">
                                <div class="col-span-2 mt-2">
                                    <p class="text-sm  gilroy-medium mb-0">{{ __('Name') }}</p>
                                    <p id="card-holder" class="mt-2 text-xs gilroy-regular text-capitalize">
                                        {{ cardTitle($virtualcard->virtualcardHolder) }}</p>
                                </div>
                                <div>
                                    <p class="whitespace-nowrap text-sm gilroy-medium mb-0 mt-2">{{ __('Expiry date') }}</p>
                                    <p id="exp-date" class="mt-2 text-xs gilroy-regular">
                                        {{ formatCardExpiryDate($virtualcard->expiry_month, $virtualcard->expiry_year) }}
                                    </p>
                                </div>
                                <div class="ml-30px">
                                    <p class="mb-2 text-center text-sm gilroy-medium mt-2">{{ __('CVV') }}</p>
                                    <div id="card-cvc" class="ml-24p text-center gilroy-regular">
                                        {{ $virtualcard->card_cvc ? str_repeat('*', strlen($virtualcard->card_cvc)) : '***' }}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-end">
                                    <span>
                                        {!! virtualcardSvgIcons('eye_off') !!}
                                        {!! virtualcardSvgIcons('loader') !!}
                                        {!! virtualcardSvgIcons('eye_on') !!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- End Visa Card -->

                    <!-- Mastercard -->
                    @if ($virtualcard->card_brand == \Modules\Virtualcard\Enums\CardBrands::MASTERCARD->value)
                        <div class="position-relative h-64 rounded-xl p-6 shadow bg-gradient-2"
                            style="background-image: url({{ asset('Modules/Virtualcard/Resources/assets/images/mastercard.png') }})">
                            <div class="d-flex align-items-center justify-content-between">
                                {!! virtualcardSvgIcons('card_network') !!}
                                {!! virtualcardSvgIcons('master_logo') !!}
                            </div>
                            <div id="card-number" class="text-2xl font-semibold text-white gilroy-Semibold">
                                {{ maskCardNumber($virtualcard->card_number) }}</div>
                            <div class="mt-3 grid grid-cols-5 gap-2 text-white">
                                <div class="col-span-2 mt-2">
                                    <p class="text-sm  gilroy-medium mb-0">{{ __('Name') }}</p>
                                    <p id="card-holder" class="mt-2 text-xs gilroy-regular text-capitalize">
                                        {{ cardTitle($virtualcard->virtualcardHolder) }}</p>
                                </div>
                                <div>
                                    <p class="whitespace-nowrap text-sm gilroy-medium mb-0 mt-2">{{ __('Expiry date') }}</p>
                                    <p id="exp-date" class="mt-2 text-xs gilroy-regular">
                                        {{ formatCardExpiryDate($virtualcard->expiry_month, $virtualcard->expiry_year) }}
                                    </p>
                                </div>
                                <div class="ml-30px">
                                    <p class="mb-2 text-center text-sm gilroy-medium mt-2">{{ __('CVC') }}</p>
                                    <div id="card-cvc" class="ml-24p text-center gilroy-regular">
                                        {{ str_repeat('*', strlen($virtualcard->card_cvc)) }}</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-end">
                                    <span>
                                        {!! virtualcardSvgIcons('eye_off') !!}
                                        {!! virtualcardSvgIcons('loader') !!}
                                        {!! virtualcardSvgIcons('eye_on') !!}
                                    </span>
                                </div>
                            </div>

                        </div>
                    @endif
                    <!-- End Mastercard -->
                </div>
                <!-- End Card -->

                <!-- Spending Limit -->
                <div class="col-xxl-3 col-xl-3 col-lg-5 col-md-6">
                    <div class="bg-white rounded-xl pb-5 h-100 spending-container d-flex justify-content-between ">
                        <div>
                            <p class="text-lg text-dark gilroy-bold mb-3">{{ __('Spending Limit') }}</p>
                            
                            @foreach ($virtualcard->spendingControls as $control)
                                <p class="f-14 gilroy-medium text-dark mb-1">{{ ucfirst(str_replace('_', ' ', $control->interval)) }} : <span class="limit f-16 leading-18 gilroy-bold text-info-200"> {{ moneyFormat($virtualcard->currency()?->symbol, formatNumber($control->amount, $virtualcard->currency()?->id)) }} </span></p>
                            @endforeach

                        </div>
                        <span class="dollar-container ms-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="20" viewBox="0 0 12 20"
                                fill="none">
                                <path
                                    d="M11.1861 13.2532C11.2701 11.9092 10.5771 10.9222 9.88411 10.3342C9.46411 9.93516 8.54011 9.49416 8.05711 9.30516C7.82611 9.20016 7.49011 9.07416 7.04911 8.90616V4.74816C7.93111 4.93716 8.56111 5.50416 8.91811 6.40716L10.9551 5.23116C10.2201 3.57216 8.91811 2.60615 7.04911 2.35416V0.485156H5.68411V2.33316C4.52911 2.43816 3.54211 2.85816 2.76511 3.59316C1.98811 4.32816 1.58911 5.27316 1.58911 6.44916C1.58911 7.73016 2.03011 8.69616 2.89111 9.36816C3.79411 10.0402 4.42411 10.3132 5.68411 10.7962V15.1642C4.38211 15.0172 3.47911 14.3452 2.99611 13.1062L0.917109 14.3032C1.65211 16.2352 3.35311 17.3902 5.68411 17.5372V19.3852H7.04911V17.5162C9.50611 17.2012 11.1861 15.6262 11.1861 13.2532ZM4.00411 6.44916C4.00411 5.52516 4.65511 4.87416 5.68411 4.70616V8.36016C4.48711 7.79316 4.00411 7.31016 4.00411 6.44916ZM7.04911 11.3212C8.22511 11.8462 8.77111 12.3502 8.77111 13.2742C8.77111 14.1982 8.16211 14.9122 7.04911 15.1432V11.3212Z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <!-- End Spending Limit -->

                <!-- Card details -->
                <div class="col-xl-4 col-lg-7 col-md-6">
                    <div class="row g-2 rounded-xl details-container bg-white h-100 mt-0 pb-26">

                        <!-- Total Balance -->
                        <div class="col-6">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold ">
                                {{ __('Total Balance') }}</p>
                            <p class="balance  f-16 leading-18 text-info-200 gilroy-medium">
                                {{ moneyFormat($virtualcard->currency()?->symbol, formatNumber($virtualcard->amount, $virtualcard->currency()?->id)) }}
                            </p>
                        </div>

                        <!-- Available Balance -->
                        <div class="col-6">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold">
                                {{ __('Available Balance') }}</p>
                            <p class="limit  f-16 leading-18 text-info-200 gilroy-medium">
                                {{ moneyFormat($virtualcard->currency()?->symbol, formatNumber($virtualcard->amount - $totalSpent, $virtualcard->currency()?->id)) }}
                            </p>
                        </div>

                        <!-- Spent -->
                        <div class="col-6">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold ">
                                {{ __('Total Spent') }}</p>
                            <p class="limit f-16 leading-18 text-info-200 gilroy-medium">
                                {{ moneyFormat($virtualcard->currency()?->symbol, formatNumber($totalSpent, $virtualcard->currency()?->id)) }}
                            </p>
                        </div>

                        <!-- Card Type -->
                        <div class="col-6 ">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold">
                                {{ __('Card Currency') }}</p>
                            <p class="limit f-16 leading-18 text-info-200 gilroy-medium">{{ $virtualcard->currency_code }}
                            </p>
                        </div>

                        <!-- Card Status -->
                        <div class="col-6">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold">
                                {{ __('Status') }}
                            </p>
                            <p
                                class="text-xs text-white font-semibold mb-0 w-max px-2 py-half rounded-200p bg-{{ getBgColor($virtualcard->status) }}">
                                {{ $virtualcard->status }}</p>
                        </div>
                    </div>
                </div> 
                <!-- End Card Details -->
                
            </div>
            <!-- End Card Section -->
        @else 
            <div class="row g-3">

                <!-- Card -->
                <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-6">
                    <!-- Visa  Card-->
                    @if ($virtualcard->card_brand == \Modules\Virtualcard\Enums\CardBrands::VISACARD->value)
                        <div class="position-relative h-64 rounded-xl p-6 shadow bg-gradient-1"
                            style="background-image:url({{ asset('Modules/Virtualcard/Resources/assets/images/visacard.png') }})">
                            <div class="d-flex align-items-center justify-content-between">
                                {!! virtualcardSvgIcons('card_network') !!}
                                {!! virtualcardSvgIcons('visa_logo') !!}
                            </div>
                            <div id="card-number" class="text-2xl font-semibold text-white gilroy-Semibold">
                                {{ maskCardNumber($virtualcard->card_number) }}</div>
                            <div class="mt-3 grid grid-cols-5 gap-2 text-white">
                                <div class="col-span-2 mt-2">
                                    <p class="text-sm  gilroy-medium mb-0">{{ __('Name') }}</p>
                                    <p id="card-holder" class="mt-2 text-xs gilroy-regular text-capitalize">
                                        {{ cardTitle($virtualcard->virtualcardHolder) }}</p>
                                </div>
                                <div>
                                    <p class="whitespace-nowrap text-sm gilroy-medium mb-0 mt-2">{{ __('Expiry date') }}</p>
                                    <p id="exp-date" class="mt-2 text-xs gilroy-regular">
                                    {{ formatCardExpiryDate($virtualcard->expiry_month, $virtualcard->expiry_year) }}
                                    </p>
                                </div>
                                <div class="ml-30px">
                                    <p class="mb-2 text-center text-sm gilroy-medium mt-2">{{ __('CVV') }}</p>
                                    <div id="card-cvc" class="ml-24p text-center gilroy-regular">
                                        {{ $virtualcard->card_cvc ? str_repeat('*', strlen($virtualcard->card_cvc)) : '***' }}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-end">
                                    <span>
                                        {!! virtualcardSvgIcons('eye_off') !!}
                                        {!! virtualcardSvgIcons('loader') !!}
                                        {!! virtualcardSvgIcons('eye_on') !!}
                                    </span>

                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- End Visa Card -->

                    <!-- Mastercard -->
                    @if ($virtualcard->card_brand == \Modules\Virtualcard\Enums\CardBrands::MASTERCARD->value)
                        <div class="position-relative h-64 rounded-xl p-6 shadow bg-gradient-2"
                            style="background-image: url({{ asset('Modules/Virtualcard/Resources/assets/images/mastercard.png') }})">
                            <div class="d-flex align-items-center justify-content-between">
                                {!! virtualcardSvgIcons('card_network') !!}
                                {!! virtualcardSvgIcons('master_logo') !!}
                            </div>
                            <div id="card-number" class="text-2xl font-semibold text-white gilroy-Semibold">
                                {{ maskCardNumber($virtualcard->card_number) }}</div>
                            <div class="mt-3 grid grid-cols-5 gap-2 text-white">
                                <div class="col-span-2 mt-2">
                                    <p class="text-sm  gilroy-medium mb-0">{{ __('Name') }}</p>
                                    <p id="card-holder" class="mt-2 text-xs gilroy-regular text-capitalize">
                                        {{ cardTitle($virtualcard->virtualcardHolder) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="whitespace-nowrap text-sm gilroy-medium mb-0 mt-2">{{ __('Expiry date') }}</p>
                                    <p id="exp-date" class="mt-2 text-xs gilroy-regular">
                                    {{ formatCardExpiryDate($virtualcard->expiry_month, $virtualcard->expiry_year) }}
                                    </p>
                                </div>
                                <div class="ml-30px">
                                    <p class="mb-2 text-center text-sm gilroy-medium mt-2">{{ __('CVC') }}</p>
                                    <div id="card-cvc" class="ml-24p text-center gilroy-regular">
                                        {{ str_repeat('*', strlen($virtualcard->card_cvc)) }}</div>
                                </div>
                                <div class="d-flex align-items-center justify-content-end">
                                    <span>
                                        {!! virtualcardSvgIcons('eye_off') !!}
                                        {!! virtualcardSvgIcons('loader') !!}
                                        {!! virtualcardSvgIcons('eye_on') !!}
                                    </span>
                                </div>
                            </div>

                        </div>
                    @endif
                    <!-- End Mastercard -->
                </div>
                <!-- End Card -->

                <div class="col-xxl-5 col-xl-6 col-lg-5 col-md-6">
                    <div class="row g-2 rounded-xl details-container bg-white h-100 mt-0 pb-26">

                        <!-- Total Balance -->
                        <div class="col-6">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold ">
                                {{ __('Total Balance') }}</p>
                            <p class="balance  f-16 leading-18 text-info-200 gilroy-medium">
                                {{ moneyFormat($virtualcard->currency()?->symbol, formatNumber($virtualcard->amount, $virtualcard->currency()?->id)) }}
                            </p>
                        </div>

                        <!-- Available Balance -->
                        <div class="col-6">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold">
                                {{ __('Available Balance') }}</p>
                            <p class="limit  f-16 leading-18 text-info-200 gilroy-medium">
                                {{ moneyFormat($virtualcard->currency()?->symbol, formatNumber($virtualcard->amount - $totalSpent, $virtualcard->currency()?->id)) }}
                            </p>
                        </div>

                        <!-- Spent -->
                        <div class="col-6">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold ">
                                {{ __('Total Spent') }}</p>
                            <p class="limit f-16 leading-18 text-info-200 gilroy-medium">
                                {{ moneyFormat($virtualcard->currency()?->symbol, formatNumber($totalSpent, $virtualcard->currency()?->id)) }}
                            </p>
                        </div>

                        <!-- Card Type -->
                        <div class="col-6 ">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold">
                                {{ __('Card Currency') }}</p>
                            <p class="limit f-16 leading-18 text-info-200 gilroy-medium">{{ $virtualcard->currency_code }}
                            </p>
                        </div>

                        <!-- Card Status -->
                        <div class="col-6">
                            <p class="mb-2 f-18 leading-18 text-dark gilroy-Semibold">
                                {{ __('Status') }}
                            </p>
                            <p
                                class="text-xs text-white font-semibold mb-0 w-max px-2 py-half rounded-200p bg-{{ getBgColor($virtualcard->status) }}">
                                {{ $virtualcard->status }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- End Card Section -->
        @if ($transactions->count() > 0 && $virtualcard->virtualcardProvider?->type == 'Manual')
            <p class="mb-0 gilroy-Semibold f-26 text-dark theme-tran r-f-20 text-left pt-5">
                {{ __('Topup & Withdrawal History') }}</p>
            <div class="table-responsive table-scrolbar thin-scrollbar">
                <table class="merchant-payments table-curved table recent_activity table-bordered mt-4">
                    <thead>
                        <tr class="payment-parent-section-title component-table-one">
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Transaction ID') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Type') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Amount') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Fees') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Total') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Currency') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Status') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Date') }}</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr class="bg-white">
                                <td>
                                    <p class="mb-0 f-18 gilroy-medium text-dark text-start">
                                        {{ $transaction->uuid }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ str_replace('Virtualcard_', '', $transaction->transaction_type?->name) }}
                                    </p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ formatNumber($transaction->subtotal, $transaction->currency_id) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ formatNumber($transaction->charge_percentage + $transaction->charge_fixed, $transaction->currency_id) }}
                                    </p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ formatNumber($transaction->total, $transaction->currency_id) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ $transaction->currency?->code }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ dateFormat($transaction->created_at) }}</p>
                                </td>
                                <td>
                                    <p
                                        class="mb-0 f-16 leading-17 gilroy-medium {{ getColor($transaction->status) }} text-start">
                                        {{ $transaction->status }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <!-- End Manual card Transactions -->

        <!-- Automatic card Spent log -->
        @if ($automatedTransactions->count() > 0 && $virtualcard->virtualcardProvider?->type == 'Automated')
            <p class="mb-0 gilroy-Semibold f-26 text-dark theme-tran r-f-20 text-left pt-5">{{ __('Spent Log') }}</p>
            <div class="table-responsive table-scrolbar thin-scrollbar">
                <table class="merchant-payments table-curved table recent_activity table-bordered mt-4">
                    <thead>
                        <tr class="payment-parent-section-title component-table-one">
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Transaction ID') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Amount') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Fees') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Total') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">
                                    {{ __('Currency') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Date') }}</p>
                            </th>
                            <th class="p-0 pb-6">
                                <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Status') }}</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($automatedTransactions as $transaction)
                            <tr class="bg-white">
                                <td class="">
                                    <h6 class="mb-0 f-18 gilroy-medium text-dark text-start">
                                        {{ $transaction->transaction_id }}</h6>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ formatNumber($transaction->amount, $transaction->virtualcard?->currency()?->id) }}
                                    </p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ formatNumber($transaction->transaction_fees, $transaction->virtualcard?->currency()?->id) }}
                                    </p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ formatNumber($transaction->transaction_fees + $transaction->amount, $transaction->virtualcard?->currency()?->id) }}
                                    </p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ $transaction->virtualcard?->currency()?->code }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">
                                        {{ dateFormat($transaction->created_at) }}</p>
                                </td>
                                <td>
                                    <p
                                        class="mb-0 f-16 leading-17 gilroy-medium {{ getColor($transaction->status) }} text-start">
                                        {{ $transaction->status }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <!-- End Automatic card Spent log -->

    </main>

    <div class="modal fade modal-overly" id="exampleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg res-dialog">
            <div class="modal-content">
                <div class="modal-content">
                    <div class="otp-form">

                        <h2 class="mb-0 gilroy-Semibold f-22 text-dark theme-tran r-f-20 text-center">
                            {{ __('OTP Verification') }}</h2>
                        <p class=" mb-0 f-16 leading-20 gilroy-medium text-gray-100">
                            {{ __('The card will be visible for 1 minute') }}</p>
                        @if (checkDemoEnvironment())
                        <p class=" mb-0 f-14 text-primary leading-17 gilroy-regular">
                            {{ __('For demo use 000000 as OTP') }}</p> 
                        @endif
                        <div class="otp-container">
                            <!-- Six input fields for OTP digits -->
                            <input type="text"
                                class="otp-input form-control input-form-control input-form-control-withdraw apply-bg not-focus-bg"
                                pattern="\d" maxlength="1">
                            <input type="text"
                                class="otp-input form-control input-form-control input-form-control-withdraw apply-bg not-focus-bg"
                                pattern="\d" maxlength="1" disabled>
                            <input type="text"
                                class="otp-input form-control input-form-control input-form-control-withdraw apply-bg not-focus-bg"
                                pattern="\d" maxlength="1" disabled>
                            <input type="text"
                                class="otp-input form-control input-form-control input-form-control-withdraw apply-bg not-focus-bg"
                                pattern="\d" maxlength="1" disabled>
                            <input type="text"
                                class="otp-input form-control input-form-control input-form-control-withdraw apply-bg not-focus-bg"
                                pattern="\d" maxlength="1" disabled>
                            <input type="text"
                                class="otp-input form-control input-form-control input-form-control-withdraw apply-bg not-focus-bg"
                                pattern="\d" maxlength="1" disabled>
                        </div>

                        <!-- Field to display entered OTP -->
                        <input type="hidden" id="verificationCode" placeholder="Enter verification code" readonly>

                        <!-- Button to verify OTP -->
                        <button type="submit" class="btn bg-primary" id="verifyOtpBtn">
                            <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            <span id="defaultCurrencySubmitBtnText">{!! __('Verify &amp; Proceed') !!}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('js')
        <script src="{{ asset('public/dist/libraries/sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
        <script>
            'use strict';
            let virtualcardId = '{{ $virtualcard->id }}';
            let sendingOtpText = "{{ __('Sending OTP...') }}";
            let buttonText = "{{ __('Reveal Card Details') }}";
            let authUser = "{{ auth()->id() }}";
            let _token = "{{ csrf_token() }}";
            let sendOtpUrl = "{{ route('user.virtualcard.send_otp') }}";
            let verifyOtpUrl = "{{ route('user.virtualcard.verify_otp') }}";
            let cardUnmasked = false;
            let maskTimeout;
            let failedTitle = "{{ __('Failed') }}";
            let failedText = "{{ __('Failed to send OTP. Please try again.') }}";
            let errorText = "{{ __('An error occurred. Please try again later.') }}";
            let invalidOTP = "{{ __('Invalid OTP. Please try again.') }}";
        </script>
        <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/user/virtualcard_show.min.js') }}"></script>
    @endpush
