@extends('layouts.app') @section('content')
<div class="page-head"><div><h1>Products</h1><p>SKU, stock, images and prices</p></div><a class="primary" href="{{route('products.create')}}">＋ Add Product</a></div>
<form class="toolbar glass"><input name="q" value="{{request('q')}}" placeholder="Search name, SKU or barcode"><button>Search</button></form>
<div class="glass table-wrap"><table><thead><tr><th>Image</th><th>Product</th><th>SKU</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead><tbody>
@foreach($products as $p)<tr><td>@if($p->image)<img class="thumb" src="{{Storage::url($p->image)}}">@else<span class="thumb noimg">📦</span>@endif</td><td><b>{{$p->name}}</b><small>{{$p->category?->name}}</small></td><td>{{$p->sku}}</td><td>৳{{number_format($p->selling_price,2)}}</td><td class="{{($p->current_stock<=$p->minimum_stock)?'low':''}}">{{$p->current_stock}}</td><td><a class="link" href="{{route('products.edit',$p)}}">Edit</a><form class="inline" method="post" action="{{route('products.destroy',$p)}}">@csrf @method('DELETE')<button class="danger-link">Archive</button></form></td></tr>@endforeach</tbody></table></div>{{$products->links()}}
@endsection
