@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">OPERATING COSTS</div><h1>Expenses</h1><p class="muted">Track shop operating expenses separately from inventory purchases.</p></div><a class="primary" href="{{route('expenses.create')}}">＋ Add Expense</a></div>
<div class="glass expense-summary"><div><span>Total shown</span><strong>৳{{number_format($expenses->sum('amount'),2)}}</strong></div><div><span>Entries</span><strong>{{$expenses->total()}}</strong></div><div><span>Current page</span><strong>{{$expenses->count()}}</strong></div></div>
<div class="glass table-wrap"><table><thead><tr><th>Date</th><th>Category</th><th>Amount</th><th>Note</th><th>Actions</th></tr></thead><tbody>@forelse($expenses as $x)<tr><td><strong>{{$x->expense_date->format('d M Y')}}</strong></td><td><span class="pill">{{$x->category}}</span></td><td><strong>৳{{number_format($x->amount,2)}}</strong></td><td>{{$x->note ?: '—'}}</td><td class="actions"><a class="secondary" href="{{route('expenses.edit',$x)}}">Edit</a><form method="post" action="{{route('expenses.destroy',$x)}}" onsubmit="return confirm('Delete this expense?')">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form></td></tr>@empty<tr><td colspan="5" class="empty">No expenses found.</td></tr>@endforelse</tbody></table></div>
{{$expenses->links()}}
@endsection
