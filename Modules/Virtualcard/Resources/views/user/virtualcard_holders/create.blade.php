@extends('user.layouts.app')

@push('css')
<link rel="stylesheet" href="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.css') }}">
@endpush

@section('content')

<div class="bg-white pxy-62 shadow" id="VirtualcardHolderCreate">
    <p class="mb-0 f-26 gilroy-Semibold text-uppercase text-center text-dark">{{ __('Holder APPLICATION') }}</p>
    <p class="mb-0 text-center f-14 gilroy-medium text-gray-100 dark-p mt-20"> {{ __('Experience effortless financial management with virtual card, offering secure transactions and unmatched convenience anytime, anywhere.') }} </p>

    <!-- Card Holder Type -->
    <div class="row">
        <div class="col-12">
            <div class="param-ref money-ref r-mt-11">
                <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0">{{ __('Account Type') }}</label>
                <select class="select2" data-minimum-results-for-search="Infinity" name="type" id="type">
                    <option value="Business" {{ old('cardHolderType') == 'Business' ? 'selected': '' }}>{{ __('Business') }}</option>
                    <option value="Individual" {{ old('cardHolderType') == 'Individual' ? 'selected': '' }}>{{ __('Individual') }}</option>
                </select>
            </div>
        </div>
    </div>


    <!-- Business Form -->
    <form method="post" action="{{ route('user.virtualcard_holder.store') }}" enctype="multipart/form-data" id="businessForm">
        @csrf

        <input type="hidden" name="userId" value="{{ auth()->id() }}">
        <input type="hidden" name="cardHolderType" id="cardHolderType" value="Business">

        <!-- Business Name -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="businessName">{{ __('Business Name') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        id="businessName"
                        name="businessName"
                        value="{{ old('businessName') }}"
                        placeholder="{{ __('Enter :x', ['x' => __('business name')]) }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Business name'), 'y' => 30]) }}"
                        minlength="3" data-min-length="{{ __(':x length should be minimum :y charcters.', ['x' => __('Business name'), 'y' => 3]) }}"
                    >
                </div>
                @error('businessName')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Business Id Number -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="businessIdNumber">{{ __('Business Id Number') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="businessIdNumber"
                        id="businessIdNumber"
                        value="{{ old('businessIdNumber') }}"
                        placeholder="{{ __('Enter :x', ['x' => __('business ID')]) }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="50" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Business ID'), 'y' => 50]) }}"
                    >
                </div>
                @error('businessIdNumber')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- FirstName -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="firstName">{{ __('First Name') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="firstName"
                        id="firstName"
                        value="{{ old('firstName') }}"
                        placeholder="{{ __('Enter first name') }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('First name'), 'y' => 30]) }}"
                    >
                </div>
                @error('firstName')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- LastName -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="lastName">{{ __('Last Name') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="lastName"
                        id="lastName"
                        value="{{ old('lastName') }}"
                        placeholder="{{ __('Enter last name') }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Last name'), 'y' => 30]) }}"
                        minlength="3" data-min-length="{{ __(':x length should be minimum :y charcters.', ['x' => __('Last name'), 'y' => 3]) }}"
                    >
                </div>
                @error('lastName')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Country -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0" for="country">{{ __('Country') }}</label>
                    <select class="select2"  name="country" id="country">
                        @foreach ($countries as $country)
                            <option value="{{ $country->name }}" {{ old('country') == $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- State -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="state">{{ __('State') }}</label>
                    <input 
                        type="text" 
                        oninput="this.value = this.value.toUpperCase()" 
                        class="form-control input-form-control apply-bg" 
                        name="state" 
                        id="state" 
                        value="{{ old('state') }}" 
                        placeholder="{{ __('Enter state') }}"
                        maxlength="2" 
                        data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('state'), 'y' => 2]) }}"
                    >
                </div>
                @error('state')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- City -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="city">{{ __('City') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="city"
                        id="city"
                        value="{{ old('city') }}"
                        placeholder="{{ __('Enter :x', ['x' => __('city')]) }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="60" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('City'), 'y' => 60]) }}"
                    >
                </div>
                @error('city')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Postal Code -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="postalCode">{{ __('Postal Code') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="postalCode"
                        id="postalCode"
                        value="{{ old('postalCode') }}"
                        placeholder="{{ __('Enter :x', ['x' => __('postal code')]) }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Postal Code'), 'y' => 30]) }}"
                    >
                </div>
                @error('postalCode')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Address -->
        <div class="label-top">
            <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="address">{{ __('Address') }}</label>
            <textarea
                class="form-control l-s0 input-form-control h-100p"
                name="address"
                id="address"
                placeholder="{{ __('Enter :x', ['x' => __('address')]) }}"
                required data-value-missing="{{ __('This field is required.') }}"
                maxlength="191" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Address'), 'y' => 191]) }}"
            >
                {{ old('address') }}
            </textarea>
            @error('address')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
            <button  type="submit" class="btn btn-lg btn-primary mt-4" id="businessFormSubmitBtn">
                <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                    <span class="visually-hidden"></span>
                </div>
                 <span id="businessFormSubmitBtnText">{{ __('Submit') }}</span>
            </button>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-4 back-direction">
            <a href="{{ route('user.virtualcard_holder.index') }}" class="text-gray gilroy-medium d-inline-flex align-items-center position-relative back-btn">
                {!! svgIcons('left_angle') !!}
                <span class="ms-1 back-btn">{{ __('Back') }}</span>
            </a>
        </div>
    </form>

    <!-- Individual Form -->
    <form method="post" action="{{ route('user.virtualcard_holder.store') }}" enctype="multipart/form-data" id="individualForm" class="d-none">
        @csrf

        <input type="hidden" name="userId" value="{{ auth()->id() }}">
        <input type="hidden" name="cardHolderType" value="Individual" id="cardHolderType">

        <!-- FirstName -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="firstName">{{ __('First Name') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="firstName"
                        id="firstName"
                        value="{{ old('firstName') }}"
                        placeholder="{{ __('Enter first name') }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('First name'), 'y' => 30]) }}"
                    >
                </div>
                @error('firstName')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- LastName -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="lastName">{{ __('Last Name') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="lastName"
                        id="lastName"
                        value="{{ old('lastName') }}"
                        placeholder="{{ __('Enter last name') }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Last name'), 'y' => 30]) }}"
                    >
                </div>
                @error('lastName')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Gender -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0" for="gender">{{ __('Gender') }}</label>
                    <select class="select2" data-minimum-results-for-search="Infinity" name="gender" id="gender">
                        @foreach (Modules\Virtualcard\Enums\Genders::cases() as $gender)
                            <option value="{{ $gender->value }}" {{ old('gender') == $gender->value ? 'selected' : '' }}>{{ $gender->value }}</option>
                        @endforeach
                    </select>
                    @error('gender')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Date Of Birth -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="dateOfBirth">{{ __('Date of Birth') }}</label>
                    <input 
                        type="text" 
                        class="form-control input-form-control apply-bg" 
                        name="dateOfBirth" id="dateOfBirth" 
                        value="{{ old('dateOfBirth') }}" 
                        placeholder="{{ __('Enter date of birth') }}"
                    >
                </div>
                @error('dateOfBirth')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Verification Document Type -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0" for="verificationDocumentType">{{ __('Verification Document Type') }}</label>
                    <select class="select2" data-minimum-results-for-search="Infinity" name="verificationDocumentType" id="verificationDocumentType">
                        @foreach (Modules\Virtualcard\Enums\VerificationDocumentTypes::cases() as $type)
                            <option value="{{ $type->value }}" {{ old('verificationDocumentType') == $type->value ? 'selected' : '' }}>{{ $type->value }}</option>
                        @endforeach
                    </select>
                    @error('verificationDocumentType')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Verification Document ID -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="verificationDocumentIdNumber">{{ __('Document Id') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg" name="verificationDocumentIdNumber"
                        id="verificationDocumentIdNumber"
                        value="{{ old('verificationDocumentIdNumber') }}"
                        placeholder="{{ __('Enter :x', ['x' => __('Id Number')]) }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Document Id'), 'y' => 30]) }}"
                    >
                </div>
                @error('verificationDocumentIdNumber')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Verification Document Image -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="verificationDocumentImageFront">{{ __('Document Front') }}</label>
                    <input 
                        type="file" 
                        class="form-control input-form-control apply-bg" 
                        name="verificationDocumentImageFront" 
                        id="subject" 
                        value="{{ old('verificationDocumentImageFront') }}" 
                        placeholder="{{ __('Enter verification document front image') }}" 
                        required data-value-missing="{{ __('This field is required.') }}"
                        >
                </div>
                @error('verificationDocumentImageFront')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="verificationDocumentImageBack">{{ __('Document Back') }}</label>
                    <input type="file" class="form-control input-form-control apply-bg" name="verificationDocumentImageBack" id="subject" value="{{ old('verificationDocumentImageBack') }}" placeholder="{{ __('Enter verification document back image') }}" required data-value-missing="{{ __('This field is required.') }}">
                </div>
                @error('verificationDocumentImageBack')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Country -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0" for="individualCountry">{{ __('Country') }}</label>
                    <select class="select2"  name="country" id="individualCountry">
                        @foreach ($countries as $country)
                            <option value="{{ $country->name }}" {{ old('country') == $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- State -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="state">{{ __('State') }}</label>
                    <input type="text" oninput="this.value = this.value.toUpperCase()" class="form-control input-form-control apply-bg" name="state" id="state" value="{{ old('state') }}" placeholder="{{ __('Enter state') }}"
                    maxlength="2" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('state'), 'y' => 2]) }}">
                </div>
                @error('state')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- City -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="city">{{ __('City') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="city"
                        id="city"
                        value="{{ old('city') }}"
                        placeholder="{{ __('Enter :x', ['x' => __('city')]) }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="60" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('City'), 'y' => 60]) }}"
                    >
                </div>
                @error('city')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Postal Code -->
        <div class="row">
            <div class="col-12">
                <div class="label-top new-ticket">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="postalCode">{{ __('Postal Code') }}</label>
                    <input
                        type="text"
                        class="form-control input-form-control apply-bg"
                        name="postalCode"
                        id="postalCode"
                        value="{{ old('postalCode') }}"
                        placeholder="{{ __('Enter :x', ['x' => __('postal code')]) }}"
                        required data-value-missing="{{ __('This field is required.') }}"
                        maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Postal Code'), 'y' => 30]) }}"
                    >
                </div>
                @error('postalCode')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Address -->
        <div class="label-top">
            <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-amount" for="address">{{ __('Address') }}</label>
            <textarea class="form-control l-s0 input-form-control h-100p" name="address" id="address" placeholder="{{ __('Enter your address') }}" required data-value-missing="{{ __('This field is required.') }}" maxlength="191" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Address'), 'y' => 191]) }}">{{ old('address') }}</textarea>
            @error('address')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
            <button  type="submit" class="btn btn-lg btn-primary mt-4" id="individualFormSubmitBtn">
                <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                    <span class="visually-hidden"></span>
                </div>
                 <span id="individualFormSubmitBtnText">{{ __('Submit') }}</span>
            </button>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-4 back-direction">
            <a href="{{ route('user.virtualcard_holder.index') }}" class="text-gray gilroy-medium d-inline-flex align-items-center position-relative back-btn">
                {!! svgIcons('left_angle') !!}
                <span class="ms-1 back-btn holderBackBtnText">{{ __('Back') }}</span>
            </a>
        </div>
    </form>
</div>
@endsection

@push('js')
<script type="text/javascript">
    'use strict';
    var submitBtnText = "{{ __('Processing...') }}";
</script>
<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('public/dist/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/user/virtualcardholder.min.js') }}"></script>

@endpush
