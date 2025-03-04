<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ $appName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/pjutils/assets/main.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/pjauth/assets/app.css') }}">
    @if($enabledRecaptcha)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
    @endif
    <script src="{{ asset('vendor/pjauth/assets/main.js') }}" type="module"></script>
    @isset($icon)
        <link rel="icon" type="{{ $iconType }}" href="{{ asset($icon) }}">
    @endisset
</head>
<body
    @if($enabledRecaptcha) data-recaptcha-site-key="{{ $recaptchaSiteKey }}" @endif
>

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