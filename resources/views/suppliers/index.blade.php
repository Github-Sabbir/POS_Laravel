@extends('layouts.app')
@section('content')
<div class="page-head"><div><h1>Suppliers</h1><p>Purchase history and supplier dues</p></div><a class="primary" href="{{route('suppliers.create')}}">＋ Add Supplier</a></div>
<div class="glass table-wrap"><table><tr><th>Name</th><th>Company</th><th>Phone</th><th>Current Due</th><th>Actions</th></tr>@forelse($suppliers as $x)<tr><td>{{$x->name}}</td><td>{{$x->company}}</td><td>{{$x->phone}}</td><td>৳{{number_format($x->current_due,2)}}</td><td><a class="link" href="{{route('suppliers.edit',$x)}}">Manage</a></td></tr>@empty<tr><td colspan="5" class="empty">No suppliers found.</td></tr>@endforelse</table></div>{{$suppliers->links()}}
@endsection
