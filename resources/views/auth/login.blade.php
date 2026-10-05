<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign in · Retail POS</title>
<link rel="stylesheet" href="{{ asset('css/pos.css') }}">
</head>
<body class="login login-animated-page">
<div class="cursor-dot" aria-hidden="true"></div><div class="cursor-ring" aria-hidden="true"></div>
<main class="animated-login-wrap">
    <section class="animated-login" aria-label="Sign in">
        <div class="login-orbit login-orbit-a" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-b" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-c" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-d" aria-hidden="true"></div>
        <div class="animated-login-content">
            @php($loginLogo = \App\Models\Setting::where('key','logo_path')->value('value'))
            @if($loginLogo)<div class="login-setting-logo"><img src="{{ route('media.file', ['path' => $loginLogo]) }}" alt="Shop logo" onerror="this.closest('.login-setting-logo').remove()"></div>@endif
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
<script src="{{ asset('js/pos.js') }}"></script>
</body>
</html>
