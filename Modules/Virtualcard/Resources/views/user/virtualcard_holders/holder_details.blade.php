<div class="modal fade" id="cardholder-Info-{{ $key }}" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase gilroy-medium" id="userInfoModalLabel">{{ $application->card_holder_type }}</h5>
                <button type="button" class="cursor-pointer close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <span class="close-div position-absolute modal-close-btn rtl-wrap-four text-gray-100 d-flex align-items-center justify-content-center">
                        {!! svgIcons('cross_icon') !!}
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="overview-section">
                    <h6 class="gilroy-medium text-dark">{{ cardTitle($application) }}</h6>
                    <p class="mb-3 gilroy-medium"><i class="bi bi-bank"></i> 
                        <span class="text-dark info-value gilroy-medium f-13"> {{ $application->address . ', ' . $application->city .', '. $application->postal_code }}</span>
                    </p>
                    <span class="badge gilroy-regular bg-{{ getBgColor($application->status) }}"> {{ $application->status }}</span>
                </div>
                <div class="row">
                    <div class="col-sm-6">

                        @if(\Modules\Virtualcard\Enums\CardHolderTypes::BUSINESS->value == $application->card_holder_type) 
                            <p>
                                <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Name') }}:</span>
                                <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ cardTitle($application) }}</span>
                            </p>
                            <p>
                                <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Business Name') }}:</span>
                                <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->business_name }}</span>
                            </p>
                            <p>
                                <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Business ID') }}:</span>
                                <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->business_id_number }}</span>
                            </p>

                        @endif
                        
                        @if(\Modules\Virtualcard\Enums\CardHolderTypes::INDIVIDUAL->value == $application->card_holder_type) 
                            <p>
                                <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Gender') }}:</span>
                                <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->gender }}</span>
                            </p>
                            <p>
                                <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Date of Birth') }}:</span>
                                <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->date_of_birth }}</span>
                            </p>
                            <p>
                                <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Verified Via') }}:</span>
                                <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->verification_document_type }}</span>
                            </p>
                            <p>
                                <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Document ID') }}:</span>
                                <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->verification_document_id_number }}</span>
                            </p>
                            
                        @endif
                        
                    </div>
                    <div class="col-sm-6">
                            @if(\Modules\Virtualcard\Enums\CardHolderTypes::INDIVIDUAL->value == $application->card_holder_type)
                            <p>
                                <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Document') }}:</span>
                                <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->verification_document_image }}</span>
                            </p>
                        @endif
                        <p>
                            <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('City') }}:</span>
                            <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->city }}</span>
                        </p>
                        <p>
                            <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Postal Code') }}:</span>
                            <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->postal_code }}</span>
                        </p>
                        <p>
                            <span class="mb-0 mt-20 text-gray-100 gilroy-medium f-13 leading-20 r-f-9 r-mt-11">{{ __('Country') }}:</span>
                            <span class="info-value mb-0 mt-5p text-dark gilroy-medium f-15 leading-22 r-text">{{ $application->country }}</span>
                        </p>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>