<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(isset($_COOKIE['BEARER_TOKEN']) && $_COOKIE['BEARER_TOKEN'] != '')
    <meta name="bearer-token" content="{{ $_COOKIE['BEARER_TOKEN'] }}">
    @endif

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<x-navbar />
<x-alerts />

<body style="background-color: #1B1F2A;">
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>

</html>