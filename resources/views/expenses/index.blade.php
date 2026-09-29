<?php

?>
@extends('layouts.app') @section('content')<div class="page-head"><h1>Expenses</h1><a class="primary" href="{{route('expenses.create')}}">＋ Expense</a></div><div class="glass table-wrap"><table><tr><th>Date</th><th>Category</th><th>Amount</th><th>Note</th><th>Actions</th></tr>@foreach($expenses as $x)<tr><td>{{$x->expense_date->format('Y-m-d')}}</td><td>{{$x->category}}</td><td>৳{{number_format($x->amount,2)}}</td><td>{{$x->note}}</td><td><a class="link" href="{{route('expenses.edit',$x)}}">Edit</a> <form class="inline" method="post" action="{{route('expenses.destroy',$x)}}" onsubmit="return confirm('Delete this expense?')">@csrf @method('DELETE')<button class="danger-link">Delete</button></form></td></tr>@endforeach</table></div>{{$expenses->links()}}@endsection<?php 
