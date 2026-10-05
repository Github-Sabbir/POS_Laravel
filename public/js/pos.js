function toggleTheme(){document.body.classList.toggle('light');localStorage.setItem('pos-theme',document.body.classList.contains('light')?'light':'dark')}
if(localStorage.getItem('pos-theme')==='light')document.body.classList.add('light');
function esc(v){return String(v??'').replace(/[&<>\"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#039;'}[m]))}
function previewImage(input){if(!input.files?.[0])return;const r=new FileReader();r.onload=e=>document.getElementById('preview').innerHTML='<img src="'+e.target.result+'">';r.readAsDataURL(input.files[0])}
let cart=[];
function addPOS(p){let x=cart.find(i=>i.id===p.id);if(x)x.qty++;else cart.push({id:p.id,name:p.name,sku:p.sku,price:Number(p.selling_price),stock:Number(p.current_stock),image:p.image,qty:1});renderCart()}
async function lookupPOS(submit=false){let q=document.getElementById('scan').value.trim();if(!q)return;try{let r=await fetch(window.POS.lookup+'/'+encodeURIComponent(q));let data=await r.json();let p=data?.product;if(p){addPOS(p);document.getElementById('scan').value='';hidePOSSuggestions();renderProducts(window.POS.products||[]);return}if(submit)alert('Product not found')}catch(e){if(submit)alert('Lookup failed')}}
function changePOS(id,d){let x=cart.find(i=>i.id===id);if(!x)return;x.qty+=d;if(x.qty<=0)cart=cart.filter(i=>i.id!==id);renderCart()}
function clearCart(){cart=[];renderCart()}
function renderProducts(list){let el=document.getElementById('product-grid');if(!el)return;list=list||[];el.innerHTML=list.map(p=>`<div class="product-card" data-product-id="${Number(p.id)}"><div class="picon">${esc((p.name||'P').trim().charAt(0).toUpperCase())}</div><b>${esc(p.name)}</b><small>${esc(p.sku)} · Stock ${Number(p.current_stock)}</small><span class="price">৳${Number(p.selling_price).toFixed(2)}</span></div>`).join('')||'<div class="empty-cart">No matching products.</div>';el.querySelectorAll('.product-card').forEach((card,i)=>card.addEventListener('click',()=>{addPOS(list[i]);hidePOSSuggestions()}))}
function hidePOSSuggestions(){const el=document.getElementById('pos-suggestions');if(el){el.classList.remove('show');el.innerHTML=''}}
function showPOSSuggestions(list){const el=document.getElementById('pos-suggestions');if(!el)return;if(!list?.length){hidePOSSuggestions();return}el.innerHTML=list.map((p,i)=>`<button type="button" class="pos-suggestion" data-index="${i}"><span class="picon">${esc((p.name||'P').trim().charAt(0).toUpperCase())}</span><span><strong>${esc(p.name)}</strong><small>${esc(p.sku)} · Stock ${Number(p.current_stock)}</small></span><b>৳${Number(p.selling_price).toFixed(2)}</b></button>`).join('');el.classList.add('show');el.querySelectorAll('.pos-suggestion').forEach((b,i)=>b.addEventListener('click',()=>{addPOS(list[i]);document.getElementById('scan').value='';hidePOSSuggestions()}))}
let posSearchTimer;async function searchPOSLive(){const q=document.getElementById('scan')?.value.trim();if(!q){renderProducts(window.POS.products||[]);hidePOSSuggestions();return}clearTimeout(posSearchTimer);posSearchTimer=setTimeout(async()=>{try{const r=await fetch(window.POS.lookup+'/'+encodeURIComponent(q));const data=await r.json();const list=data?.suggestions||[];renderProducts(list);showPOSSuggestions(list)}catch(e){hidePOSSuggestions()}},140)}
function renderCart(){let el=document.getElementById('cart');if(!el)return;let sub=cart.reduce((s,x)=>s+x.price*x.qty,0);document.getElementById('subtotal').textContent='৳'+sub.toFixed(2);let dis=Number(document.getElementById('discount').value||0),tax=Number(document.getElementById('tax').value||0),total=Math.max(0,sub-dis+tax);document.getElementById('grand').textContent='৳'+total.toFixed(2);document.getElementById('cart-count').textContent=cart.length+' '+(cart.length===1?'item':'items');el.innerHTML=cart.length?cart.map(x=>`<div class="cart-line"><div><b>${x.name}</b><small>${x.sku}</small></div><div><div>৳${(x.price*x.qty).toFixed(2)}</div><div class="qty"><button onclick="changePOS(${x.id},-1)">−</button>${x.qty}<button onclick="changePOS(${x.id},1)">+</button></div></div></div>`).join(''):'<div class="empty-cart">Your cart is empty.<br><small>Scan a barcode or click a product to start.</small></div>'}
async function checkoutPOS(){
  if(!cart.length)return alert('Cart is empty');
  let sub=cart.reduce((s,x)=>s+x.price*x.qty,0),dis=Number(document.getElementById('discount').value||0),tax=Number(document.getElementById('tax').value||0),total=Math.max(0,sub-dis+tax),paid=Number(document.getElementById('paid').value||0);
  if(paid<total&&!confirm('Payment is less than total. Continue as due?'))return;
  let body={customer_id:document.getElementById('customer').value||null,items:cart.map(x=>({product_id:x.id,quantity:x.qty,price:x.price})),discount:dis,tax:tax,paid,payment_method:document.getElementById('payment').value};
  let r=await fetch(window.POS.checkout,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.POS.csrf,'Accept':'application/json'},body:JSON.stringify(body)});
  let data=await r.json();
  if(!r.ok)return alert(data.message||'Checkout failed');
  clearCart();document.getElementById('paid').value=0;
  const w=window.open(window.POS.receiptBase+'/'+data.sale_id+'/receipt','receipt','width=420,height=720');
  if(!w)alert('Sale completed. Please allow pop-ups so the receipt can print automatically.');
}


// Stable desktop glass cursor: it never falls back to the native arrow while the pointer is inside the app.
(function(){
  if (!window.matchMedia || !window.matchMedia('(pointer:fine)').matches) return;
  const dot=document.querySelector('.cursor-dot'), ring=document.querySelector('.cursor-ring');
  if(!dot || !ring) return;
  let x=-100,y=-100,rx=-100,ry=-100,inside=false;
  document.body.classList.add('cursor-ready');
  window.addEventListener('mousemove',e=>{
    inside=true; x=e.clientX; y=e.clientY;
    dot.style.left=x+'px'; dot.style.top=y+'px';
    const el=document.elementFromPoint(x,y);
    document.body.classList.toggle('cursor-hover',!!el?.closest('a,button,.product-card,.quick a,.secondary,.primary,.danger,.nav-btn,.link,.danger-link,.icon-remove-row'));
  });
  function tick(){rx+=(x-rx)*.22;ry+=(y-ry)*.22;ring.style.left=rx+'px';ring.style.top=ry+'px';requestAnimationFrame(tick)}
  tick();
  document.addEventListener('mousedown',()=>document.body.classList.add('cursor-down'));
  document.addEventListener('click',()=>{document.body.classList.add('cursor-click');setTimeout(()=>document.body.classList.remove('cursor-click'),180)});
  document.addEventListener('mouseup',()=>document.body.classList.remove('cursor-down'));
  document.addEventListener('mouseleave',()=>{inside=false;document.body.classList.remove('cursor-hover','cursor-down')});
  document.addEventListener('mouseenter',()=>{inside=true});
  window.addEventListener('blur',()=>document.body.classList.remove('cursor-hover','cursor-down'));
  window.addEventListener('mouseleave',()=>document.body.classList.remove('cursor-hover','cursor-down'));
})();
