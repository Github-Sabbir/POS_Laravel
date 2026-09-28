@extends('layouts.app') @section('content')
<div class="page-head"><h1>Customers</h1><a class="primary" href="{{route('customers.create')}}">＋ Customer</a></div>
<div class="glass table-wrap"><table><tr><th>Name</th><th>Phone</th><th>Current Due</th><th>Actions</th></tr>@foreach($customers as $x)<tr><td>{{$x->name}}</td><td>{{$x->phone}}</td><td>৳{{number_format($x->current_due,2)}}</td><td><a class="link" href="{{route('customers.edit',$x)}}">Edit</a> <form class="inline" method="post" action="{{route('customers.destroy',$x)}}" onsubmit="return confirm('Delete this customer?')">@csrf @method('DELETE')<button class="danger-link">Delete</button></form></td></tr>@endforeach</table></div>{{$customers->links()}}
@endsection
