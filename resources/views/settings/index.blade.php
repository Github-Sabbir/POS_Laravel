<?php

?>
@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">SYSTEM</div><h1>Settings</h1><p class="muted">Store and receipt configuration.</p></div></div>
<form class="glass form-grid" method="post" action="{{route('settings.update')}}">@csrf
<label>Shop name<input name="shop_name" value="{{$settings['shop_name']??'Retail POS'}}"></label>
<label>Currency symbol<input name="currency_symbol" value="{{$settings['currency_symbol']??'৳'}}"></label>
<label>Tax %<input name="tax" type="number" step=".01" value="{{$settings['tax']??0}}"></label>
<label>Thermal receipt width<select name="receipt_width"><option value="58mm" @selected(($settings['receipt_width']??'80mm')==='58mm')>58 mm</option><option value="80mm" @selected(($settings['receipt_width']??'80mm')==='80mm')>80 mm</option></select></label>
<div class="full"><button class="primary">Save Settings</button></div>
</form>
@endsection<?php 
