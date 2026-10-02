<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login · Retail POS</title>
<link rel="stylesheet" href="<?php echo e(asset('css/pos.css')); ?>">
</head>
<body class="login login-animated-page">
<div class="cursor-dot" aria-hidden="true"></div><div class="cursor-ring" aria-hidden="true"></div>

<main class="animated-login-wrap">
    <section class="animated-login" aria-label="Retail POS sign in">
        <div class="login-orbit login-orbit-a" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-b" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-c" aria-hidden="true"></div>
        <div class="login-orbit login-orbit-d" aria-hidden="true"></div>

        <div class="animated-login-content">
            <div class="animated-login-brand">
                <span class="brand-mark">R</span>
                <span>Retail POS</span>
            </div>
            <h1>Login</h1>
            <p class="login-subtitle">Sign in to continue to your workspace</p>

            <?php if($errors->any()): ?>
                <div class="login-error" role="alert"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo e(route('login.store')); ?>" autocomplete="on" class="animated-login-form">
                <?php echo csrf_field(); ?>
                <label class="animated-field">
                    <span class="sr-only">Username or email</span>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" autocomplete="username" placeholder="Username" required autofocus>
                </label>
                <label class="animated-field">
                    <span class="sr-only">Password</span>
                    <input type="password" name="password" autocomplete="current-password" placeholder="Password" required>
                </label>
                <button class="animated-login-button" type="submit"><span>Sign in</span></button>
                <label class="animated-remember"><input type="checkbox" name="remember"><span>Remember me</span></label>
            </form>
        </div>
    </section>
</main>
<script src="<?php echo e(asset('js/pos.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\Users\Dell_Inspiron\Desktop\New folder\resources\views/auth/login.blade.php ENDPATH**/ ?>