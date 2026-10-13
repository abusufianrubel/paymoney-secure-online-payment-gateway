<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ __('Page Not Found | :x', ['x' => settings('name')]) }}</title>

    <script src="{{ asset('public/frontend/templates/js/flashesh-dark.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('public/dist/libraries/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/templates/css/prism.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/templates/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/templates/css/owl-css/owl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/frontend/templates/css/404.min.css') }}">

    <link rel="shortcut icon" href="{{ faviconPath() }}" />
</head>

<body>
	<div class="position-relative vh-100 log-bg d-flex flex-column align-items-center pb-11 overflow-auto">
        <img class="mt-54p w-[175px] h-[42px]" src="{{ image(settings('logo'), 'logo') }}" alt="brand-logo">
        <div class="mt-14 relative flex flex-col items-center">
            <p class="error-code gilroy-Semibold">{{ __('404') }}</p>
            <p class="error-message text-center gilroy-medium mb-5">{{ __('The page you’re looking for appears to have been moved, deleted or doesn’t exist. We apologize for the inconveniences.') }}</p>
            <a href="{{ route('home')}}" class="border d-flex align-items-center justify-content-center log-btn rounded ml-60 mt-n4p mt-54p m-auto">
                <span class="text-lg homepage-link gilroy-medium color-white text-uppercase">{{ __('Go to Home') }}</span> 
            </a>
        </div>
    </div>

    <!-- Footer section -->
    <script src="{{ asset('public/dist/libraries/jquery/dist/jquery.min.js') }}"></script>  
    <script src="{{ asset('public/frontend/templates/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('public/dist/libraries/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/user/templates/js/main.min.js') }}"></script>
    <script src="{{ asset('public/frontend/templates/js/prism.min.js') }}"></script>
</body>

</html>