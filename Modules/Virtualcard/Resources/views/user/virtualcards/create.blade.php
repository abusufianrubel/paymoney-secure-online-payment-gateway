@extends('user.layouts.app')

@section('content')

<div class="bg-white pxy-62 shadow" id="VirtualcardCreate">

    <p class="mb-0 f-26 gilroy-Semibold text-uppercase text-center text-dark">{{ __('Card APPLICATION') }}</p>
    <p class="mb-0 text-center f-14 gilroy-medium text-gray-100 dark-p mt-20"> {{ __('Empower your transactions with VirtualCard—safe, instant, and hassle-free payments wherever you go') }} </p>

    <!-- Card Application Form -->
    <form method="post" action="{{ route('user.virtualcard.store') }}" id="virtualcardForm">
        @csrf


        <!-- Virtualcard Holder -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0" for="virtualcardHolderId">{{ __('Select a Card Holder') }}</label>
                    <select class="select2"  name="virtualcardHolderId" id="virtualcardHolderId">
                        @foreach ($cardholders as $cardholder)
                            <option value="{{ $cardholder->id }}" data-name="{{ cardTitle($cardholder) }}" data-address="{{ $cardholder->address . ', ' . $cardholder->city . ', ' . $cardholder->postal_code }}" >{{ cardTitle($cardholder) }}</option>
                        @endforeach
                    </select>
                    @error('virtualcardHolderId')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>


        <!-- Prefered Currency -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0" for="preferredCurrency">{{ __('Preferred Currency') }}</label>
                    <select class="select2"  name="preferredCurrency" id="preferredCurrency">
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->code }}">{{ $currency->code }}</option>
                        @endforeach
                    </select>
                    @error('preferredCurrency')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    <p class="mb-0 text-gray-100 dark-B87 gilroy-regular f-14" id="cardCreationFee"></p>

                </div>
            </div>
        </div>

        <!-- Prefered Category -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0" for="preferredCategory">{{ __('Preferred Category') }}</label>
                    <select class="select2"  name="preferredCategory" id="preferredCategory">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('preferredCategory')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Prefered Network -->
        <div class="row">
            <div class="col-12">
                <div class="param-ref money-ref r-mt-11">
                    <label class="gilroy-medium text-gray-100 mb-2 f-15 mt-4 r-mt-0" for="cardBrand">{{ __('Preferred Network') }}</label>
                    <select class="select2"  name="cardBrand" id="cardBrand">
                        @foreach (\Modules\Virtualcard\Enums\CardBrands::cases() as $brand)
                            <option value="{{ $brand->value }}">{{ $brand->value }}</option>
                        @endforeach
                    </select>
                    @error('cardBrand')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Payment Methods Empty -->
        <div class="row">
            <div class="col-12">
                <div class="mt-20 param-ref d-none" id="preferredCurrencyEmpty">
                    <label class="gilroy-medium text-warning mb-2 f-15">{{ __('Fees and limits are currently unavailable.') }}</label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid">
            <button  type="submit" class="btn btn-lg btn-primary mt-4 d-none" id="virtualcardFormSubmitBtn">
                <div class="spinner spinner-border text-white spinner-border-sm mx-2 d-none" role="status">
                    <span class="visually-hidden"></span>
                </div>
                 <span id="virtualcardFormSubmitBtnText">{{ __('Submit') }}</span>
            </button>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-4 back-direction">
            <a href="{{ route('user.virtualcard.index') }}" class="text-gray gilroy-medium d-inline-flex align-items-center position-relative back-btn">
                {!! svgIcons('left_angle') !!}
                <span class="ms-1 back-btn">{{ __('Back') }}</span>
            </a>
        </div>
    </form>

</div>
@endsection

@push('js')
<script type="text/javascript">
    'use strict';
    let _token = "{{ csrf_token() }}";
    let cardCreationFeeUrl = "{{ route('user.virtualcard.creation_fee') }}";
    let buttonText = "{{ __('Submit') }}";
    var submitBtnText = "{{ __('Processing...') }}";
</script>
<script src="{{ asset('public/dist/plugins/html5-validation/validation.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('Modules/Virtualcard/Resources/assets/js/user/virtualcard.min.js') }}"></script>

@endpush
