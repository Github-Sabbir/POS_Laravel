@extends('layouts.app')
@section('content')
<div class="page-head"><div><h1>Purchase {{ $purchase->reference_no }}</h1><p>Purchase details and stock received.</p></div><a class="secondary" href="{{ route('purchases.index') }}">Back</a></div>
<div class="glass table-wrap"><p><b>Supplier:</b> {{ $purchase->supplier?->name ?? 'Walk-in supplier' }}</p><p><b>Total:</b> ৳{{ number_format($purchase->total,2) }} &nbsp; <b>Paid:</b> ৳{{ number_format($purchase->paid,2) }} &nbsp; <b>Due:</b> ৳{{ number_format($purchase->due,2) }}</p>
<table><thead><tr><th>Product</th><th>Qty</th><th>Unit Cost</th><th>Total</th></tr></thead><tbody>
@foreach($purchase->items as $item)<tr><td>{{ $item->product?->name }}</td><td>{{ $item->quantity }}</td><td>৳{{ number_format($item->unit_cost,2) }}</td><td>৳{{ number_format($item->line_total,2) }}</td></tr>@endforeach
</tbody></table></div>
@endsection
