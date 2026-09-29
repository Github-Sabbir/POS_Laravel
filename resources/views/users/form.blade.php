<?php

?>
@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">USER ACCOUNT</div><h1>{{$user->exists?'Edit User':'Add User'}}</h1></div><a class="secondary" href="{{route('users.index')}}">← Back</a></div>
<div class="glass form-card"><form method="post" action="{{$user->exists?route('users.update',$user):route('users.store')}}">@csrf @if($user->exists) @method('PUT') @endif
<div class="form-grid"><label>Name<input name="name" value="{{old('name',$user->name)}}" required></label><label>Email<input type="email" name="email" value="{{old('email',$user->email)}}" required></label><label>Role<select name="role_id" required>@foreach($roles as $role)<option value="{{$role->id}}" @selected(old('role_id',$user->role_id)==$role->id)>{{$role->name}}</option>@endforeach</select></label><label>Password <small>{{$user->exists?'(leave blank to keep current)':''}}</small><input type="password" name="password" {{$user->exists?'':'required'}}></label><label>Confirm Password<input type="password" name="password_confirmation" {{$user->exists?'':'required'}}></label><label class="check"><input type="checkbox" name="active" value="1" @checked(old('active',$user->exists?$user->active:true))> Active account</label></div><button class="primary" type="submit">{{$user->exists?'Update User':'Create User'}}</button></form></div>
@endsection
<?php 
