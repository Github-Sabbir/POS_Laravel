@extends('layouts.app')
@section('content')
<div class="page-head purchase-page-head">
    <div><div class="eyebrow">INVENTORY IN</div><h1>New Purchase</h1><p class="muted">Receive stock, record supplier details and track the amount paid.</p></div>
    <a class="secondary" href="{{ route('purchases.index') }}">← Purchases</a>
</div>

<form class="purchase-form" method="post" action="{{ route('purchases.store') }}">
@csrf
<section class="glass purchase-card">
    <div class="purchase-card-head"><div><h2>Purchase details</h2><p class="muted">Choose a supplier and add the products you received.</p></div><span class="status ok">Stock receiving</span></div>
    <div class="purchase-fields">
        <label>Supplier
            <select name="supplier_id">
                <option value="">No supplier / Cash purchase</option>
                @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}{{ $s->company ? ' — '.$s->company : '' }}</option>@endforeach
            </select>
        </label>
    </div>
</section>

<section class="glass purchase-card">
    <div class="purchase-card-head"><div><h2>Purchase items</h2><p class="muted">Quantity and unit cost can be changed before receiving.</p></div><button class="secondary" type="button" id="addPurchaseRow">＋ Add item</button></div>
    <div class="purchase-table-wrap">
        <div class="purchase-row purchase-row-head"><span>Product</span><span>Quantity</span><span>Unit cost</span><span></span></div>
        <div id="purchaseRows">
            <div class="purchase-row">
                <select name="items[0][product_id]" class="purchase-product" required>
                    <option value="">Select product</option>
                    @foreach($products as $p)<option value="{{ $p->id }}" data-cost="{{ $p->purchase_price }}">{{ $p->name }} — {{ $p->sku }}</option>@endforeach
                </select>
                <input name="items[0][quantity]" type="number" step=".001" min=".001" value="1" required>
                <input name="items[0][unit_cost]" class="purchase-cost" type="number" step=".01" min="0" value="0" required>
                <button class="icon-remove-row" type="button" title="Remove item" aria-label="Remove item">×</button>
            </div>
        </div>
    </div>
    <div class="purchase-note">Tip: select a product to automatically load its current purchase cost. You can still edit it.</div>
    <div class="purchase-totals"><div class="purchase-total-box"><span>Subtotal</span><strong id="purchaseSubtotal">৳0.00</strong></div><div class="purchase-total-box"><span>Paid</span><strong id="purchasePaidPreview">৳0.00</strong></div><div class="purchase-total-box due"><span>Supplier Due</span><strong id="purchaseDuePreview">৳0.00</strong></div></div>
</section>

<section class="glass purchase-card purchase-payment-card">
    <div><h2>Payment</h2><p class="muted">Enter the amount paid now. Any remaining amount becomes supplier due.</p></div>
    <div class="purchase-payment-row">
        <label>Paid now<input name="paid" type="number" step=".01" min="0" value="0" required></label>
        <div class="purchase-submit"><button class="primary" type="submit">Receive Purchase</button></div>
    </div>
</section>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const rows = document.getElementById('purchaseRows');
    const add = document.getElementById('addPurchaseRow');
    if (!rows || !add) return;
    let index = 1;
    const productOptions = @json($products->map(fn($p) => ['id'=>$p->id,'name'=>$p->name,'sku'=>$p->sku,'cost'=>$p->purchase_price])->values());

    function optionHtml(){
        return '<option value="">Select product</option>' + productOptions.map(p => `<option value="${p.id}" data-cost="${p.cost}">${escapeHtml(p.name)} — ${escapeHtml(p.sku)}</option>`).join('');
    }
    function escapeHtml(v){ return String(v ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
    function bind(row){
        const select = row.querySelector('.purchase-product');
        const cost = row.querySelector('.purchase-cost');
        const remove = row.querySelector('.icon-remove-row');
        select?.addEventListener('change', () => { const selected = select.options[select.selectedIndex]; if (selected?.dataset.cost && (!cost.value || cost.value === '0')) cost.value = selected.dataset.cost; updateTotals(); });
        row.querySelectorAll('input').forEach(input=>input.addEventListener('input',updateTotals));
        remove?.addEventListener('click', () => {
            if (rows.children.length === 1) { select.value=''; cost.value='0'; return; }
            row.remove(); updateTotals();
        });
    }
    function updateTotals(){ let total=0; rows.querySelectorAll('.purchase-row').forEach(row=>{const q=Number(row.querySelector('[name*="[quantity]"]')?.value||0);const c=Number(row.querySelector('.purchase-cost')?.value||0);total+=q*c;}); const paid=Number(document.querySelector('[name=paid]')?.value||0); document.getElementById('purchaseSubtotal').textContent='৳'+total.toFixed(2); document.getElementById('purchasePaidPreview').textContent='৳'+paid.toFixed(2); document.getElementById('purchaseDuePreview').textContent='৳'+Math.max(0,total-paid).toFixed(2); }
    bind(rows.firstElementChild); updateTotals();
    document.querySelector('[name=paid]')?.addEventListener('input',updateTotals);
    add.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className='purchase-row';
        row.innerHTML = `<select name="items[${index}][product_id]" class="purchase-product" required>${optionHtml()}</select><input name="items[${index}][quantity]" type="number" step=".001" min=".001" value="1" required><input name="items[${index}][unit_cost]" class="purchase-cost" type="number" step=".01" min="0" value="0" required><button class="icon-remove-row" type="button" title="Remove item" aria-label="Remove item">×</button>`;
        rows.appendChild(row); bind(row); index++; updateTotals();
    });
})();
</script>
@endpush
