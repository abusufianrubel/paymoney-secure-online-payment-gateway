@extends('admin.layouts.master')

@section('title', __('Fees Limits'))

@section('head_style')
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/DataTables/DataTables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/DataTables/Responsive/css/responsive.dataTables.min.css') }}">
@endsection

@section('page_content')
    <div class="box box-default">
        <div class="box-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="top-bar-title padding-bottom pull-left">{{ __('Fees Limits') }}</div>
                </div>
                <div>
                    @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'add_card_fees_limit'))
                    <a href="{{ route('admin.card_fees.create') }}" class="btn btn-theme f-14"><span class="fa fa-plus"> &nbsp;</span>{{ __('Add Fees Limit') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="table-responsive">
                                {!! $dataTable->table(['class' => 'table table-striped table-hover f-14 dt-responsive', 'width' => '100%', 'cellspacing' => '0']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
    <script type="text/javascript" src="{{ asset('public/dist/plugins/DataTables/DataTables/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/dist/plugins/DataTables/Responsive/js/dataTables.responsive.min.js') }}"></script>
    {!! $dataTable->scripts() !!}
@endpush
