@extends('admin.layouts.master')
@section('title', __('Card Details'))
@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/libraries/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Virtualcard/Resources/assets/css/virtualcard_show.min.css') }}">
@endsection
@section('page_content')
<div id="show-section">
    <!-- Header Section -->
    <div class="box box-default">
        <div class="box-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="top-bar-title padding-bottom pull-left">{{ __('Virtual Card Details') }}</div>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <!-- Status -->
                    <div >
                        @if ($virtualcard->status)
                        <p class="text-left mb-0 f-18">{{ __('Status') }} : {!! getStatusText($virtualcard->status == 'Canceled' ? 'cancelled' : $virtualcard->status) !!}</p>
                        @endif
                    </div>
                    <!-- Status -->

                    <!-- Action Button -->
                    @if ($virtualcard->status != 'Pending' && $virtualcard->status != 'Declined')
                        <div>
                            <button type="button" class="btn btn-theme f-14 w-100 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('Action') }}
                            </button>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript: void(0)" class="dropdown-item card-action" data-action="active" data-cardid="{{ $virtualcard->id }}">{{ __('Activate') }}</a>
                                </li>
                                <li>
                                    <a href="javascript: void(0)" class="dropdown-item card-action" data-action="inactive" data-cardid="{{ $virtualcard->id }}">{{ __('Inactivate') }}</a>
                                </li>
                                <li>
                                    <a href="javascript: void(0)" class="dropdown-item card-action" data-action="canceled" data-cardid="{{ $virtualcard->id }}">{{ __('Cancel') }}</a>
                                </li>
                                <li>
                                    <a href="javascript: void(0)" class="dropdown-item card-action" data-action="expired" data-cardid="{{ $virtualcard->id }}">{{ __('Expire') }}</a>
                                </li>
                            </ul>
                        </div>
                    @endif
                    <!-- Action Button -->
                </div>
            </div>
        </div>
    </div>
    <!-- Header Section -->

    <!-- Details Section -->
    <section class="min-vh-100">
        <div class="my-30">
            <div class="row">
                <!-- Left Side: Card Details -->
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-body">
                            <div class="panel">
                                <div>
                                    <div class="p-4">

                                        <div class="panel panel-default">
                                            <div class="panel-body">

                                                <!-- Card owner -->
                                                <x-virtualcard::card-info lable="{{ __('Owner') }}" value="{{ getColumnValue($virtualcard->virtualcardHolder?->user) }}" />

                                                <!-- Card Holder -->
                                                <div class="form-group row">
                                                    <label class="control-label f-14 fw-bold text-sm-end col-sm-4" for="user">{{ __('Card Holder') }}</label>
                                                    <div class="col-sm-8">
                                                        <p class="form-control-static f-14 mb-0">
                                                            <a href="{{ route("admin.virtualcard_holder.show", $virtualcard->virtualcardHolder) }}">{{ cardTitle($virtualcard->virtualcardHolder) }}</a>
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Provider -->
                                                @if ($virtualcard->virtualcardProvider)
                                                    <div class="form-group row">
                                                        <label class="control-label f-14 fw-bold text-sm-end col-sm-4" for="user">{{ __('Provider') }}</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-static f-14 mb-0">
                                                                {{ $virtualcard->virtualcardProvider?->name ? $virtualcard->virtualcardProvider?->name : '-' }}
                                                            </p>
                                                            <p class="f-14">
                                                                <span class="badge bg-secondary">
                                                                    {{ $virtualcard->virtualcardProvider?->type }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Card Type -->
                                                <x-virtualcard::card-info lable="{{ __('Type') }}" value="{{ $virtualcard->card_type }}" />

                                                <!-- Card Brand -->
                                                @if ($virtualcard->status == 'Active')
                                                    <x-virtualcard::card-info lable="{{ __('Brand') }}" value="{{ $virtualcard->card_brand }}" />
                                                @else
                                                    <x-virtualcard::card-info lable="{{ __('Preferred Brand') }}" value="{{ $virtualcard->card_brand }}" />
                                                @endif

                                                <!-- Card Category -->
                                                <x-virtualcard::card-info lable="{{ __('Category') }}" value="{{ $virtualcard->virtualcardCategory?->name }}" />

                                                <!-- Currency Code -->
                                                @if ($virtualcard->status == 'Active')
                                                    <x-virtualcard::card-info lable="{{ __('Currency') }}" value="{{ $virtualcard->currency_code }}" />
                                                @else
                                                    <x-virtualcard::card-info lable="{{ __('Preferred Currency') }}" value="{{ $virtualcard->currency_code }}" />
                                                @endif

                                                <!-- Amount -->
                                                <x-virtualcard::card-info lable="{{ __('Amount') }}" value="{{ formatNumber($virtualcard->amount, $virtualcard->currency()?->id) }}" />

                                                <!-- Button -->

                                                <div class="row">
                                                    <div class="col-md-6 offset-md-3">
                                                        <a id="cancel_anchor" class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.virtualcard.index') }}">{{ __('Back') }}</a>
                                                        @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_virtual_card') && $virtualcard->virtualcardProvider?->type == 'Manual')
                                                        <a href="{{ route('admin.virtualcard.edit', $virtualcard) }}" class="btn btn-theme f-14">
                                                            <span id="deposits_edit_text">{{ __('Edit') }}</span>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Left Side: Card Details -->

                <!-- Right Side: Card Info -->
                <div class="col-md-6">
                    <!-- Card Info -->
                    <div>
                        <div class="box">
                            <div class="box-body">
                                <div class="panel">
                                    <div>
                                        <div class=" p-4">

                                            <p class="mb-0 f-18 fw-bold spend-table p-3">{{ __('Card Credentials') }}</p>

                                            <div class="panel panel-default">
                                                <div class="panel-body">

                                                    <!-- Title -->
                                                    <x-virtualcard::card-info lable="{{ __('Title') }}" value='<a href="{{ route("admin.virtualcard_holder.show", $virtualcard->virtualcardHolder) }}">{{ cardTitle($virtualcard->virtualcardHolder) }}</a>' />

                                                    <!-- Card Number -->
                                                    <div class="form-group row d-flex align-items-center">
                                                        <label class="control-label f-14 fw-bold text-sm-end col-sm-4 mb-0 mt-0">{{ __('Number') }}</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-static f-14 mb-0 mt-0">

                                                                {!! $virtualcard->card_number ? virtualcardSvgIcons(strtolower(str_replace(' ', '_', $virtualcard->card_brand)) . '_icon') .  maskCardNumberForLogo($virtualcard->card_number) : '-' !!}

                                                            </p>
                                                        </div>
                                                    </div>

                                                    <!-- Card Cvc -->
                                                    <x-virtualcard::card-info 
                                                        lable="{{ __('CVC') }}" 
                                                        value="{{ 
                                                            $virtualcard->card_cvc 
                                                                ? str_repeat('*', strlen($virtualcard->card_cvc)) 
                                                                : '-' 
                                                        }}" 
                                                    />

                                                    <!-- Expiry Date -->
                                                    <x-virtualcard::card-info lable="{{ __('Expiry Date') }}" value="{{ formatCardExpiryDate($virtualcard->expiry_month, $virtualcard->expiry_year) }}" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            @if ($virtualcard->status == 'Pending')
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="row gap-2 gap-sm-0">
                                            <div class="col-sm-6">
                                                <a class="btn btn-theme-danger f-14 me-1 w-100 card-warning" href="{{ route('admin.virtualcard_issue.decline', $virtualcard) }}" id="users_cancel">{{ __('Decline') }}</a>
                                            </div>
                                            <div class="col-sm-6">

                                                <button type="button" class="btn btn-theme f-14 w-100 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ __('Approve') }}
                                                </button>

                                                <ul class="dropdown-menu">
                                                    @foreach ($providers as $provider)
                                                        <li><a href="{{ route('admin.virtualcard_issue.approve', [$cardHolder, $provider, $virtualcard]) }}" class="dropdown-item card-warning">{{ $provider->name . '(' .  $provider->type .')' }}</a></li>
                                                    @endforeach
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Card Info -->

                    <!-- Spending Control -->
                    @if ($virtualcard->status != 'Pending' && $virtualcard->status != 'Declined')
                        <div class="mt-4">
                            <div class="box">
                                <div class="box-body">
                                    <div class="panel">

                                        <div class="spend-table p-3">
                                            <h5 class="f-18">{{ __('Spend controls') }} <i class="fa fa-pencil cursor-pointer" data-bs-toggle="modal" data-bs-target="#spendControlModal"></i></h5>

                                            <!-- Spending Control list -->
                                            @forelse ($spendingControls as $spendingControl)
                                                <div class="spend-row">
                                                    <div class="spend-circle"></div>
                                                    <div class="spend-info">
                                                        <p class="fw-bold f-14">{{ \Illuminate\Support\Str::ucfirst(str_replace('_', ' ', $spendingControl->interval)) }}</p>
                                                        <p class="f-14">
                                                            @php
                                                                $amount = MoneyFormat($virtualcard->currency()?->symbol, formatNumber($spendingControl->amount, $virtualcard->currency_id));
                                                            @endphp

                                                            @if ($spendingControl->interval == 'all_time')
                                                                {{ __(':x allowed in total', ['x' => $amount]) }}
                                                            @elseif ($spendingControl->interval == 'per_authorization')
                                                                {{ __(':x allowed per transaction', ['x' => $amount]) }}
                                                            @elseif ($spendingControl->interval == 'yearly')
                                                                {{ __(':x allowed per year', ['x' => $amount]) }}
                                                            @elseif ($spendingControl->interval == 'monthly')
                                                                {{ __(':x allowed per month', ['x' => $amount]) }}
                                                            @elseif ($spendingControl->interval == 'weekly')
                                                                {{ __(':x allowed per week', ['x' => $amount]) }}
                                                            @else
                                                                {{ __(':x allowed per day', ['x' => $amount]) }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="card-custom">
                                                    <h5 class="f-14 fw-bold">{{ __('No spend controls available') }}</h5>
                                                    <p class="f-14">{{ __('You haven\'t set up any spend controls on this card.') }}</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Spending Control -->
                </div>
            </div>
        </div>
    </section>
    <!-- Details Section -->

    <!-- Card Approve/Decline Modal -->
    <div class="modal fade del-modal" id="card-warning-modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content w-100 h-100 aliceblue">
                <div class="modal-header">
                    <h4 class="modal-title f-18"></h4>
                    <a type="button" class="close f-18" data-bs-dismiss="modal">&times;</a>
                </div>
                <div class="px-3 f-14">
                    <p><strong class="modal-text"></strong></p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger f-14" id="card-modal-yes" href="javascript:void(0)">{{ __('Yes') }}</a>
                    <button type="button" class="btn btn-default f-14" data-bs-dismiss="modal">{{ __('No') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Spending controls Modal -->
    <div class="modal fade" id="spendControlModal" tabindex="-1" aria-labelledby="spendControlModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title f-18 fw-bold" id="spendControlModalLabel">{{ __('Set up spend controls') }}</h5>
                    <button type="button" class="btn-close f-14" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold f-14">{{ __('Basic spend limits') }}</h6>
                    <p class="f-14">{{ __('Select the spend limits and its occurrence. You can set up multiple spend limits.') }}</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text f-14">{{ $virtualcard->currency()?->symbol }}</span>
                        <!-- Amount -->
                        <input
                            type="text"
                            class="form-control f-14 rounded-end"
                            placeholder="0"
                            id="amount"
                            name="amount"
                            aria-required="true"
                            aria-invalid="false"
                            onkeypress="return isNumberOrDecimalPointKey(this, event);"
                            oninput="restrictNumberToPrefdecimalOnInput(this);"
                            required data-value-missing="{{ __('This field is required') }}"
                        >

                        <select class="form-select f-14 rounded spend-control-ml" id="interval" name="interval" aria-required="true" aria-invalid="false">
                            @foreach (\Modules\Virtualcard\Enums\SpendingIntervals::cases() as $interval)
                                <option value="{{ $interval->value }}" {{ $interval->value == 'all_time' ? 'selected' : '' }}>{{ ucfirst(strtolower(str_replace('_', ' ', $interval->name))) }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-secondary f-14 rounded bg-white bordered text-black spend-control-ml" >{{ __('Add control') }}</button>
                    </div>
                    <p class="f-14 error spendingControlError d-none"></p>

                    <ul class="list-group spendingLimits">
                    </ul>

                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-theme f-14 spendingControlBtn">
                        <i class="fa fa-spinner fa-spin d-none"></i>
                        <span class="spendingControlBtnText">{{ __('Save changes') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('extra_body_scripts')

    @include('common.restrict_number_to_pref_decimal')
    @include('common.restrict_character_decimal_point')

    <script src="{{ asset('public/dist/libraries/sweetalert2/sweetalert2.min.js') }}" type="text/javascript"></script>
        <script>

            'use strict';
            var decimalFormate = "{{ preference('decimal_format_amount', 2) }}";
            var caryptoFormate = "{{ preference('decimal_format_amount_crypto', 8) }}";
            let _token = '{{ csrf_token() }}';
            let declineTitle = '{{ __("Confirm Decline") }}';
            let declineText = '{{ __("Are you sure you want to decline?") }}';
            let approveTitle = '{{ __("Confirm Approve") }}';
            let approveText = '{{ __("Are you sure you want to approve?") }}';
            let cardActionUrl = '{{ route("admin.virtualcard.action") }}';
            let somethingWentWrongText = '{{ __("Error! Something went wrong.") }}';
            let errorText = '{{ __("Error!") }}';
            let actionTitle = '{{ __("Are you sure?") }}';
            let actionActiveText = '{{ __("You are about to activate this card!") }}';
            let actionDeactiveText = '{{ __("You are about to inactive this card!") }}';
            let actionActiveButtonText = '{{ __("Yes, activate it!") }}';
            let actionDeactiveButtonText = '{{ __("Yes, inactive it!") }}';
            let pleaseWaitText = '{{ __("Please wait...") }}';
            let loadingText = '{{ __("Loading...") }}';
            let failedTitle = '{{ __("Failed") }}';
            let limitText = '{{ __("Limit") }}';
            let limitInText = '{{ __("limit in") }}';
            let limitPerText = '{{ __("limit per") }}';
            let dayText = '{{ __("day") }}';
            let weekText = '{{ __("week") }}';
            let monthText = '{{ __("month") }}';
            let yearText = '{{ __("year") }}';
            let totalText = '{{ __("total") }}';
            let authorizationText = '{{ __("authorization") }}';
            let spendingControlLimitUrl = '{{ route("admin.virtualcard_spendingcontrol.limit") }}';
            let virtualcardId = '{{ $virtualcard->id }}';
            let currency = {!! $currency !!};
            let spendingControlExistCheckUrl = '{{ route("admin.virtualcard_spendingcontrol.limit_exist") }}';
            let updatingText = '{{ __("Updating...") }}';
            let saveChangeText = '{{ __("Save changes") }}';
            let unexpectedErrorText = '{{ __("An unexpected error occured.") }}';
            let validAmountText = '{{ __("Please enter a valid amount.") }}';

        </script>
    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/admin/virtualcard.min.js') }}" type="text/javascript"></script>
@endpush


