@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">OPERATING COSTS</div><h1>{{ $expense->exists?'Edit Expense':'Add Expense' }}</h1><p class="muted">Record a shop operating expense.</p></div><a class="secondary" href="{{route('expenses.index')}}">← Expenses</a></div>
<div class="glass form-card"><form method="post" action="{{$expense->exists?route('expenses.update',$expense):route('expenses.store')}}">@csrf @if($expense->exists)@method('PUT')@endif
<div class="form-grid"><label>Category<input name="category" value="{{$expense->category}}" placeholder="Rent, electricity, transport..." required></label><label>Amount<input name="amount" type="number" step=".01" min="0.01" value="{{$expense->amount}}" required></label><label>Date<input name="expense_date" type="date" value="{{$expense->expense_date?->format('Y-m-d')??now()->format('Y-m-d')}}" required></label><label>Note<input name="note" value="{{$expense->note}}" placeholder="Optional note"></label></div>
<div class="actions"><button class="primary" type="submit">{{$expense->exists?'Update Expense':'Add Expense'}}</button><a class="secondary" href="{{route('expenses.index')}}">Cancel</a></div>
</form></div>
@endsection
