@extends('admin.layouts.master')

@section('title', __('Kyc Verification'))

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/DataTables/DataTables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/plugins/DataTables/Responsive/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
    <div class="box">
        <div class="box-body pb-20">
            <form class="form-horizontal" action="" method="GET">

                <input id="startfrom" type="hidden" name="from" value="{{ isset($from) ? $from : '' }}">
                <input id="endto" type="hidden" name="to" value="{{ isset($to) ? $to : '' }}">

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

                                <!-- Provider -->
                                <div class="pr-25">
                                    <label class="f-14 fw-bold mb-1" for="provider">{{ __('Provider') }}</label><br>
                                    <select class="form-control select2" name="provider" id="provider">
                                        <option value="all" {{ ($provider =='all') ? 'selected' : '' }} >{{ __('All') }}</option>
                                        @foreach($providers as $verification)
                                            <option value="{{ $verification->provider_id }}" {{ ($verification->provider_id == $provider) ? 'selected' : '' }}>
                                                {{ $verification->provider?->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Type -->
                                <div class="pr-25">
                                    <label class="f-14 fw-bold mb-1" for="type">{{ __('Type') }}</label><br>
                                    <select class="form-control select2" name="type" id="type">
                                        <option value="all" {{ ($type =='all') ? 'selected' : '' }} >{{ __('All') }}</option>
                                        @foreach($types as $verification)
                                            <option value="{{ $verification->verification_type }}" {{ ($verification->verification_type == $type) ? 'selected' : '' }}>
                                                {{ ucfirst($verification->verification_type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Status -->
                                <div class="pr-25">
                                    <label class="f-14 fw-bold mb-1" for="status">{{ __('Status') }}</label><br>
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="all" {{ ($status =='all') ? 'selected' : '' }} >{{ __('All') }}</option>
                                        @foreach($statuses as $verification)
                                            <option value="{{ $verification->status }}" {{ ($verification->status == $status) ? 'selected' : '' }}>
                                                {{ ucfirst($verification->status) }}
                                            </option>
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
            <p class="panel-title text-bold ml-5 mb-0 f-14">{{ __('All Verifications') }}</p>
        </div>
        <div class="col-md-4">
            <div class="btn-group pull-right">
                <a href="" class="btn btn-sm btn-default btn-flat f-14" id="csv">{{ __('CSV') }}</a>
                <a href="" class="btn btn-sm btn-default btn-flat f-14" id="pdf">{{ __('PDF') }}</a>
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

    <script type="text/javascript">
        'use strict';
        let sessionDateFormateType = "{{ Session::get('date_format_type') }}";
        let dateRangePickerText = '{{ __("Pick a date range") }}';
        let startDate = "{!! $from !!}";
        let endDate = "{!! $to !!}";
        let csvUrl = "{{ route('admin.kyc.verifications.csv') }}";
        let pdfUrl = "{{ route('admin.kyc.verifications.pdf') }}";
    </script>

    <script src="{{ asset('public/admin/customs/js/daterange-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/admin/customs/js/csv-pdf.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('Modules/KycVerification/Resources/assets/js/admin/verification.min.js') }}" type="text/javascript"></script>

    {!! $dataTable->scripts() !!}
@endpush
