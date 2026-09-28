@extends('layouts.app')
@section('content')
<div class="page-head"><div><h1>Customers</h1><p>Customer purchase and due management</p></div><a class="primary" href="{{route('customers.create')}}">＋ Add Customer</a></div>
<div class="glass table-wrap"><table><tr><th>Name</th><th>Phone</th><th>Current Due</th><th>Actions</th></tr>@forelse($customers as $x)<tr><td>{{$x->name}}</td><td>{{$x->phone}}</td><td>৳{{number_format($x->current_due,2)}}</td><td><a class="link" href="{{route('customers.edit',$x)}}">Manage</a></td></tr>@empty<tr><td colspan="4" class="empty">No customers found.</td></tr>@endforelse</table></div>{{$customers->links()}}
@endsection
