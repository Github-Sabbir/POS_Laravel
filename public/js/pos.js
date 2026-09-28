function toggleTheme(){document.body.classList.toggle('light');localStorage.setItem('pos-theme',document.body.classList.contains('light')?'light':'dark')}
if(localStorage.getItem('pos-theme')==='light')document.body.classList.add('light');
function esc(v){return String(v??'').replace(/[&<>\"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#039;'}[m]))}
function previewImage(input){if(!input.files?.[0])return;const r=new FileReader();r.onload=e=>document.getElementById('preview').innerHTML='<img src="'+e.target.result+'">';r.readAsDataURL(input.files[0])}
let cart=[];
function addPOS(p){let x=cart.find(i=>i.id===p.id);if(x)x.qty++;else cart.push({id:p.id,name:p.name,sku:p.sku,price:Number(p.selling_price),stock:Number(p.current_stock),image:p.image,qty:1});renderCart()}
async function lookupPOS(){let q=document.getElementById('scan').value.trim();if(!q)return;try{let r=await fetch(window.POS.lookup+'/'+encodeURIComponent(q));let p=await r.json();if(p?.id){addPOS(p);document.getElementById('scan').value=''}else alert('Product not found')}catch(e){alert('Lookup failed')}}
function changePOS(id,d){let x=cart.find(i=>i.id===id);if(!x)return;x.qty+=d;if(x.qty<=0)cart=cart.filter(i=>i.id!==id);renderCart()}
function clearCart(){cart=[];renderCart()}
function renderProducts(list){let el=document.getElementById('product-grid');if(!el)return;el.innerHTML=list.map(p=>`<div class="product-card" data-product-id="${Number(p.id)}"><div class="picon">${esc((p.name||'P').trim().charAt(0).toUpperCase())}</div><b>${esc(p.name)}</b><small>${esc(p.sku)} · Stock ${Number(p.current_stock)}</small><span class="price">৳${Number(p.selling_price).toFixed(2)}</span></div>`).join('');el.querySelectorAll('.product-card').forEach((card,i)=>card.addEventListener('click',()=>addPOS(list[i])))}
function renderCart(){let el=document.getElementById('cart');if(!el)return;let sub=cart.reduce((s,x)=>s+x.price*x.qty,0);document.getElementById('subtotal').textContent='৳'+sub.toFixed(2);let dis=Number(document.getElementById('discount').value||0),tax=Number(document.getElementById('tax').value||0),total=Math.max(0,sub-dis+tax);document.getElementById('grand').textContent='৳'+total.toFixed(2);document.getElementById('cart-count').textContent=cart.length+' '+(cart.length===1?'item':'items');el.innerHTML=cart.length?cart.map(x=>`<div class="cart-line"><div><b>${x.name}</b><small>${x.sku}</small></div><div><div>৳${(x.price*x.qty).toFixed(2)}</div><div class="qty"><button onclick="changePOS(${x.id},-1)">−</button>${x.qty}<button onclick="changePOS(${x.id},1)">+</button></div></div></div>`).join(''):'<div class="empty-cart">Your cart is empty.<br><small>Scan a barcode or click a product to start.</small></div>'}
async function checkoutPOS(){if(!cart.length)return alert('Cart is empty');let sub=cart.reduce((s,x)=>s+x.price*x.qty,0),dis=Number(document.getElementById('discount').value||0),tax=Number(document.getElementById('tax').value||0),total=Math.max(0,sub-dis+tax),paid=Number(document.getElementById('paid').value||0);if(paid<total && !confirm('Payment is less than total. Continue as due?'))return;let body={customer_id:document.getElementById('customer').value||null,items:cart.map(x=>({product_id:x.id,quantity:x.qty,price:x.price})),discount:dis,tax:tax,paid,payment_method:document.getElementById('payment').value};let r=await fetch(window.POS.checkout,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.POS.csrf,'Accept':'application/json'},body:JSON.stringify(body)});let data=await r.json();if(!r.ok)return alert(data.message||'Checkout failed');alert(`Sale completed\\nInvoice: ${data.invoice}\\nTotal: ৳${data.total.toFixed(2)}\\nChange: ৳${data.change.toFixed(2)}\\nDue: ৳${data.due.toFixed(2)}`);clearCart();document.getElementById('paid').value=0}

let searchTimer=null;
function showSuggestions(list){
  const box=document.getElementById('search-suggestions'); if(!box)return;
  if(!list.length){box.innerHTML='';box.hidden=true;return;}
  box.innerHTML=list.map((p,i)=>`<button type="button" class="suggestion-item" data-index="${i}"><span><b>${esc(p.name)}</b><small>${esc(p.sku)} · Stock ${Number(p.current_stock)}</small></span><strong>৳${Number(p.selling_price).toFixed(2)}</strong></button>`).join('');
  box.hidden=false;box.querySelectorAll('.suggestion-item').forEach((b,i)=>b.addEventListener('click',()=>{addPOS(list[i]);document.getElementById('scan').value='';box.hidden=true;box.innerHTML='';document.getElementById('scan').focus()}));
}
async function liveSearchPOS(){
  const input=document.getElementById('scan'); if(!input)return;
  const q=input.value.trim(); clearTimeout(searchTimer);
  if(!q){showSuggestions([]);return;}
  searchTimer=setTimeout(async()=>{try{const r=await fetch(window.POS.search+'?q='+encodeURIComponent(q),{headers:{'Accept':'application/json'}});if(r.ok)showSuggestions(await r.json())}catch(e){}},180);
}
document.addEventListener('click',e=>{const wrap=document.querySelector('.scan-wrap');const box=document.getElementById('search-suggestions');if(box&&wrap&&!wrap.contains(e.target)){box.hidden=true}});
