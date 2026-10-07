function toggleTheme(){document.body.classList.toggle('light');localStorage.setItem('pos-theme',document.body.classList.contains('light')?'light':'dark')}
if(localStorage.getItem('pos-theme')==='light')document.body.classList.add('light');
function esc(v){return String(v??'').replace(/[&<>\"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#039;'}[m]))}
function previewImage(input){if(!input.files?.[0])return;const r=new FileReader();r.onload=e=>document.getElementById('preview').innerHTML='<img src="'+e.target.result+'">';r.readAsDataURL(input.files[0])}
let cart=[];
function addPOS(p){let x=cart.find(i=>i.id===p.id);if(x)x.qty++;else cart.push({id:p.id,name:p.name,sku:p.sku,price:Number(p.selling_price),tax_rate:Number(p.tax_rate||0),stock:Number(p.current_stock),image:p.image,qty:1});renderCart()}
async function lookupPOS(submit=false){let q=document.getElementById('scan').value.trim();if(!q)return;try{let r=await fetch(window.POS.lookup+'/'+encodeURIComponent(q));let data=await r.json();let p=data?.product;if(p){addPOS(p);document.getElementById('scan').value='';hidePOSSuggestions();renderProducts(window.POS.products||[]);return}if(submit)alert('Product not found')}catch(e){if(submit)alert('Lookup failed')}}
function changePOS(id,d){let x=cart.find(i=>i.id===id);if(!x)return;x.qty+=d;if(x.qty<=0)cart=cart.filter(i=>i.id!==id);renderCart()}
function clearCart(){cart=[];renderCart()}
function renderProducts(list){let el=document.getElementById('product-grid');if(!el)return;list=list||[];el.innerHTML=list.map(p=>`<div class="product-card" data-product-id="${Number(p.id)}"><div class="picon">${esc((p.name||'P').trim().charAt(0).toUpperCase())}</div><b>${esc(p.name)}</b><small>${esc(p.sku)} · Stock ${Number(p.current_stock)}</small><span class="price">৳${Number(p.selling_price).toFixed(2)}</span></div>`).join('')||'<div class="empty-cart">No matching products.</div>';el.querySelectorAll('.product-card').forEach((card,i)=>card.addEventListener('click',()=>{addPOS(list[i]);hidePOSSuggestions()}))}
function hidePOSSuggestions(){const el=document.getElementById('pos-suggestions');if(el){el.classList.remove('show');el.innerHTML=''}}
function showPOSSuggestions(list){const el=document.getElementById('pos-suggestions');if(!el)return;if(!list?.length){hidePOSSuggestions();return}el.innerHTML=list.map((p,i)=>`<button type="button" class="pos-suggestion" data-index="${i}"><span class="picon">${esc((p.name||'P').trim().charAt(0).toUpperCase())}</span><span><strong>${esc(p.name)}</strong><small>${esc(p.sku)} · Stock ${Number(p.current_stock)}</small></span><b>৳${Number(p.selling_price).toFixed(2)}</b></button>`).join('');el.classList.add('show');el.querySelectorAll('.pos-suggestion').forEach((b,i)=>b.addEventListener('click',()=>{addPOS(list[i]);document.getElementById('scan').value='';hidePOSSuggestions()}))}
let posSearchTimer;async function searchPOSLive(){const q=document.getElementById('scan')?.value.trim();if(!q){renderProducts(window.POS.products||[]);hidePOSSuggestions();return}clearTimeout(posSearchTimer);posSearchTimer=setTimeout(async()=>{try{const r=await fetch(window.POS.lookup+'/'+encodeURIComponent(q));const data=await r.json();const list=data?.suggestions||[];renderProducts(list);showPOSSuggestions(list)}catch(e){hidePOSSuggestions()}},140)}
function renderCart(){let el=document.getElementById('cart');if(!el)return;let sub=cart.reduce((s,x)=>s+x.price*x.qty,0);document.getElementById('subtotal').textContent='৳'+sub.toFixed(2);let dis=Number(document.getElementById('discount').value||0),tax=cart.reduce((sum,x)=>sum+(x.price*x.qty*(Number(x.tax_rate||0)/100)),0),total=Math.max(0,sub-dis+tax);document.getElementById('tax-total').textContent='৳'+tax.toFixed(2);document.getElementById('grand').textContent='৳'+total.toFixed(2);document.getElementById('cart-count').textContent=cart.length+' '+(cart.length===1?'item':'items');el.innerHTML=cart.length?cart.map(x=>`<div class="cart-line"><div><b>${x.name}</b><small>${x.sku}</small></div><div><div>৳${(x.price*x.qty).toFixed(2)}</div><div class="qty"><button onclick="changePOS(${x.id},-1)">−</button>${x.qty}<button onclick="changePOS(${x.id},1)">+</button></div></div></div>`).join(''):'<div class="empty-cart">Your cart is empty.<br><small>Scan a barcode or click a product to start.</small></div>'}
let customerTimer;
function selectCustomer(c){
  const select=document.getElementById('customer');
  let opt=[...select.options].find(o=>String(o.value)===String(c.id));
  if(!opt){opt=new Option(`${c.name} — ${c.phone||'No phone'}`,c.id);opt.dataset.phone=c.phone||'';select.add(opt);}
  select.value=c.id;
  document.getElementById('customer-phone').value=c.phone||'';
  document.getElementById('customer-name').value=c.name||'';
  document.getElementById('customer-status').textContent='Customer selected: '+c.name;
  document.getElementById('customer-suggestions').innerHTML='';
}
async function searchCustomerByPhone(){
  const input=document.getElementById('customer-phone'), q=input.value.trim(), box=document.getElementById('customer-suggestions');
  document.getElementById('customer-status').textContent=q?'Searching customers…':'Phone is optional. Start typing to find an existing customer.';
  clearTimeout(customerTimer);
  if(!q){box.innerHTML='';return;}
  customerTimer=setTimeout(async()=>{try{const r=await fetch(window.POS.customerLookup+'/'+encodeURIComponent(q));const d=await r.json();const list=d.customers||[];box.innerHTML=list.length?list.map(c=>`<button type="button" class="customer-suggestion"><strong>${esc(c.phone||'')}</strong><span>${esc(c.name)}</span></button>`).join(''):'<div class="customer-suggestion-empty">No saved customer found.</div>';box.querySelectorAll('button').forEach((b,i)=>b.onclick=()=>selectCustomer(list[i]));}catch(e){box.innerHTML='';}},180);
}
async function quickAddCustomer(){
  const name=document.getElementById('customer-name').value.trim(), phone=document.getElementById('customer-phone').value.trim();
  if(!name){alert('Enter customer name first. Phone number is optional.');return;}
  const r=await fetch(window.POS.customerQuick,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.POS.csrf,'Accept':'application/json'},body:JSON.stringify({name,phone})});
  const d=await r.json(); if(!r.ok){alert(d.message||'Customer could not be saved.');return;}
  selectCustomer(d.customer); alert('Customer saved successfully.');
}
async function checkoutPOS(){
  if(!cart.length)return alert('Cart is empty');
  let sub=cart.reduce((s,x)=>s+x.price*x.qty,0),dis=Number(document.getElementById('discount').value||0),tax=cart.reduce((sum,x)=>sum+(x.price*x.qty*(Number(x.tax_rate||0)/100)),0),total=Math.max(0,sub-dis+tax),paid=Number(document.getElementById('paid').value||0);
  if(paid<total&&!confirm('Payment is less than total. Continue as due?'))return;
  let body={customer_id:document.getElementById('customer').value||null,items:cart.map(x=>({product_id:x.id,quantity:x.qty,price:x.price})),discount:dis,paid,payment_method:document.getElementById('payment').value};
  let r=await fetch(window.POS.checkout,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.POS.csrf,'Accept':'application/json'},body:JSON.stringify(body)});
  let data=await r.json();
  if(!r.ok)return alert(data.message||'Checkout failed');
  clearCart();document.getElementById('paid').value=0;
  const w=window.open(window.POS.receiptBase+'/'+data.sale_id+'/receipt','receipt','width=420,height=720');
  if(!w)alert('Sale completed. Please allow pop-ups so the receipt can print automatically.');
}


// Login-only orbit interaction.
(function(){
  const page=document.querySelector('.login-animated-page');
  if(!page)return;
  const rings=[...page.querySelectorAll('.login-orbit')];
  const colors=['#a7ff3f','#62e7ff','#ff5fa2','#ffe16b','#b58cff','#6fffd1','#ff8a5c'];
  const randomize=()=>rings.forEach((ring,i)=>{const c=colors[Math.floor(Math.random()*colors.length)];ring.style.setProperty('--orbit-color',c);ring.style.setProperty('--orbit-glow',c+'66');ring.style.borderColor=c;});
  const reset=()=>rings.forEach(r=>{r.style.borderColor='rgba(255,255,255,.86)';r.style.removeProperty('--orbit-color');r.style.removeProperty('--orbit-glow')});
  page.querySelectorAll('.animated-field input').forEach(input=>{
    input.addEventListener('input',()=>{randomize();page.classList.add('login-input-active');});
    input.addEventListener('focus',()=>{randomize();page.classList.add('login-input-active');});
    input.addEventListener('blur',()=>page.classList.remove('login-input-active'));
  });
  page.addEventListener('mouseenter',()=>{randomize();page.classList.add('login-hover-active')});
  page.addEventListener('mouseleave',()=>{page.classList.remove('login-hover-active','login-input-active');reset()});
})();

function previewUserImage(input){
  const file=input?.files?.[0], box=document.getElementById('user-image-box'), img=document.getElementById('user-image-preview');
  if(!file || !img) return;
  const r=new FileReader(); r.onload=e=>{img.src=e.target.result;if(box)box.hidden=false}; r.readAsDataURL(file);
}


// v18: remove unexpected top-level overlays on application pages.
(function(){
  const clean=()=>{
    if(document.body.classList.contains('login-animated-page')) return;
    [...document.body.children].forEach(el=>{
      if(el.matches('aside.sidebar, main, script, style, link')) return;
      el.remove();
    });
  };
  if(document.body) clean();
  new MutationObserver(clean).observe(document.documentElement,{childList:true,subtree:false});
})();
