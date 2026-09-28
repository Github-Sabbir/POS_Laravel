@extends('layouts.app')
@section('content')
<h1>{{ $supplier->exists?'Manage Supplier':'Add Supplier' }}</h1>
<form class="glass form-grid" method="post" action="{{$supplier->exists?route('suppliers.update',$supplier):route('suppliers.store')}}">@csrf @if($supplier->exists)@method('PUT')@endif
<label>Name<input name="name" value="{{$supplier->name}}" required></label><label>Company<input name="company" value="{{$supplier->company}}"></label><label>Phone<input name="phone" value="{{$supplier->phone}}"></label><label>Email<input name="email" value="{{$supplier->email}}"></label><label>Opening Due<input name="opening_due" type="number" step=".01" value="{{$supplier->opening_due}}"></label><label>Status<select name="status"><option value="active" @selected($supplier->status==='active')>Active</option><option value="inactive" @selected($supplier->status==='inactive')>Inactive</option></select></label><label class="full">Address<textarea name="address">{{$supplier->address}}</textarea></label><button class="primary">Save Supplier</button></form>
@if($supplier->exists)
<div class="glass" style="margin-top:20px;padding:20px"><h2>Current Due: ৳{{number_format($supplier->current_due,2)}}</h2><form class="form-grid" method="post" action="{{route('suppliers.payment',$supplier)}}">@csrf<label>Payment Amount<input name="amount" type="number" step=".01" min=".01" max="{{$supplier->current_due}}" required></label><label>Payment Method<select name="payment_method"><option value="cash">Cash</option><option value="card">Card</option><option value="mobile">Mobile Banking</option><option value="other">Other</option></select></label><label class="full">Note<textarea name="note"></textarea></label><button class="primary">Record Supplier Payment</button></form></div>
@endif
@endsection
