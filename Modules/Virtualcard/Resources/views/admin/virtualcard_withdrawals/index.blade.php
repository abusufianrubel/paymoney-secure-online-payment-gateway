@extends('admin.layouts.master')

@section('title', __('Card Withdrawals'))

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/DataTables/DataTables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/DataTables/Responsive/css/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/libraries/jquery-ui/jquery-ui.min.css')}}">
@endsection

@section('page_content')
    <div class="box" id="withdrawalIndexBox">
        <div class="box-body pb-20">

        <form class="form-horizontal" action="{{ route('admin.virtualcard_withdrawal.index') }}" method="GET">

            <input id="startfrom" type="hidden" name="filter[from]" value="{{ isset($filter) ? $filter['from'] : '' }}">
            <input id="endto" type="hidden" name="filter[to]" value="{{ isset($filter) ? $filter['to'] : '' }}">

            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex flex-wrap">

                            <!-- Date and time range -->
                            <div class="pr-25">
                                <label class="f-14 fw-bold mb-1" for="daterange-btn">{{ __('Date Range') }}</label><br>
                                <button type="button" class="btn btn-default f-14" id="daterange-btn">
                                    <span id="drp"><i class="fa fa-calendar"></i></span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                            </div>

                            <!-- Currency -->
                            <div class="pr-25">
                                <label class="f-14 fw-bold mb-1" for="currency">{{ __('Currency') }}</label><br>
                                <select class="form-control select2" name="filter[currency]" id="currency">
                                    <option value="" >{{ __('All') }}</option>
                                    @foreach($virtualcardWithdrawalCurrencies as $currency)
                                        <option value="{{ $currency }}" {{ $filter && $filter['currency'] ==  $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="pr-25">
                                <label class="f-14 fw-bold mb-1" for="status">{{ __('Status') }}</label><br>
                                <select class="form-control select2" name="filter[status]" id="status">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach($virtualcardWithdrawalStatuses as $status)
                                        <option value="{{ $status->fund_approval_status }}" {{ $filter && $filter['status'] == $status->fund_approval_status ? 'selected' : '' }}>{{ $status->fund_approval_status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- User -->
                            <div class="pr-25">
                                <label class="f-14 fw-bold mb-1" for="user">{{ __('User') }}</label><br>
                                <select class="form-control select2" name="filter[user]" id="user">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach($virtualcardWithdrawalUsers as $user)
                                        <option value="{{ getColumnValue($user) }}" {{ $filter && $filter['user'] == getColumnValue($user) ? 'selected' : '' }}>{{ getColumnValue($user) }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div>
                            <div class="input-group mt-3">
                                <button type="submit" name="btn" class="btn btn-theme f-14" id="btn">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>

        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <p class="panel-title f-14 fw-bold ml-5 f-14">{{ __('All Withdrawals') }}</p>
        </div>
        <div class="col-md-4">
            <div class="btn-group pull-right">
                <a href="javascript: void(0)" class="btn btn-sm btn-default btn-flat f-14" id="csv">{{ __('CSV') }}</a>
                <a href="javascript: void(0)" class="btn btn-sm btn-default btn-flat f-14" id="pdf">{{ __('PDF') }}</a>
            </div>
        </div>
    </div>

    <div class="box mt-20">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 f-14">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="table-responsive">
                                {!! $dataTable->table(['class' => 'table table-striped table-hover dt-responsive', 'width' => '100%', 'cellspacing' => '0']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
    <script src="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/plugins/DataTables/DataTables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/plugins/DataTables/Responsive/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/libraries/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>

    {!! $dataTable->scripts() !!}

    <script type="text/javascript">

        'use strict';
        var sessionDateFormateType = '{{ Session::get("date_format_type") }}';
        let dateRangePickerText = '{{ __("Pick a date range") }}';
        var startDate = "{!! isset($filter['from']) ? setDateForDb($filter['from']) : '' !!}";
        var endDate = "{!! isset($filter['to']) ? setDateForDb($filter['to']) : '' !!}";

    </script>

    <script src="{{ asset('public/admin/customs/js/daterange-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/admin/customs/js/csv-pdf.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/admin/virtualcard_withdrawal.min.js') }}"></script>

@endpush
