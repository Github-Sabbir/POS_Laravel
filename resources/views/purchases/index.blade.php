@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">INVENTORY HISTORY</div><h1>Purchases</h1><p class="muted">Received stock, supplier balances and purchase payments.</p></div><a class="primary" href="{{route('purchases.create')}}">＋ New Purchase</a></div>
<div class="glass table-wrap"><table><thead><tr><th>Reference</th><th>Supplier</th><th>Total</th><th>Paid</th><th>Due</th><th>Actions</th></tr></thead><tbody>
@forelse($purchases as $x)
<tr>
<td><span class="purchase-ref">{{ $x->reference_no }}</span></td>
<td>{{$x->supplier?->name ?? 'Cash purchase'}}</td>
<td>৳{{number_format($x->total,2)}}</td>
<td>৳{{number_format($x->paid,2)}}</td>
<td><strong class="{{(float)$x->due>0?'low':''}}">৳{{number_format($x->due,2)}}</strong></td>
<td><div class="actions"><a class="secondary" href="{{route('purchases.show',$x)}}">View</a><a class="secondary" href="{{route('purchases.edit',$x)}}">Edit</a><form method="post" action="{{route('purchases.destroy',$x)}}" onsubmit="return confirm('Delete this purchase and reverse its stock? This cannot be undone.')">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form></div></td>
</tr>
@empty<tr><td colspan="6" class="empty">No purchases found.</td></tr>@endforelse
</tbody></table></div>
{{$purchases->links()}}
@endsection
