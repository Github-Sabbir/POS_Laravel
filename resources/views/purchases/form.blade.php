@extends('layouts.app')
@section('content')
@php($editing = $purchase->exists)
<div class="page-head purchase-page-head">
    <div><div class="eyebrow">{{ $editing ? 'EDIT PURCHASE' : 'INVENTORY IN' }}</div><h1>{{ $editing ? 'Edit Purchase' : 'New Purchase' }}</h1><p class="muted">{{ $editing ? 'Edit the receipt. Stock is safely reversed and re-applied in one transaction.' : 'Receive stock, record supplier details and track the amount paid.' }}</p></div>
    <a class="secondary" href="{{ route('purchases.index') }}">← Purchases</a>
</div>
<form class="purchase-form" method="post" action="{{ $editing ? route('purchases.update',$purchase) : route('purchases.store') }}">
@csrf
@if($editing) @method('PUT') @endif
<section class="glass purchase-card">
    <div class="purchase-card-head"><div><h2>Purchase details</h2><p class="muted">Choose a supplier and add the products you received.</p></div><span class="status ok">{{ $editing ? $purchase->reference_no : 'Stock receiving' }}</span></div>
    <div class="purchase-fields">
        <label>Supplier
            <select name="supplier_id">
                <option value="">No supplier / Cash purchase</option>
                @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id',$purchase->supplier_id)==$s->id)>{{ $s->name }}{{ $s->company ? ' — '.$s->company : '' }}</option>@endforeach
            </select>
        </label>
    </div>
</section>
<section class="glass purchase-card">
    <div class="purchase-card-head"><div><h2>Purchase items</h2><p class="muted">Quantity and unit cost can be changed before {{ $editing ? 'saving' : 'receiving' }}.</p></div><button class="secondary" type="button" id="addPurchaseRow">＋ Add item</button></div>
    <div class="purchase-table-wrap">
        <div class="purchase-row purchase-row-head"><span>Product</span><span>Quantity</span><span>Unit cost</span><span></span></div>
        <div id="purchaseRows">
        @php($formItems = old('items'))
        @if($formItems)
            @foreach($formItems as $i=>$it)
            <div class="purchase-row">
                <select name="items[{{ $i }}][product_id]" class="purchase-product" required><option value="">Select product</option>{!! $products->map(fn($p) => '<option value="'.$p->id.'" data-cost="'.$p->purchase_price.'" '.((string)$it['product_id']===(string)$p->id?'selected':'').'>'.e($p->name).' — '.e($p->sku).'</option>')->implode('') !!}</select>
                <input name="items[{{ $i }}][quantity]" type="number" step=".001" min=".001" value="{{ $it['quantity'] }}" required>
                <input name="items[{{ $i }}][unit_cost]" class="purchase-cost" type="number" step=".01" min="0" value="{{ $it['unit_cost'] }}" required>
                <button class="icon-remove-row" type="button" title="Remove item" aria-label="Remove item">×</button>
            </div>
            @endforeach
        @elseif($editing && $purchase->items->count())
            @foreach($purchase->items as $i=>$it)
            <div class="purchase-row">
                <select name="items[{{ $i }}][product_id]" class="purchase-product" required><option value="">Select product</option>{!! $products->map(fn($p) => '<option value="'.$p->id.'" data-cost="'.$p->purchase_price.'" '.($it->product_id===$p->id?'selected':'').'>'.e($p->name).' — '.e($p->sku).'</option>')->implode('') !!}</select>
                <input name="items[{{ $i }}][quantity]" type="number" step=".001" min=".001" value="{{ $it->quantity }}" required>
                <input name="items[{{ $i }}][unit_cost]" class="purchase-cost" type="number" step=".01" min="0" value="{{ $it->unit_cost }}" required>
                <button class="icon-remove-row" type="button" title="Remove item" aria-label="Remove item">×</button>
            </div>
            @endforeach
        @else
            <div class="purchase-row">
                <select name="items[0][product_id]" class="purchase-product" required><option value="">Select product</option>{!! $products->map(fn($p) => '<option value="'.$p->id.'" data-cost="'.$p->purchase_price.'">'.e($p->name).' — '.e($p->sku).'</option>')->implode('') !!}</select>
                <input name="items[0][quantity]" type="number" step=".001" min=".001" value="1" required>
                <input name="items[0][unit_cost]" class="purchase-cost" type="number" step=".01" min="0" value="0" required>
                <button class="icon-remove-row" type="button" title="Remove item" aria-label="Remove item">×</button>
            </div>
        @endif
        </div>
    </div>
    <div class="purchase-note">Tip: select a product to automatically load its current purchase cost. You can still edit it.</div>
    <div class="purchase-totals"><div class="purchase-total-box"><span>Subtotal</span><strong id="purchaseSubtotal">৳0.00</strong></div><div class="purchase-total-box"><span>Paid</span><strong id="purchasePaidPreview">৳0.00</strong></div><div class="purchase-total-box due"><span>Supplier Due</span><strong id="purchaseDuePreview">৳0.00</strong></div></div>
</section>
<section class="glass purchase-card purchase-payment-card">
    <div><h2>Payment</h2><p class="muted">Enter the amount paid now. Any remaining amount becomes supplier due.</p></div>
    <div class="purchase-payment-row"><label>Paid now<input name="paid" type="number" step=".01" min="0" value="{{ old('paid',$purchase->exists?$purchase->paid:0) }}" required></label><div class="purchase-submit"><button class="primary" type="submit">{{ $editing ? 'Save Purchase' : 'Receive Purchase' }}</button></div></div>
</section>
</form>
@endsection
@push('scripts')
<script>
(() => {
 const rows=document.getElementById('purchaseRows'),add=document.getElementById('addPurchaseRow'); if(!rows||!add)return;
 let index=rows.querySelectorAll('.purchase-row').length;
 const productOptions=@json($products->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'sku'=>$p->sku,'cost'=>$p->purchase_price])->values());
 const esc=v=>String(v??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
 const optionHtml=()=>'<option value="">Select product</option>'+productOptions.map(p=>`<option value="${p.id}" data-cost="${p.cost}">${esc(p.name)} — ${esc(p.sku)}</option>`).join('');
 function bind(row){const sel=row.querySelector('.purchase-product'),cost=row.querySelector('.purchase-cost');sel?.addEventListener('change',()=>{const o=sel.options[sel.selectedIndex];if(o?.dataset.cost&&(!cost.value||cost.value==='0'))cost.value=o.dataset.cost;update()});row.querySelectorAll('input').forEach(i=>i.addEventListener('input',update));row.querySelector('.icon-remove-row')?.addEventListener('click',()=>{if(rows.children.length===1){sel.value='';cost.value='0'}else row.remove();update()})}
 function update(){let total=0;rows.querySelectorAll('.purchase-row').forEach(r=>total+=Number(r.querySelector('[name*="[quantity]"]')?.value||0)*Number(r.querySelector('.purchase-cost')?.value||0));const paid=Number(document.querySelector('[name=paid]')?.value||0);document.getElementById('purchaseSubtotal').textContent='৳'+total.toFixed(2);document.getElementById('purchasePaidPreview').textContent='৳'+paid.toFixed(2);document.getElementById('purchaseDuePreview').textContent='৳'+Math.max(0,total-paid).toFixed(2)}
 rows.querySelectorAll('.purchase-row').forEach(bind);document.querySelector('[name=paid]')?.addEventListener('input',update);update();
 add.addEventListener('click',()=>{const row=document.createElement('div');row.className='purchase-row';row.innerHTML=`<select name="items[${index}][product_id]" class="purchase-product" required>${optionHtml()}</select><input name="items[${index}][quantity]" type="number" step=".001" min=".001" value="1" required><input name="items[${index}][unit_cost]" class="purchase-cost" type="number" step=".01" min="0" value="0" required><button class="icon-remove-row" type="button" title="Remove item">×</button>`;rows.appendChild(row);bind(row);index++;update()});
})();
</script>
@endpush
