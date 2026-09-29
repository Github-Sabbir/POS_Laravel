<?php

?>
@extends('layouts.app')
@section('content')
<div class="page-head">
    <div><div class="eyebrow">ANALYTICS</div><h1>Reports</h1><p class="muted">Sales, returns, cost, profit and payment overview for the selected period.</p></div>
</div>

<form class="glass toolbar" method="get">
    <label>From<input type="date" name="from" value="{{ $from->toDateString() }}"></label>
    <label>To<input type="date" name="to" value="{{ $to->toDateString() }}"></label>
    <button class="primary" type="submit">Apply Filter</button>
    <a class="secondary" href="{{ route('reports') }}">This Month</a>
</form>

<div class="stats report-stats">
    <div class="stat"><span>Gross Sales</span><b>৳{{ number_format($grossSales,2) }}</b><small>{{ $transactions }} transactions</small></div>
    <div class="stat"><span>Returns / Refunds</span><b>৳{{ number_format($returns,2) }}</b><small>Returned goods value</small></div>
    <div class="stat"><span>Net Revenue</span><b>৳{{ number_format($revenue,2) }}</b><small>Sales less refunds</small></div>
    <div class="stat"><span>COGS</span><b>৳{{ number_format($cogs,2) }}</b><small>Cost of goods sold</small></div>
    <div class="stat"><span>Gross Profit</span><b>৳{{ number_format($gross,2) }}</b><small>Revenue less COGS</small></div>
    <div class="stat"><span>Operating Expenses</span><b>৳{{ number_format($expenses,2) }}</b><small>Recorded expenses</small></div>
    <div class="stat"><span>Net Profit</span><b>৳{{ number_format($net,2) }}</b><small>Gross profit less expenses</small></div>
    <div class="stat"><span>Low Stock</span><b>{{ $low }}</b><small>Products at/below minimum</small></div>
</div>

<div class="report-grid">
    <section class="glass card"><div class="section-head"><div><h2>Payment Methods</h2><p class="muted">Completed sales by payment method.</p></div></div>
        <div class="table-wrap"><table><thead><tr><th>Method</th><th>Transactions</th><th>Amount</th></tr></thead><tbody>
        @forelse($paymentBreakdown as $row)<tr><td>{{ ucfirst($row->payment_method) }}</td><td>{{ $row->transactions }}</td><td>৳{{ number_format($row->amount,2) }}</td></tr>@empty<tr><td colspan="3" class="empty">No sales in this period.</td></tr>@endforelse
        </tbody></table></div>
    </section>
    <section class="glass card"><div class="section-head"><div><h2>Top Products</h2><p class="muted">Highest sales value in the selected period.</p></div></div>
        <div class="table-wrap"><table><thead><tr><th>Product</th><th>Qty</th><th>Sales</th></tr></thead><tbody>
        @forelse($topProducts as $row)<tr><td>{{ $row->name }}</td><td>{{ number_format($row->quantity,3) }}</td><td>৳{{ number_format($row->amount,2) }}</td></tr>@empty<tr><td colspan="3" class="empty">No product sales in this period.</td></tr>@endforelse
        </tbody></table></div>
    </section>
</div>
@endsection
<?php 
