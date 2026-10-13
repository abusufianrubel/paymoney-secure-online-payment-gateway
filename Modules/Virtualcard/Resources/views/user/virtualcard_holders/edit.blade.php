@extends('user.layouts.app')

@section('content')
<div class="bg-white pxy-62 shadow" id="ticketCreate">
    <p class="mb-0 f-26 gilroy-Semibold text-uppercase text-center text-dark">{{ __('NEW TICKET') }}</p>
    <p class="mb-0 text-center f-14 gilroy-medium text-gray-100 dark-p mt-20"> {{ __('Write down to us the problem you are facing and set the level of priority. Our team will get back to you soon.') }} </p>

    @include('user.common.alert')

    <form method="post" action="{{ route('user.virtualcard_holder.update', 1) }}" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="userId" value="{{ auth()->id() }}">

        <!-- Card Holder Type -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0">{{ __('Card Holder Type') }}</label>
                    <select class="select2" data-minimum-results-for-search="Infinity" name="cardHolderType" id="priority">
                        <option value="Business">{{ __('Business') }}</option>
                        <option value="Individual">{{ __('Individual') }}</option>
                    </select>
                    @error('priority')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount">{{ __('Company Name') }}</label>
                    <input type="text" class="form-control input-form-control apply-bg" name="companyName" id="subject" value="{{ old('companyName') }}" placeholder="{{ __('Enter company name') }}" required data-value-missing="{{ __('This field is required.') }}">
                    @error('companyName')
                        <div class="error">{{ $companyName }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount">{{ __('Business Id Number') }}</label>
                    <input type="text" class="form-control input-form-control apply-bg" name="businessIdNumber" id="subject" value="{{ old('businessIdNumber') }}" placeholder="{{ __('Enter business id number') }}" required data-value-missing="{{ __('This field is required.') }}">
                    @error('businessIdNumber')
                        <div class="error">{{ $businessIdNumber }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount">{{ __('Country') }}</label>
                    <input type="text" class="form-control input-form-control apply-bg" name="country" id="subject" value="{{ old('companyName') }}" placeholder="{{ __('Enter country') }}" required data-value-missing="{{ __('This field is required.') }}">
                    @error('country')
                        <div class="error">{{ $country }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount">{{ __('State') }}</label>
                    <input type="text" class="form-control input-form-control apply-bg" name="state" id="subject" value="{{ old('companyName') }}" placeholder="{{ __('Enter state') }}" required data-value-missing="{{ __('This field is required.') }}">
                    @error('state')
                        <div class="error">{{ $state }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount">{{ __('City') }}</label>
                    <input type="text" class="form-control input-form-control apply-bg" name="city" id="subject" value="{{ old('companyName') }}" placeholder="{{ __('Enter city') }}" required data-value-missing="{{ __('This field is required.') }}">
                    @error('city')
                        <div class="error">{{ $city }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount">{{ __('Postal Code') }}</label>
                    <input type="text" class="form-control input-form-control apply-bg" name="postalCode" id="subject" value="{{ old('companyName') }}" placeholder="{{ __('Enter postalCode') }}" required data-value-missing="{{ __('This field is required.') }}">
                    @error('postalCode')
                        <div class="error">{{ $postalCode }}</div>
                    @enderror
                </div>
            </div>
        </div>
        

        <div class="label-top">
            <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="floatingTextarea">{{ __('Address') }}</label>
            <textarea name="address" class="form-control l-s0 input-form-control h-100p" id="address" placeholder="{{ __('Enter your address') }}" required data-value-missing="{{ __('This field is required.') }}">{{ old('address') }}</textarea>
            @error('address')
                <div class="error">{{ $address }}</div>
            @enderror
        </div> 

        <div class="d-grid">
            <button  type="submit" class="btn btn-lg btn-primary mt-4" id="ticketCreateSubmitBtn">
                <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                    <span class="visually-hidden"></span>
                </div>
                 <span id="ticketCreateSubmitBtnText">{{ __('Create Ticket') }}</span>
            </button>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-4 back-direction">
            <a href="javascript:void(0);" class="text-gray gilroy-medium d-inline-flex align-items-center position-relative back-btn">
                {!! svgIcons('left_angle') !!}
                <span class="ms-1 back-btn">{{ __('Back') }}</span>
            </a>
        </div>
    </form>
</div>
@endsection

@push('js')
<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}" type="text/javascript"></script>
<script>
    'use strict';
    var csrfToken = $('[name="_token"]').val();
    var submitButtonText = "{{ __('Submitting...') }}";
</script>
@endpush
