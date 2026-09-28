@extends('layouts.app') @section('content')
<div class="page-head"><h1>Suppliers</h1><a class="primary" href="{{route('suppliers.create')}}">＋ Supplier</a></div>
<div class="glass table-wrap"><table><tr><th>Name</th><th>Company</th><th>Phone</th><th>Current Due</th><th>Actions</th></tr>@foreach($suppliers as $x)<tr><td>{{$x->name}}</td><td>{{$x->company}}</td><td>{{$x->phone}}</td><td>৳{{number_format($x->current_due,2)}}</td><td><a class="link" href="{{route('suppliers.edit',$x)}}">Edit</a> <form class="inline" method="post" action="{{route('suppliers.destroy',$x)}}" onsubmit="return confirm('Delete this supplier?')">@csrf @method('DELETE')<button class="danger-link">Delete</button></form></td></tr>@endforeach</table></div>{{$suppliers->links()}}
@endsection
