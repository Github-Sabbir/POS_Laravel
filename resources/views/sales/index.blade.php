@extends('layouts.app')
@section('content')
<div class="page-head">
    <div><div class="eyebrow">SALES HISTORY</div><h1>Sales History</h1><p class="muted">Admin and Manager can review completed sales, customer, cashier, time and sold items.</p></div>
    <a class="primary" href="{{ route('pos') }}">＋ Open POS</a>
</div>
<form class="glass toolbar sales-history-filter" method="get">
    <input name="q" value="{{ $search }}" placeholder="Search invoice, customer or cashier...">
    <label>From <input type="date" name="from" value="{{ $from }}"></label>
    <label>To <input type="date" name="to" value="{{ $to }}"></label>
    <button class="primary" type="submit">Search</button>
    @if($search || $from || $to)<a class="secondary" href="{{ route('sales.history') }}">Clear</a>@endif
</form>
<div class="glass table-wrap sales-history-table">
<table>
<thead><tr><th>Invoice</th><th>Date & Time</th><th>Customer</th><th>Cashier</th><th>What was sold</th><th>Payment</th><th>Total</th><th>Due</th><th>Receipt</th></tr></thead>
<tbody>
@forelse($sales as $sale)
<tr>
<td><strong class="purchase-ref">{{ $sale->invoice_no }}</strong></td>
<td>{{ $sale->created_at->format('d M Y, h:i A') }}</td>
<td>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
<td>{{ $sale->user?->name ?? '—' }}</td>
<td><div class="sale-items-list">@foreach($sale->items as $item)<span>{{ $item->product?->name ?? 'Deleted product' }} × {{ rtrim(rtrim(number_format((float)$item->quantity,3,'.',''), '0'), '.') }}</span>@endforeach</div></td>
<td>{{ ucfirst($sale->payment_method) }}</td>
<td><strong>৳{{ number_format($sale->total,2) }}</strong></td>
<td>৳{{ number_format($sale->due,2) }}</td>
<td><a class="secondary" href="{{ route('sales.receipt',$sale) }}" target="_blank">Receipt</a></td>
</tr>
@empty
<tr><td colspan="9" class="empty">No sales found for the selected filters.</td></tr>
@endforelse
</tbody>
</table>
</div>
{{ $sales->links() }}
@endsection
