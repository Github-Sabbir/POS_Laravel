@extends('layouts.app')
@section('content')
<div class="pos-shell">
<section class="glass pos-products">
    <div class="pos-head"><div><div class="eyebrow">FAST CHECKOUT</div><h1>POS Terminal</h1><p>Scan a barcode, type a product name or click a product.</p></div><div class="pos-search-wrap"><input id="scan" class="scan" autocomplete="off" autofocus placeholder="⌕  Scan barcode / search product..." oninput="searchPOSLive()" onkeydown="if(event.key==='Enter'){lookupPOS(true)}"><div id="pos-suggestions" class="pos-suggestions"></div></div></div>
    <div id="product-grid" class="product-grid"></div>
</section>
<section class="glass cart-panel"><div class="cart-head"><div><h2 style="margin:0">Current Sale</h2><small class="muted">Live cart</small></div><span class="cart-badge" id="cart-count">0 items</span></div><div id="cart"></div><div class="summary">
    <div>Subtotal <b id="subtotal">৳0.00</b></div>
    <div>Discount <input id="discount" type="number" step=".01" value="0" oninput="renderCart()"></div>
    <div><span>Tax <small class="muted">(from product settings)</small></span><b id="tax-total">৳0.00</b></div>
    <div class="grand">Total <b id="grand">৳0.00</b></div>
    <div class="pos-customer-box">
        <label>Customer phone <span class="optional">optional</span><input id="customer-phone" type="tel" inputmode="tel" autocomplete="off" placeholder="01XXXXXXXXX" oninput="searchCustomerByPhone()"><div id="customer-suggestions" class="customer-suggestions"></div></label>
        <label>Customer name <span class="optional">optional</span><input id="customer-name" type="text" placeholder="Walk-in / customer name"></label>
        <div class="customer-actions"><select id="customer"><option value="">Walk-in Customer</option>@foreach($customers as $c)<option value="{{$c->id}}" data-phone="{{$c->phone}}">{{$c->name}} — {{$c->phone ?: 'No phone'}}</option>@endforeach</select><button type="button" class="secondary" onclick="quickAddCustomer()">＋ Add Customer</button></div>
        <small id="customer-status" class="muted">Phone is optional. Start typing to find an existing customer.</small>
    </div>
    <label>Payment<select id="payment"><option value="cash">Cash</option><option value="card">Card</option><option value="mobile">Mobile banking</option><option value="other">Other</option></select></label><label>Paid<input id="paid" type="number" step=".01" value="0"></label><button class="primary wide" onclick="checkoutPOS()">Pay & Complete Sale</button><button class="secondary wide" onclick="clearCart()">Clear Cart</button>
</div></section></div>
@endsection
@push('scripts')<script>
window.POS={lookup:"{{url('/pos/product')}}",customerLookup:"{{url('/pos/customer')}}",customerQuick:"{{route('pos.customer.quick')}}",checkout:"{{route('pos.checkout')}}",receiptBase:"{{url('/sales')}}",csrf:"{{csrf_token()}}",products:@json($products)};
window.addEventListener('DOMContentLoaded',()=>{renderProducts(window.POS.products||[]);renderCart();});
</script>@endpush
