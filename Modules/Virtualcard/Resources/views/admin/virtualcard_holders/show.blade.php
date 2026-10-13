@extends('admin.layouts.master')
@section('title', __('Card Holder Details'))

@section('page_content')
<div id="VirtualCardHolderShow">
    <div class="box box-default">
        <div class="box-body">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="top-bar-title padding-bottom pull-left">{{ __('Holder Details') }}</div>
                </div>
                <div>
                        <p class="text-left mb-0 f-18">{{ __('Status') }} :
                            {!! getStatusText($cardHolder->status) !!}
                        </p>
                </div>
            </div>
        </div>
    </div>

    <div class="my-30">
        <form action="javascript:void(0);" class="form-horizontal row" id="deposit_form" method="POST">
            <!-- Page title start -->
            <div class="col-md-7">
                <div class="box">
                    <div class="box-body">
                        <div class="panel">
                            <div>
                                <div class="p-4">

                                    <!-- User -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('User') }}</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static f-14">{{ getColumnValue($cardHolder->user) }}</p>
                                        </div>
                                    </div>

                                    <!-- Card Holder Type -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Card Holder Type') }}</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static f-14">{{ ucwords($cardHolder->card_holder_type) }}</p>
                                        </div>
                                    </div>

                                    <!-- country -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Country') }}</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static f-14">{{ $cardHolder->country }}</p>
                                        </div>
                                    </div>

                                    <!-- state -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('State') }}</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static f-14">{{ $cardHolder->state }}</p>
                                        </div>
                                    </div>

                                    <!-- postal code -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('postal code') }}</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static f-14">{{ $cardHolder->postal_code }}</p>
                                        </div>
                                    </div>

                                    <!-- city -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('City') }}</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static f-14">{{ $cardHolder->city }}</p>
                                        </div>
                                    </div>
                                    <!-- address -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Address') }}</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-static f-14">{{ $cardHolder->address }}</p>
                                        </div>
                                    </div>

                                    <!-- Provider -->
                                    @if ($cardHolder->status == 'Approved')
                                        <div class="form-group row">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-3">{{ __('Provider') }}</label>
                                            <div class="col-sm-6">
                                                <p class="form-control-static f-14">{{ $cardHolder->virtualcardProvider?->name }} <span class="badge bg-secondary">{{  $cardHolder->virtualcardProvider?->type }}</span></p>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="box">
                    <div class="box-body">
                        <div class="panel">
                            <div>
                                <div class="pt-4">
                                    <!-- Name -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-5">{{ __('Name') }}</label>
                                        <div class="col-sm-7">
                                            <p class="form-control-static f-14">{{ $cardHolder->title . ' '. getColumnValue($cardHolder) }}</p>
                                        </div>
                                    </div>

                                    @if ($cardHolder->card_holder_type == 'Individual')
                                        <!-- Gender -->
                                        <div class="form-group row">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-5">{{ __('Gender') }}</label>
                                            <div class="col-sm-7">
                                                <p class="form-control-static f-14">{{ $cardHolder->gender }}</p>
                                            </div>
                                        </div>

                                        <!-- Date of Birth -->
                                        <div class="form-group row">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-5">{{ __('Date of Birth') }}</label>
                                            <div class="col-sm-7">
                                                <p class="form-control-static f-14">{{ $cardHolder->date_of_birth }}</p>
                                            </div>
                                        </div>

                                        <!-- Verification Document Type -->
                                        <div class="form-group row">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-5">{{ __('Document Type') }}</label>
                                            <div class="col-sm-7">
                                                <p class="form-control-static f-14">{{ $cardHolder->verification_document_type }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($cardHolder->card_holder_type != 'Individual')
                                        <!-- Business -->
                                        <div class="form-group row">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-5">{{ __('Business Name') }}</label>
                                            <div class="col-sm-7">
                                                <p class="form-control-static f-14">{{ $cardHolder->business_name }}</p>
                                            </div>
                                        </div>
                                        <!-- Business Number -->
                                        <div class="form-group row">
                                            <label class="control-label fw-bold f-14 text-sm-end col-sm-5">{{ __('Business Number') }}</label>
                                            <div class="col-sm-7">
                                                <p class="form-control-static f-14">{{ $cardHolder->business_id_number }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($cardHolder->card_holder_type == 'Individual')
                                    <!-- image -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-5">{{ __('Document Front') }}</label>
                                        <div class="col-sm-7">
                                            <p class="form-control-static f-14">
                                                @if ($cardHolder->verification_document_image_front != null && file_exists('Modules/Virtualcard/Resources/assets/attachments/virtual_card_holders/' . $cardHolder->verification_document_image_front))
                                                    <a href="{{ asset('Modules/Virtualcard/Resources/assets/attachments/virtual_card_holders/' . $cardHolder->verification_document_image_front) }}" download={{ $cardHolder->verification_document_image_front }}><i class="fa fa-fw fa-download"></i>
                                                        {{ $cardHolder->verification_document_image_front }}
                                                    </a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-5">{{ __('Document Back') }}</label>
                                        <div class="col-sm-7">
                                            <p class="form-control-static f-14">
                                                @if ($cardHolder->verification_document_image_back != null && file_exists('Modules/Virtualcard/Resources/assets/attachments/virtual_card_holders/' . $cardHolder->verification_document_image_back))
                                                    <a href="{{ asset('Modules/Virtualcard/Resources/assets/attachments/virtual_card_holders/' . $cardHolder->verification_document_image_back) }}" download={{ $cardHolder->verification_document_image_back }}><i class="fa fa-fw fa-download"></i>
                                                        {{ $cardHolder->verification_document_image_back }}
                                                    </a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Created At -->
                                    <div class="form-group row">
                                        <label class="control-label fw-bold f-14 text-sm-end col-sm-5" for="created_at">{{ __('Date') }}</label>
                                        <div class="col-sm-7">
                                            <p class="form-control-static f-14">{{ dateFormat($cardHolder->created_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    @if ('Pending' == $cardHolder->status && Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_virtual_card'))
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row gap-2 gap-sm-0">
                                    <div class="col-sm-6">
                                        <a class="btn btn-theme-danger f-14 me-1 w-100 reject-warning" href="{{ route('admin.virtualcard_holder.reject', $cardHolder) }}" id="users_cancel">{{ __('Reject') }}</a>
                                    </div>
                                    <div class="col-sm-6">

                                        <button type="button" class="btn btn-theme f-14 w-100 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ __('Approve') }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach ($providers as $provider)
                                                <li><a href="{{ route('admin.virtualcard_holder.approve', [$cardHolder, $provider]) }}" class="dropdown-item reject-warning">{{ $provider->name . '(' .  $provider->type .')' }}</a></li>
                                            @endforeach
                                        </ul>

                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Approve/Reject Modal for href-->
    <div class="modal fade del-modal" id="reject-warning-modal" role="dialog">
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
                    <a class="btn btn-danger f-14" id="reject-modal-yes" href="javascript:void(0)">{{ __('Yes') }}</a>
                    <button type="button" class="btn btn-default f-14" data-bs-dismiss="modal">{{ __('No') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('extra_body_scripts')

    <script>
        'use strict';
        let rejectTitle = '{{ __("Confirm Reject") }}';
        let rejectText = '{{ __("Are you sure you want to reject?") }}';
        let approveTitle = '{{ __("Confirm Approve") }}';
        let approveText = '{{ __("Are you sure you want to approve?") }}';
    </script>

    <script src="{{ asset('Modules/Virtualcard/Resources/assets/js/admin/cardholder.min.js') }}"></script>
@endpush



