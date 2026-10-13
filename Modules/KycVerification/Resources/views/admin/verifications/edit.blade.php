@extends('admin.layouts.master')
@section('title', __('Edit Verification'))

@section('page_content')
	<div class="box box-default" id="verification-update">
		<div class="box-body">
			<div class="d-flex justify-content-between">
				<div>
					<div class="top-bar-title padding-bottom pull-left">{{ __('Verification Detail') }}</div>
				</div>

				<div>
					@if ($verification->status)
                        @php
                            $statusClass = match($verification->status) {
                                'approved' => 'text-green',
                                'pending' => 'text-blue',
                                default => 'text-red',
                            };
                        @endphp

						<p class="text-left mb-0 f-18">{{ __('Status') }} :
                            <span class="{{ $statusClass }}">{{ ucwords($verification->status) }}</span>
                        </p>
					@endif
				</div>
			</div>
		</div>
	</div>

    <section class="min-vh-100">
        <div class="my-30">
            <div class="row">
                <form action="{{ route('admin.kyc.verifications.update', $verification->id) }}" class="row form-horizontal" id="verify-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="col-md-8">
                        <div class="box">
                            <div class="box-body">
                                <div class="panel">
                                    <div>
                                        <div class="p-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    @if ($verification->created_at)
                                                        <div class="form-group row">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-3">{{ __('Date') }}</label>
                                                            <div class="col-sm-9">
                                                                <p class="form-control-static f-14">{{ dateFormat($verification->created_at) }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($verification->user_id)
                                                        <div class="form-group row">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-3">{{ __('User') }}</label>
                                                            <div class="col-sm-9">
                                                                <p class="form-control-static f-14">{{ getColumnValue($verification->user) }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($verification->provider_id)
                                                        <div class="form-group row">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-3">{{ __('Provider') }}</label>
                                                            <div class="col-sm-9">
                                                                <p class="form-control-static f-14">{{ getColumnValue($verification->provider, 'name', '') }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($verification->verification_type)
                                                        <div class="form-group row">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-3">{{ __('Type') }}</label>
                                                            <div class="col-sm-9">
                                                                <p class="form-control-static f-14">{{ ucfirst($verification->verification_type) }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (strtolower($verification->verification_type) == 'identity')
                                                        <div class="form-group row">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-3">{{ __('Identity Type') }}</label>
                                                            <div class="col-sm-9">
                                                                <p class="form-control-static f-14">{{ ucwords(str_replace('_', ' ', $verification->identity_type)) }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-3">{{ __('Identity Number') }}</label>
                                                            <div class="col-sm-9">
                                                                <p class="form-control-static f-14">{{ $verification->identity_number }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($verification->status)
                                                        <div class="form-group row align-items-center">
                                                            <label class="control-label f-14 fw-bold text-sm-end col-sm-3 require" for="status">{{ __('Change Status') }}</label>
                                                            <div class="col-sm-6">
                                                                <select class="form-control select2 w-60" name="status" id="status">
                                                                    <option value="approved" {{ $verification->status ==  'approved'? 'selected' : "" }}>{{ __('Approved') }}</option>
                                                                    <option value="pending"  {{ $verification->status == 'pending' ? 'selected' : "" }}>{{ __('Pending') }}</option>
                                                                    <option value="rejected"  {{ $verification->status == 'rejected' ? 'selected' : "" }}>{{ __('Rejected') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row">
                                                        <div class="col-md-6 offset-md-3">
                                                            <a id="cancel_anchor" class="btn btn-theme-danger f-14 me-1" href="{{ route('admin.kyc.verifications.index') }}">{{ __('Cancel') }}</a>
                                                            <button type="submit" class="btn btn-theme f-14" id="verify-btn">
                                                                <i class="fa fa-spinner fa-spin d-none"></i> <span id="verify-btn-txt">{{ __('Update') }}</span>
                                                            </button>
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
                    @if ($verification->file)
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-body">
                                    <div class="panel">
                                        <div>
                                            <div class="mt-4 p-4">
                                                <div class="row">
                                                    <ul class="list-unstyled">
                                                        <p class="mb-0 f-18 text-decoration-underline">{{ __('Verification Proof') }}</p>
                                                        <li> {{ $verification->file?->filename }}
                                                            @php
                                                                $filePath = asset('public/'. getKycDocumentPath(strtolower($verification->verification_type)) . $verification->file?->filename);
                                                            @endphp
                                                            <a class="text-info pull-right" href="{{ url($filePath) }}">
                                                                <i class="fa fa-download text-black"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection

@push('extra_body_scripts')
    <script type="text/javascript">
        'use strict';
        let updateBtnTxt = "{{ __('Updating...') }}";
    </script>
    <script src="{{ asset('Modules/KycVerification/Resources/assets/js/admin/verification.min.js') }}" type="text/javascript"></script>

@endpush
