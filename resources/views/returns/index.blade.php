@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">SALES AFTERCARE</div><h1>Sales Returns</h1><p class="muted">Find an invoice, select the returned item and restore stock safely.</p></div></div>
<section class="glass return-lookup-card">
    <div class="return-search"><label>Invoice Number<input id="return-invoice" placeholder="e.g. INV-20260924-ABCD" autocomplete="off"></label><button class="primary" type="button" id="find-return">Find Invoice</button></div>
    <div id="return-message" class="return-message"></div>
</section>
<section class="glass card" id="return-sale" style="display:none;margin-top:18px">
    <div class="section-head"><div><h2 id="return-customer">Customer</h2><p id="return-meta" class="muted"></p></div><span class="pill" id="return-invoice-badge"></span></div>
    <form method="post" action="{{ route('returns.store') }}" id="return-form" class="return-form">@csrf
        <label>Item<select name="sale_item_id" id="return-item" required></select></label>
        <label>Quantity<input type="number" name="quantity" id="return-qty" step=".001" min=".001" required></label>
        <label>Refund Amount<input type="number" name="refund_amount" id="return-refund" step=".01" min="0" value="0"></label>
        <label>Refund Method<select name="refund_method"><option value="cash">Cash</option><option value="card">Card</option><option value="mobile">Mobile banking</option><option value="other">Other</option></select></label>
        <label class="full">Reason<textarea name="reason" placeholder="Optional reason"></textarea></label>
        <div class="return-item-info full" id="return-item-info">Select an item to see remaining quantity.</div>
        <div class="actions full"><button class="primary" type="submit">Process Sales Return</button><button class="secondary" type="button" onclick="document.getElementById('return-sale').style.display='none'">Cancel</button></div>
    </form>
</section>
<section class="glass card" style="margin-top:18px"><div class="section-head"><div><h2>Recent Returns</h2><p class="muted">Latest processed customer returns.</p></div></div><div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Product</th><th>Qty</th><th>Refund</th><th>Method</th><th>Date</th></tr></thead><tbody>@forelse($returns as $r)<tr><td><strong>{{ $r->sale->invoice_no }}</strong></td><td>{{ $r->product->name }}</td><td>{{ number_format($r->quantity,3) }}</td><td>৳{{ number_format($r->refund_amount,2) }}</td><td>{{ ucfirst($r->refund_method ?? '—') }}</td><td>{{ $r->created_at->format('d M Y, h:i A') }}</td></tr>@empty<tr><td colspan="6" class="empty">No returns yet.</td></tr>@endforelse</tbody></table></div>{{ $returns->links() }}</section>
@endsection
@push('scripts')
<script>
const returnLookupUrl = @json(route('returns.lookup','__INVOICE__'));
const invoiceInput=document.getElementById('return-invoice');
const findBtn=document.getElementById('find-return');
const message=document.getElementById('return-message');
const saleBox=document.getElementById('return-sale');
const itemSelect=document.getElementById('return-item');
const qtyInput=document.getElementById('return-qty');
const refundInput=document.getElementById('return-refund');
const itemInfo=document.getElementById('return-item-info');
let returnItems=[];
function selectedReturnItem(){return returnItems.find(x=>String(x.id)===String(itemSelect.value));}
function updateReturnInfo(){const item=selectedReturnItem();if(!item){itemInfo.textContent='Select an item to see remaining quantity.';return;}qtyInput.max=item.remaining;qtyInput.value=Math.min(1,item.remaining);refundInput.max=(item.remaining*item.unit_price).toFixed(2);refundInput.value=(qtyInput.value*item.unit_price).toFixed(2);itemInfo.innerHTML=`<strong>${item.name}</strong> · Sold ${item.quantity} · Already returned ${item.returned} · <b>Remaining ${item.remaining}</b> · Unit ৳${Number(item.unit_price).toFixed(2)}`;}
itemSelect.addEventListener('change',updateReturnInfo);qtyInput.addEventListener('input',()=>{const item=selectedReturnItem();if(item)refundInput.value=(Math.min(Number(qtyInput.value)||0,item.remaining)*item.unit_price).toFixed(2);});
findBtn.addEventListener('click',async()=>{const invoice=invoiceInput.value.trim();if(!invoice)return;message.textContent='Searching...';message.className='return-message';try{const res=await fetch(returnLookupUrl.replace('__INVOICE__',encodeURIComponent(invoice)));const data=await res.json();if(!res.ok)throw new Error(data.message||'Invoice not found.');returnItems=data.items;itemSelect.innerHTML=returnItems.map(x=>`<option value="${x.id}">${x.name} · Remaining ${x.remaining} · ৳${Number(x.unit_price).toFixed(2)}</option>`).join('');document.getElementById('return-customer').textContent=data.customer;document.getElementById('return-meta').textContent=`Paid ৳${Number(data.paid).toFixed(2)} · ${returnItems.length} returnable item(s)`;document.getElementById('return-invoice-badge').textContent=data.invoice;saleBox.style.display=returnItems.length?'block':'none';message.textContent=returnItems.length?'Invoice loaded. Select the returned item.':'This invoice has no remaining returnable items.';message.className='return-message success';updateReturnInfo();}catch(e){saleBox.style.display='none';message.textContent=e.message;message.className='return-message error';}});
invoiceInput.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();findBtn.click();}});
</script>
@endpush
