<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?php echo e($title ?? 'Retail POS'); ?></title><link rel="stylesheet" href="<?php echo e(asset('css/pos.css')); ?>"></head><body>
<aside class="sidebar">
  <div class="brand"><span class="brand-mark">R</span><span>Retail POS</span></div>
  <div class="sidebar-label">Workspace</div>
  <nav>
    <a class="<?php echo e(request()->routeIs('dashboard')?'active':''); ?>" href="<?php echo e(route('dashboard')); ?>"><span class="nav-icon">⌂</span>Dashboard</a>
    <a class="<?php echo e(request()->routeIs('pos')?'active':''); ?>" href="<?php echo e(route('pos')); ?>"><span class="nav-icon">▣</span>POS Terminal</a>
    <a class="<?php echo e(request()->routeIs('products.*')?'active':''); ?>" href="<?php echo e(route('products.index')); ?>"><span class="nav-icon">◈</span>Products</a>
    <a class="<?php echo e(request()->routeIs('barcodes.*')?'active':''); ?>" href="<?php echo e(route('barcodes.index')); ?>"><span class="nav-icon">⌁</span>Barcodes</a>
    <a class="<?php echo e(request()->routeIs('purchases.*')?'active':''); ?>" href="<?php echo e(route('purchases.index')); ?>"><span class="nav-icon">↗</span>Purchases</a>
    <a class="<?php echo e(request()->routeIs('customers.*')?'active':''); ?>" href="<?php echo e(route('customers.index')); ?>"><span class="nav-icon">♙</span>Customers</a>
    <a class="<?php echo e(request()->routeIs('suppliers.*')?'active':''); ?>" href="<?php echo e(route('suppliers.index')); ?>"><span class="nav-icon">◇</span>Suppliers</a>
    <a class="<?php echo e(request()->routeIs('expenses.*')?'active':''); ?>" href="<?php echo e(route('expenses.index')); ?>"><span class="nav-icon">−</span>Expenses</a>
    <a class="<?php echo e(request()->routeIs('reports')?'active':''); ?>" href="<?php echo e(route('reports')); ?>"><span class="nav-icon">◫</span>Reports</a>
    <a class="<?php echo e(request()->routeIs('returns.*')?'active':''); ?>" href="<?php echo e(route('returns.index')); ?>"><span class="nav-icon">↩</span>Returns</a>
    <a class="<?php echo e(request()->routeIs('settings')?'active':''); ?>" href="<?php echo e(route('settings')); ?>"><span class="nav-icon">⚙</span>Settings</a>
    <?php if(auth()->user()->hasPermission('users.view')): ?><a class="<?php echo e(request()->routeIs('users.*')?'active':''); ?>" href="<?php echo e(route('users.index')); ?>"><span class="nav-icon">♙</span>Users</a><?php endif; ?>
    <?php if(auth()->user()->hasPermission('roles.manage')): ?><a class="<?php echo e(request()->routeIs('roles.*')?'active':''); ?>" href="<?php echo e(route('roles.index')); ?>"><span class="nav-icon">◉</span>Roles</a><?php endif; ?>
  </nav>
  <form method="post" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?><button class="nav-btn"><span class="nav-icon">⇥</span>Logout</button></form>
</aside>
<main><header class="topbar"><button class="icon-btn" onclick="document.body.classList.toggle('nav-open')">☰</button><div><strong><?php echo e($title ?? 'Retail POS'); ?></strong></div><div class="top-actions"><button class="theme-btn" onclick="toggleTheme()" title="Toggle theme">◐</button><div class="top-user"><span class="avatar"><?php echo e(strtoupper(substr(auth()->user()->name ?? 'U',0,1))); ?></span><span><?php echo e(auth()->user()->name ?? 'User'); ?></span></div></div></header>
<?php if(session('success')): ?><div class="toast success"><?php echo e(session('success')); ?></div><?php endif; ?> <?php if($errors->any()): ?><div class="toast error"><?php echo e($errors->first()); ?></div><?php endif; ?>
<div class="page"><?php echo $__env->yieldContent('content'); ?></div></main><script src="<?php echo e(asset('js/pos.js')); ?>"></script><?php echo $__env->yieldPushContent('scripts'); ?></body></html>
<?php /**PATH C:\xampp\htdocs\retail-pos-full\resources\views/layouts/app.blade.php ENDPATH**/ ?>