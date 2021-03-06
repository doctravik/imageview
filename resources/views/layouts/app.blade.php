<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('title')</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'storage' => Storage::url('/')
        ]) !!};
    </script>
</head>
<body>
    <div id="app" class="site" v-cloak>
        @include('layouts.partials.nav')

        <section class="section flex-1">
            <div class="container">
                @include('layouts.partials.flash')
                
                @yield('content')
            </div>
        </section>

        @include('layouts.partials.footer')
    </div>

    <!-- Scripts -->
    <script src="{{ mix('/js/manifest.js') }} "></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
