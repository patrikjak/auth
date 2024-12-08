<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-config" content="/favicon/browserconfig.xml">
    @stack('meta')
    <title>@yield('title') - {{ config('pjauth.app_name', 'App') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/pjutils/assets/main.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/pjauth/assets/app.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/pjauth/assets/main.js') }}">
    @if(config('pjauth.recaptcha.enabled'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('pjauth.recaptcha.site_key') }}"></script>
    @endif
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
</head>
<body>

    <div class="navigation">
        <div class="logo">
            <a href="/">
                @if($logo)
                    <img src="{{ asset($logo) }}" alt="logo">
                @else
                    @lang('pjauth::pages.general.home')
                @endisset
            </a>
        </div>

        @isset($links)
            <div class="links">
                {{ $links }}
            </div>
        @endisset
    </div>

    <div class="pj-auth">
        <div class="image">
            {{ $image }}
        </div>
        <div class="form">
            <div class="wrapper">
                <h1>{{ $title }}</h1>
                {{ $slot }}
            </div>
        </div>
    </div>

</body>
</html>