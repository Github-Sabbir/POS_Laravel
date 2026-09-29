<?php

?>
@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">PRODUCT IDENTIFICATION</div><h1>Barcode Manager</h1><p class="muted">Attach multiple unique barcodes to a product, choose one primary barcode, generate internal codes and print labels.</p></div></div>

<div class="barcode-tools">
<section class="glass form-card"><div class="section-head"><div><h2>Add Barcode</h2><p class="muted">USB scanner or manual entry.</p></div></div>
<form method="post" action="{{ route('barcodes.store') }}" class="form-grid single-form">@csrf
<label>Product<select name="product_id" required><option value="">Select product</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} — {{ $p->sku }}</option>@endforeach</select></label>
<label>Barcode<input name="barcode" autofocus placeholder="Scan or type barcode" required></label>
<label class="check"><input type="checkbox" name="is_primary" value="1"> Set as primary</label>
<button class="primary" type="submit">Save Barcode</button>
</form></section>
<section class="glass form-card"><div class="section-head"><div><h2>Bulk Generate</h2><p class="muted">Creates internal Code-128-compatible numeric values for your own labels.</p></div></div>
<form method="post" action="{{ route('barcodes.bulk') }}" class="form-grid single-form">@csrf
<label>Product<select name="product_id" required><option value="">Select product</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} — {{ $p->sku }}</option>@endforeach</select></label>
<label>Number of barcodes<input type="number" name="count" min="1" max="100" value="10" required></label>
<button class="primary" type="submit">Generate</button>
</form></section>
</div>

<form class="glass toolbar" method="get"><input name="q" value="{{ $search }}" placeholder="Search product, SKU or barcode..."><button class="primary" type="submit">Search</button>@if($search)<a class="secondary" href="{{route('barcodes.index')}}">Clear</a>@endif</form>

<div class="glass table-wrap"><table><thead><tr><th>Product</th><th>SKU</th><th>Barcode</th><th>Primary</th><th>Actions</th></tr></thead><tbody>
@forelse($barcodes as $b)<tr><td><b>{{ $b->product->name }}</b></td><td>{{ $b->product->sku }}</td><td><code>{{ $b->barcode }}</code></td><td>@if($b->is_primary)<span class="status ok">Primary</span>@else<span class="status">Secondary</span>@endif</td><td><div class="actions">@unless($b->is_primary)<form method="post" action="{{route('barcodes.primary',$b)}}">@csrf<button class="secondary" type="submit">Make Primary</button></form>@endunless<form method="post" action="{{route('barcodes.destroy',$b)}}">@csrf @method('DELETE')<button class="danger" type="submit" onclick="return confirm('Delete this barcode?')">Delete</button></form><button class="secondary" type="button" onclick="printBarcode('{{addslashes($b->barcode)}}','{{addslashes($b->product->name)}}')">Print</button></div></td></tr>@empty<tr><td colspan="5" class="empty">No barcodes found.</td></tr>@endforelse
</tbody></table></div>
{{ $barcodes->links() }}
@endsection
@push('scripts')
<script>
function printBarcode(code,name){const w=window.open('','barcode','width=420,height=420');if(!w)return;w.document.write('<!doctype html><html><head><title>Barcode '+code+'</title><style>@page{size:58mm 30mm;margin:0}body{font-family:Arial;text-align:center;padding:5mm}.name{font-size:12px;font-weight:bold}.code{font-family:monospace;font-size:18px;letter-spacing:2px;margin-top:8px}</style></head><body><div class="name">'+name.replace(/</g,'&lt;')+'</div><div class="code">'+code+'</div><script>setTimeout(()=>window.print(),200)<\\/script></body></html>');w.document.close();}
</script>
@endpush
<?php 
