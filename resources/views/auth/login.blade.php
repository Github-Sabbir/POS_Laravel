<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
@php($shopName = \App\Models\Setting::where('key','shop_name')->value('value') ?: 'Retail POS')
<title>Sign in · {{ $shopName }}</title>
<link rel="stylesheet" href="{{ asset('css/pos.css') }}?v={{ @filemtime(public_path('css/pos.css')) ?: time() }}">
</head>
<body class="login login-animated-page">
<main class="animated-login-wrap">
    <section class="animated-login" aria-label="Sign in">
        <div class="login-orbit login-orbit-a" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-b" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-c" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-d" aria-hidden="true"></div>
        <div class="animated-login-content">
            @php($loginLogo = \App\Models\Setting::where('key','logo_path')->value('value'))
            @if($loginLogo)<div class="login-setting-logo"><img src="{{ route('media.file', ['path' => $loginLogo]) }}" alt="Shop logo" onerror="this.closest('.login-setting-logo').remove()"></div>@endif
            <div class="login-shop-name">{{ $shopName }}</div>
            @if($errors->any())<div class="login-error" role="alert">{{ $errors->first() }}</div>@endif
            <form method="post" action="{{ route('login.store') }}" autocomplete="on" class="animated-login-form">
                @csrf
                <label class="animated-field"><span>Username / Email</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="Enter your email" required autofocus></label>
                <label class="animated-field"><span>Password</span><input type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required></label>
                <button class="animated-login-button" type="submit"><span>Sign in</span></button>
            </form>
        </div>
    </section>
</main>
<script src="{{ asset('js/pos.js') }}?v={{ @filemtime(public_path('js/pos.js')) ?: time() }}"></script>
</body>
</html>
