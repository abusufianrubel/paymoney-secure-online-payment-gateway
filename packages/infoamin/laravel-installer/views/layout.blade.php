<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>{{ __('Pay Money') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="icon" type="image/x-icon" href="{{ asset('public/dist/images/default-favicon.png') }}">
        <link rel="stylesheet" href="{{ asset('public/dist/libraries/materialize/materialize.min.css') }}">
        <link href="{{ asset('public/frontend/templates/css/installer.min.css') }}" rel="stylesheet">
        @yield('style')
    </head>
    <body>
        @yield('content')
        <script type="text/javascript" src="{{ asset('public/dist/libraries/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('public/dist/libraries/materialize/materialize.min.js') }}"></script>
        @yield('script')
    </body>
</html>