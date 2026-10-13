@extends('user.layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/virtual-card.min.css') }}">
@endpush

@section('content')

<div id="VirtualcardIndex">

    @if ($virtualcards->where('status', 'Pending')->count() > 0 && $virtualcards->where('status', 'Active')->count() < 0)
        <div class="alert alert-warning" role="alert">
            {{ __('Please use the filter option to find cards with a Pending status') }}
        </div>
    @endif

    @include('user.common.alert')

    <div class="text-center">
        <p class="mb-0 gilroy-Semibold f-26 text-dark theme-tran r-f-20">{{ __('Virtual Cards') }}</p>
        <p class="mb-0 gilroy-medium text-gray-100 f-16 r-f-12 mt-2 tran-title">
            {{ __('List of all the virtual cards you have') }}</p>
    </div>

    <!-- Filtered Data -->
    @if (\Illuminate\Support\Facades\Request::is('virtualcard/cards') &&  \Illuminate\Support\Facades\Request::query())

        @if($virtualcards->count() > 0)
            <div>
                <!-- Filter Section -->
                <div class="mt-22 mt-sm-4">
                    <form method="get" id="virtualcardForm">
                        <div class="filter-panel">
                            <div class="d-flex flex-wrap justify-content-between gap-2  align-items-centerpb-26">
                                <div class="d-flex flex-wrap align-items-center pb-xl-0">

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
                                            @foreach($virtualcardStatuses as $status)
                                                <option value="{{ $status->status }}" {{ $filter && $filter['status'] == $status->status ? 'selected' : '' }}>{{ $status->status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="filter-btn f-14 gilroy-medium leading-17">{{ __('Filter') }}</button>
                                </div>
                            </div>
                            @if (count($cardholders))
                            <div class="d-flex justify-content-end align-items-center ">

                                <a href="{{ route('user.virtualcard.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn cursor-pointer">
                                    <span class="f-14 leading-29 gilroy-medium">+ {{ __('New Card') }}</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
                <!-- Filter Section -->

                <div class="table-responsive table-scrolbar thin-scrollbar mt-4">
                    <table class="merchant-payments table-curved table recent_activity table-bordered mt-4">
                        <thead>
                        <tr class="payment-parent-section-title component-table-one">
                                <th width="30%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Number') }}</p>
                                </th>

                                <th width="15%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Expired') }}</p>
                                </th>

                                <th width="15%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Card Holder') }}</p>
                                </th>
                                <th width="15%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start whitespace-nowrap">{{ __('Currency') }}</p>
                                </th>

                                <th width="15%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Status') }}</p>
                                </th>
                                <th width="10%" class="p-0 pb-6 pe-2">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-end pe-2">{{ __('Action') }}</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($virtualcards as $virtualcard)
                            <tr class="bg-white">

                                <td class="d-flex align-items-center">
                                    @if ($virtualcard->card_number && $virtualcard->card_brand == 'Visa Card')
                                        {!! virtualcardSvgIcons('visa_card_icon') !!}
                                    @elseif ($virtualcard->card_number && $virtualcard->card_brand == 'Master Card')
                                        {!! virtualcardSvgIcons('master_card_icon') !!}
                                    @endif
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcard->card_number ? maskCardNumberForLogo($virtualcard->card_number) : '-' }}</p>
                                </td>

                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ formatCardExpiryDate($virtualcard->expiry_month, $virtualcard->expiry_year) }}</p>
                                </td>

                                <td class="w-270p">
                                    <p class="mb-0 f-18 gilroy-medium text-dark text-start w-270p whitespace-nowrap">{{ cardTitle($virtualcard->virtualcardHolder) }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcard->currency_code }}</p>
                                </td>

                                <td>
                                    <span class="f-12 gilroy-medium custom-badge d-flex justify-content-center align-items-center px-0 {{ getColor($virtualcard->status) }}  r-mt-open">{{ $virtualcard->status }}</span>
                                </td>

                                <td class="text-end">
                                    @if ($virtualcard->status == 'Active')
                                        <a class="arrow-hovers cursor-pointer transaction-arrow text-right"
                                            href="{{ route('user.virtualcard.show', $virtualcard) }}">
                                            <svg class="nscaleX-1" width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M3.5312 1.52861C3.27085 1.78896 3.27085 2.21107 3.5312 2.47141L7.0598 6.00001L3.5312 9.52861C3.27085 9.78895 3.27085 10.2111 3.5312 10.4714C3.79155 10.7318 4.21366 10.7318 4.47401 10.4714L8.47401 6.47141C8.73436 6.21106 8.73436 5.78895 8.47401 5.52861L4.47401 1.52861C4.21366 1.26826 3.79155 1.26826 3.5312 1.52861Z"
                                                    fill="currentColor"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @else
            <!-- Filter Section -->
            <div class="mt-22 mt-sm-4">
                <form method="get" id="virtualcardForm">
                    <div class="filter-panel">
                        <div class="d-flex flex-wrap justify-content-between gap-2  align-items-centerpb-26">
                            <div class="d-flex flex-wrap align-items-center pb-xl-0">

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
                                        @foreach($virtualcardStatuses as $status)
                                            <option value="{{ $status->status }}" {{ $filter && $filter['status'] == $status->status ? 'selected' : '' }}>{{ $status->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="filter-btn f-14 gilroy-medium leading-17">{{ __('Filter') }}</button>
                            </div>
                        </div>
                        @if (count($cardholders))
                        <div class="d-flex justify-content-end align-items-center ">

                            <a href="{{ route('user.virtualcard.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn cursor-pointer">
                                <span class="f-14 leading-29 gilroy-medium">+ {{ __('New Card') }}</span>
                            </a>
                        </div>
                        @endif
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
                        @if (count($cardholders))
                        <div class="d-flex mt-14 r-mt-22 align-items-center">
                            <a class="bg-primary text-white Add-new-btn w-176 addnew text-center green-btn" href="{{ route('user.virtualcard.create') }}">
                                <span class="f-14 leading-29 gilroy-medium">+ {{ __('New Card') }}</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    <!-- Unfiltered Data -->
    @elseif (\Illuminate\Support\Facades\Request::is('virtualcard/cards') && ! \Illuminate\Support\Facades\Request::query())

        @if($virtualcards->whereNotIn('status', ['Pending'])->count() > 0)

            <div>
                <!-- Filter Section -->
                <div class="mt-22 mt-sm-4">
                    <form method="get" id="virtualcardForm">
                        <div class="filter-panel">
                            <div class="d-flex flex-wrap justify-content-between gap-2  align-items-centerpb-26">
                                <div class="d-flex flex-wrap align-items-center pb-xl-0">

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
                                            @foreach($virtualcardStatuses as $status)
                                                <option value="{{ $status->status }}" {{ $filter && $filter['status'] == $status->status ? 'selected' : '' }}>{{ $status->status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="filter-btn f-14 gilroy-medium leading-17">{{ __('Filter') }}</button>
                                </div>
                            </div>
                            @if (count($cardholders))
                            <div class="d-flex justify-content-end align-items-center ">

                                <a href="{{ route('user.virtualcard.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn cursor-pointer">
                                    <span class="f-14 leading-29 gilroy-medium">+ {{ __('New Card') }}</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
                <!-- Filter Section -->

                <div class="table-responsive table-scrolbar thin-scrollbar mt-4">
                    <table class="merchant-payments table-curved table recent_activity table-bordered mt-4">
                        <thead>
                            <tr class="payment-parent-section-title component-table-one">
                                <th width="30%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Number') }}</p>
                                </th>

                                <th width="15%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Expired') }}</p>
                                </th>

                                <th width="15%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Card Holder') }}</p>
                                </th>
                                <th width="15%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start whitespace-nowrap">{{ __('Currency') }}</p>
                                </th>

                                <th width="15%" class="p-0 pb-6">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-start">{{ __('Status') }}</p>
                                </th>
                                <th width="10%" class="p-0 pb-6 pe-2">
                                    <p class="mb-0 f-14 leading-17 gilroy-regular text-gray-100 text-end pe-2">{{ __('Action') }}</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($virtualcards as $virtualcard)
                                @if ($virtualcard->status != 'Pending' && $virtualcard->status != 'Declined')
                                    <tr class="bg-white">

                                        <td class="d-flex align-items-center">
                                            @if ($virtualcard->card_number && $virtualcard->card_brand == 'Visa Card')
                                                {!! virtualcardSvgIcons('visa_card_icon') !!}
                                            @elseif ($virtualcard->card_number && $virtualcard->card_brand == 'Master Card')
                                                {!! virtualcardSvgIcons('master_card_icon') !!}
                                            @endif
                                            <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcard->card_number ? maskCardNumberForLogo($virtualcard->card_number) : '-' }}</p>
                                        </td>

                                        <td>
                                            <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ formatCardExpiryDate($virtualcard->expiry_month, $virtualcard->expiry_year) }}</p>
                                        </td>

                                        <td class="w-270p">
                                            <p class="mb-0 f-18 gilroy-medium text-dark text-start w-270p whitespace-nowrap">{{ cardTitle($virtualcard->virtualcardHolder) }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 f-16 leading-17 gilroy-medium text-dark text-start">{{ $virtualcard->currency_code }}</p>
                                        </td>

                                        <td>
                                            <span class="f-12 gilroy-medium custom-badge d-flex justify-content-center align-items-center px-0 {{ getColor($virtualcard->status) }}  r-mt-open">{{ $virtualcard->status }}</span>
                                        </td>

                                        <td class="text-end">
                                            @if ($virtualcard->status != 'Pending')
                                                <a class="arrow-hovers cursor-pointer transaction-arrow text-right"
                                                    href="{{ route('user.virtualcard.show', $virtualcard) }}">
                                                    <svg class="nscaleX-1" width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M3.5312 1.52861C3.27085 1.78896 3.27085 2.21107 3.5312 2.47141L7.0598 6.00001L3.5312 9.52861C3.27085 9.78895 3.27085 10.2111 3.5312 10.4714C3.79155 10.7318 4.21366 10.7318 4.47401 10.4714L8.47401 6.47141C8.73436 6.21106 8.73436 5.78895 8.47401 5.52861L4.47401 1.52861C4.21366 1.26826 3.79155 1.26826 3.5312 1.52861Z"
                                                            fill="currentColor"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($virtualcards->count() > 0)
            <!-- Filter Section -->
            <div class="mt-22 mt-sm-4">
                <form method="get" id="virtualcardForm">
                    <div class="filter-panel">
                        <div class="d-flex flex-wrap justify-content-between gap-2  align-items-centerpb-26">
                            <div class="d-flex flex-wrap align-items-center pb-xl-0">

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
                                        @foreach($virtualcardStatuses as $status)
                                            <option value="{{ $status->status }}" {{ $filter && $filter['status'] == $status->status ? 'selected' : '' }}>{{ $status->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="filter-btn f-14 gilroy-medium leading-17">{{ __('Filter') }}</button>
                            </div>
                        </div>
                        @if (count($cardholders))
                        <div class="d-flex justify-content-end align-items-center ">

                            <a href="{{ route('user.virtualcard.create') }}" class="bg-primary text-white w-176 addnew text-center green-btn cursor-pointer">
                                <span class="f-14 leading-29 gilroy-medium">+ {{ __('New Card') }}</span>
                            </a>
                        </div>
                        @endif
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
                        @if (count($cardholders))
                        <div class="d-flex mt-14 r-mt-22 align-items-center">
                            <a class="bg-primary text-white Add-new-btn w-176 addnew text-center green-btn" href="{{ route('user.virtualcard.create') }}">
                                <span class="f-14 leading-29 gilroy-medium">+ {{ __('New Card') }}</span>
                            </a>
                        </div>
                        @endif
                    </div>
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
                        @if (count($cardholders))
                        <div class="d-flex mt-14 r-mt-22 align-items-center cursor-pointer">
                            <a class="bg-primary text-white Add-new-btn w-176 addnew text-center green-btn" href="{{ route('user.virtualcard.create') }}">
                                <span class="f-14 leading-29 gilroy-medium">+ {{ __('New Card') }}</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    @endif
    <div class="mt-4">
        {{ $virtualcards->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('public/dist/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
<script src="{{ asset('public/user/customs/js/daterange-select.min.js') }}"></script>
<script type="text/javascript">
    'use strict';
    var filterUrl = '{{ route("user.virtualcard.index") }}';
    var sessionDateFormateType = "{{ Session::get('date_format_type') }}";
    var startDate = "{!! isset($filter['from']) ? setDateForDb($filter['from']) : '' !!}";
    var endDate = "{!! isset($filter['to']) ? setDateForDb($filter['to']) : '' !!}";
    let dateRangePickerText = "{{ __('Pick a date range') }}";
    var submitBtnText = "{{ __('Processing...') }}";
</script>
<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/user/virtualcard.min.js') }}"></script>

@endpush
