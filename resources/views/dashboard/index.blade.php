<?php

?>
@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">STORE OVERVIEW</div><h1>Good day, {{ auth()->user()->name }} 👋</h1><p class="muted">Here is your store overview for today.</p></div><a class="primary" href="{{ route('pos') }}">＋ Open POS</a></div>
<div class="stats">
    <div class="stat"><div class="stat-top"><span>Today's Sales</span><span class="stat-icon">↗</span></div><b>৳{{ number_format($sales,2) }}</b><small>{{ $transactions }} completed transactions</small></div>
    <div class="stat"><div class="stat-top"><span>Returns</span><span class="stat-icon">↩</span></div><b>৳{{ number_format($returns,2) }}</b><small>Today's refund value</small></div>
    <div class="stat"><div class="stat-top"><span>Low Stock</span><span class="stat-icon">!</span></div><b>{{ $lowStock }}</b><small>Products at/below minimum</small></div>
    @if($canSeeFinance)
    <div class="stat"><div class="stat-top"><span>Net Profit</span><span class="stat-icon">✓</span></div><b>৳{{ number_format($netProfit,2) }}</b><small>Finance view · gross profit less expenses</small></div>
    @endif
</div>
@if($canSeeFinance)
<div class="glass finance-strip"><span>Purchases <b>৳{{ number_format($purchases,2) }}</b></span><span>COGS <b>৳{{ number_format($cogs,2) }}</b></span><span>Expenses <b>৳{{ number_format($expenses,2) }}</b></span><span>Gross Profit <b>৳{{ number_format($grossProfit,2) }}</b></span></div>
@endif
<div class="dashboard-grid"><div class="glass card chart-card"><div class="section-head"><div><h2>Sales activity</h2><p class="muted">Visual overview · dashboard preview</p></div><span class="cart-badge">Today</span></div><div class="chart-bars">@foreach([42,58,47,72,61,84,68,91,77,64,82,96] as $h)<div class="chart-bar" style="height:{{$h}}%"></div>@endforeach</div><div class="chart-labels"><span>8AM</span><span>10AM</span><span>12PM</span><span>2PM</span><span>4PM</span><span>6PM</span><span>8PM</span></div></div>
<div class="glass card"><h2>Quick actions</h2><p class="muted">Jump directly into common tasks.</p><div class="quick"><a href="{{route('pos')}}">Open POS</a>@if(auth()->user()->hasPermission('products.view'))<a href="{{route('products.create')}}">＋ Product</a>@endif @if(auth()->user()->hasPermission('customers.create'))<a href="{{route('customers.create')}}">＋ Customer</a>@endif @if(auth()->user()->hasPermission('expenses.create'))<a href="{{route('expenses.create')}}">＋ Expense</a>@endif</div><div style="margin-top:18px" class="mini-list">@if($canSeeFinance)<div class="mini-row"><span>Reports</span><a class="link" href="{{route('reports')}}">View →</a></div>@endif @if(auth()->user()->hasPermission('returns.view'))<div class="mini-row"><span>Sales Returns</span><a class="link" href="{{route('returns.index')}}">Open →</a></div>@endif</div></div></div>
@endsection
