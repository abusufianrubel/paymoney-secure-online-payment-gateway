    <!-- Hero section -->
    <div class="standards-hero-section">
        <div class="px-240">
            <div class="d-flex flex-column align-items-start">
                <nav class="customize-bcrm">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Developer') }}</li>
                    </ol>
                </nav>
                <div class="btn-section">
                    <button class="btn btn-dark btn-lg">{{ __('Developer') }}</button>
                </div>
                <div class="merchant-text">
                    <p>{{ __('With :x Standard and Express, you can easily and safely receive online payments from your customer.', ['x' => settings('name')]) }}</p>
                </div>
            </div>
        </div>
    </div>