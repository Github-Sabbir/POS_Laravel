<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Retail POS' }}</title>
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">
</head>
<body>
<div class="cursor-dot" aria-hidden="true"></div><div class="cursor-ring" aria-hidden="true"></div>
<aside class="sidebar">
    <div class="brand">
        @php($logoPath = \App\Models\Setting::where('key', 'logo_path')->value('value'))
        @if($logoPath)
            <img class="brand-logo" src="{{ route('media.file', ['path' => $logoPath]) }}" alt="Logo">
        @else
            <span class="brand-mark">R</span>
        @endif
        <span>{{ \App\Models\Setting::where('key', 'shop_name')->value('value') ?: 'Retail POS' }}</span>
    </div>
    <div class="sidebar-label">Workspace</div>
    <nav>
        <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="nav-icon">⌂</span>Dashboard</a>
        @if(auth()->user()->hasPermission('pos.access'))<a class="{{ request()->routeIs('pos') ? 'active' : '' }}" href="{{ route('pos') }}"><span class="nav-icon">▣</span>POS Terminal</a>@endif
        @if(auth()->user()->hasPermission('products.view'))<a class="{{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><span class="nav-icon">◈</span>Products</a>@endif
        @if(auth()->user()->hasPermission('barcodes.manage'))<a class="{{ request()->routeIs('barcodes.*') ? 'active' : '' }}" href="{{ route('barcodes.index') }}"><span class="nav-icon">⌁</span>Barcodes</a>@endif
        @if(auth()->user()->hasPermission('purchases.manage'))<a class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}"><span class="nav-icon">↗</span>Purchases</a>@endif
        @if(auth()->user()->hasPermission('customers.view'))<a class="{{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><span class="nav-icon">♙</span>Customers</a>@endif
        @if(auth()->user()->hasPermission('suppliers.view'))<a class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><span class="nav-icon">◇</span>Suppliers</a>@endif
        @if(auth()->user()->hasPermission('expenses.view'))<a class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}"><span class="nav-icon">−</span>Expenses</a>@endif
        @if(auth()->user()->hasPermission('reports.view'))<a class="{{ request()->routeIs('reports') ? 'active' : '' }}" href="{{ route('reports') }}"><span class="nav-icon">◫</span>Reports</a>@endif
        @if(in_array(auth()->user()->role, ['admin','manager'], true))<a class="{{ request()->routeIs('sales.history') ? 'active' : '' }}" href="{{ route('sales.history') }}"><span class="nav-icon">▤</span>Sales History</a>@endif
        @if(auth()->user()->hasPermission('returns.view'))<a class="{{ request()->routeIs('returns.*') ? 'active' : '' }}" href="{{ route('returns.index') }}"><span class="nav-icon">↩</span>Sales Returns</a>@endif
        @if(auth()->user()->hasPermission('settings.manage'))<a class="{{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('settings') }}"><span class="nav-icon">⚙</span>Settings</a>@endif
        @if(auth()->user()->hasPermission('users.view'))<a class="{{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><span class="nav-icon">♙</span>Users</a>@endif
        @if(auth()->user()->hasPermission('roles.manage'))<a class="{{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}"><span class="nav-icon">◉</span>Roles & Permissions</a>@endif
    </nav>
    <form method="post" action="{{ route('logout') }}">@csrf<button class="nav-btn"><span class="nav-icon">⇥</span>Logout</button></form>
</aside>
<main>
<header class="topbar"><button class="icon-btn" onclick="document.body.classList.toggle('nav-open')">☰</button><div><strong>{{ $title ?? 'Retail POS' }}</strong></div><div class="top-actions"><button class="theme-btn" onclick="toggleTheme()" title="Toggle theme">◐</button><div class="top-user"><span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}</span><span>{{ auth()->user()->name ?? 'User' }}</span></div></div></header>
@if(session('success'))<div class="toast success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="toast error">{{ $errors->first() }}</div>@endif
<div class="page">@yield('content')</div>
</main>
<script src="{{ asset('js/pos.js') }}"></script>
@stack('scripts')
</body>
</html>
