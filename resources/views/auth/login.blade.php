<?php

?>
<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title><link rel="stylesheet" href="{{asset('css/pos.css')}}"></head><body class="login"><div class="login-card glass"><div class="brand">◈ Retail POS</div><h1>Welcome back</h1><p>Sign in to your shop</p><form method="post" action="{{route('login.store')}}">@csrf<label>Email<input type="email" name="email" value="{{old('email')}}" required></label><label>Password<input type="password" name="password" required></label><label class="check"><input type="checkbox" name="remember"> Remember me</label><button class="primary wide">Sign in</button></form><small>Demo: admin@example.com / password</small></div></body></html>
