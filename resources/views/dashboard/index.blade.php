@extends('layouts.app')
@section('content')
<div class="page-head">
    <div><div class="eyebrow">STORE OVERVIEW</div><h1>Good day, {{ auth()->user()->name }} 👋</h1><p class="muted">Activity from {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}.</p></div>
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager')<a class="secondary" href="{{ route('reports') }}">View Reports</a>@endif
    <a class="primary" href="{{ route('pos') }}">＋ Open POS</a>
</div>
@if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
<form class="glass dashboard-filter" method="get">
    <div><strong>Activity period</strong><small class="muted">{{ auth()->user()->role === 'manager' ? 'Manager: maximum 7 days' : 'Admin: choose any period' }}</small></div>
    <label>From <input type="date" name="from" value="{{ $from->toDateString() }}"></label>
    <span class="date-arrow">→</span>
    <label>To <input type="date" name="to" value="{{ $to->toDateString() }}"></label>
    @if(auth()->user()->role === 'manager')<a class="secondary" href="{{ route('dashboard', ['from'=>now()->subDays(6)->toDateString(),'to'=>now()->toDateString()]) }}">Last 7 Days</a>@endif
    <button class="primary" type="submit">Apply</button>
    <a class="secondary" href="{{ route('dashboard') }}">Today</a>
</form>
@endif
<div class="stats">
    <div class="stat"><div class="stat-top"><span>Sales</span><span class="stat-icon">↗</span></div><b>৳{{ number_format($sales,2) }}</b><small>{{ $transactions }} completed transactions</small></div>
    <div class="stat"><div class="stat-top"><span>Returns</span><span class="stat-icon">↩</span></div><b>৳{{ number_format($returns,2) }}</b><small>Refund value in period</small></div>
    <div class="stat"><div class="stat-top"><span>Low Stock</span><span class="stat-icon">!</span></div><b>{{ $lowStock }}</b><small>Products at/below minimum</small></div>
    @if($canSeeFinance)<div class="stat finance-stat"><div class="stat-top"><span>Net Profit</span><span class="stat-icon">✓</span></div><b>৳{{ number_format($netProfit,2) }}</b><small>Revenue − COGS − expenses</small></div>@endif
</div>
@if($canSeeFinance)
<div class="stats finance-stats dashboard-finance-same">
    <div class="stat"><div class="stat-top"><span>Purchases</span><span class="stat-icon">↗</span></div><b>৳{{ number_format($purchases,2) }}</b><small>Stock investment</small></div>
    <div class="stat"><div class="stat-top"><span>COGS</span><span class="stat-icon">◈</span></div><b>৳{{ number_format($cogs,2) }}</b><small>Cost of sold items</small></div>
    <div class="stat"><div class="stat-top"><span>Expenses</span><span class="stat-icon">−</span></div><b>৳{{ number_format($expenses,2) }}</b><small>Operating expenses</small></div>
    <div class="stat"><div class="stat-top"><span>Gross Profit</span><span class="stat-icon">✓</span></div><b>৳{{ number_format($grossProfit,2) }}</b><small>Revenue before expenses</small></div>
</div>
@endif
<div class="dashboard-grid">
    <section class="glass card chart-card">
        <div class="section-head"><div><h2>Sales activity</h2><p class="muted">{{ $days === 1 ? '24-hour activity · 12 AM to 12 AM' : 'Daily activity for the selected period' }}</p></div><span class="cart-badge">{{ $days }} day{{ $days === 1 ? '' : 's' }}</span></div>
        <div class="chart-bars chart-bars-dashboard">
            @php($maxActivity=max(1,max(array_column($activity,'sales'))))
            @foreach($activity as $point)<div class="chart-column"><div class="chart-bar" style="height:{{ max(4, ($point['sales'] / $maxActivity) * 100) }}%" title="{{ $point['label'] }} · ৳{{ number_format($point['sales'],2) }}"></div><small>{{ $point['label'] }}</small></div>@endforeach
        </div>
    </section>
    <section class="glass card"><h2>Quick actions</h2><p class="muted">Jump directly into common tasks.</p><div class="quick">
        <a href="{{route('pos')}}">Open POS</a>
        @if(auth()->user()->hasPermission('products.manage'))<a href="{{route('products.create')}}">＋ Product</a>@endif
        @if(auth()->user()->hasPermission('customers.create'))<a href="{{route('customers.create')}}">＋ Customer</a>@endif
        @if(auth()->user()->hasPermission('expenses.create'))<a href="{{route('expenses.create')}}">＋ Expense</a>@endif
    </div><div class="mini-list" style="margin-top:18px">
        @if($canSeeFinance)<div class="mini-row"><span>Reports</span><a class="secondary" href="{{route('reports')}}">View →</a></div>@endif
        @if(auth()->user()->hasPermission('returns.view'))<div class="mini-row"><span>Sales Returns</span><a class="secondary" href="{{route('returns.index')}}">Open →</a></div>@endif
    </div></section>
</div>
@endsection
