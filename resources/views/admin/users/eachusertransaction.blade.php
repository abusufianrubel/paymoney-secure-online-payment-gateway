@extends('admin.layouts.master')

@section('title', __('Transactions'))

@section('head_style')
<!-- Bootstrap daterangepicker -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css')}}">

<!-- dataTables -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/DataTables/DataTables/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/DataTables/Responsive/css/responsive.dataTables.min.css') }}">

@endsection

@section('page_content')
    <div class="box">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs f-14 cus" role="tablist">
                @include('admin.users.user_tabs')
           </ul>
          <div class="clearfix"></div>
       </div>
    </div>

    <div class="box">
        <div class="box-body pb-20">
            <form class="form-horizontal" action="{{  url(config('adminPrefix')."/users/transactions", $users->id) }}" method="GET">

                <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">

                <input id="user_id" type="hidden" name="user_id" value="{{ $users->id }}">

                <div class="row">
                    <div class="col-md-12 f-14">
                        <div class="d-flex flex-wrap justify-content-between">
                            <div class="d-flex flex-wrap">
                                <!-- Date and time range -->
                                <div class="pr-25">
                                    <label class="fw-bold" for="daterange-btn">{{ __('Date Range') }}</label><br>
                                    <button type="button" class="btn btn-default f-14 mt-1" id="daterange-btn" >
                                        <span id="drp">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>

                                <!-- Currency -->
                                <div class="pr-25">
                                    <label class="fw-bold mb-1" for="currency">{{ __('Currency') }}</label><br>
                                    <select class="form-control select2" name="currency" id="currency">
                                        <option value="all" {{ ($currency =='all') ? 'selected' : '' }}>{{ __('All') }}</option>
                                        @foreach($transactionCurrency as $transactionCurrency)
                                            <option value="{{ $transactionCurrency->currency_id }}" {{ ($transactionCurrency->currency_id == $currency) ? 'selected' : '' }}>{{ $transactionCurrency->code }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="pr-25">
                                    <label for="status" class="fw-bold mb-1">{{ __('Status') }}</label><br>
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="all" {{ ($status =='all') ? 'selected' : '' }} >{{ __('All') }}</option>
                                        @foreach($transactionStatus as $transactionStatus)
                                            <option value="{{ $transactionStatus->status }}" {{ ($transactionStatus->status == $status) ? 'selected' : '' }}>
                                                {{
                                                    (($transactionStatus->status == 'Blocked') ? "Cancelled" : (($transactionStatus->status == 'Refund') ? "Refunded" : $transactionStatus->status))
                                                }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="pr-25">
                                    <label class="fw-bold mb-1" for="type">{{ __('Type') }}</label><br>
                                    <select class="form-control select2" name="type" id="type">
                                        <option value="all" {{ ($type =='all') ? 'selected' : '' }} >{{ __('All') }}</option>
                                        @foreach($transactionType as $transactionType)
                                            <option value="{{ $transactionType->transaction_type_id }}" {{ ($transactionType->transaction_type_id == $type) ? 'selected' : '' }}>{{ replaceUnderscoresWithSpaces($transactionType->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <div class="input-group mt-25">
                                    <button type="submit" name="btn" class="btn btn-theme f-14" id="btn">{{ __('Filter') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table table-striped table-hover f-14 dt-responsive transactions', 'width' => '100%', 'cellspacing' => '0']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')

<!-- Bootstrap daterangepicker -->
<script src="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.js') }}" type="text/javascript"></script>

<!-- jquery.dataTables js -->
<script src="{{ asset('public/dist/plugins/DataTables/DataTables/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/DataTables/Responsive/js/dataTables.responsive.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    'use strict';
    var sessionDateFormateType = "{{Session::get('date_format_type')}}";
    let dateRangePickerText = '{{ __("Pick a date range") }}';
    var startDate = "{!! $from !!}";
    var endDate   = "{!! $to !!}";
</script>

<script src="{{ asset('public/admin/customs/js/daterange-select.min.js') }}" type="text/javascript"></script>


{!! $dataTable->scripts() !!}

<script type="text/javascript">
    $(".select2").select2({});
</script>

@endpush
