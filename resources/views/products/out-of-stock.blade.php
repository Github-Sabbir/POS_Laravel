@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">INVENTORY ALERT</div><h1>Out of Stock</h1><p class="muted">Products with zero or negative stock. Everyone can view this list.</p></div><span class="cart-badge">{{ $products->total() }} products</span></div>
<div class="glass table-wrap"><table><thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Stock</th><th>Minimum</th><th>Status</th>@if(auth()->user()->role==='admin')<th>Admin Action</th>@endif</tr></thead><tbody>
@forelse($products as $p)
<tr><td><strong>{{ $p->name }}</strong></td><td>{{ $p->sku }}</td><td>{{ $p->category?->name ?? '—' }}</td><td class="low"><strong>0</strong></td><td>{{ $p->minimum_stock }}</td><td><span class="status off">Out of Stock</span></td>@if(auth()->user()->role==='admin')<td><form method="post" action="{{ route('out-of-stock.archive',$p) }}" onsubmit="return confirm('Archive this out-of-stock product?')">@csrf<button class="danger" type="submit">Delete / Archive</button></form></td>@endif</tr>
@empty<tr><td colspan="{{ auth()->user()->role==='admin' ? 7 : 6 }}" class="empty">No out-of-stock products.</td></tr>@endforelse
</tbody></table></div>
{{ $products->links() }}
@endsection
