@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">ACCESS CONTROL</div><h1>User Management</h1><p class="muted">Create staff accounts and assign roles.</p></div><a class="primary" href="{{route('users.create')}}">+ Add User</a></div>
<div class="glass table-card"><div class="table-wrap"><table><thead><tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($users as $user)<tr><td><strong>{{$user->name}}</strong></td><td>{{$user->email}}</td><td><span class="pill">{{optional($user->roleModel)->name ?? ucfirst($user->role)}}</span></td><td><span class="status {{$user->active?'ok':'off'}}">{{$user->active?'Active':'Inactive'}}</span></td><td class="actions"><a class="secondary" href="{{route('users.edit',$user)}}">Edit</a><form method="post" action="{{route('users.destroy',$user)}}" onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE')<button class="danger">Delete</button></form></td></tr>@empty<tr><td colspan="5" class="empty">No users found.</td></tr>@endforelse
</tbody></table></div></div>
<div class="page-head compact"><div><div class="eyebrow">ROLES</div><h2>Roles & Permissions</h2></div><a class="secondary" href="{{route('roles.index')}}">Manage Roles</a></div>
<div class="role-grid">@foreach($roles as $role)<div class="glass mini-card"><div class="role-title"><strong>{{$role->name}}</strong><span class="pill">{{$role->users()->count()}} users</span></div><p class="muted">{{$role->description ?: 'Access profile'}}</p><div class="chips">@foreach($role->permissions->take(5) as $p)<span>{{$p->name}}</span>@endforeach @if($role->permissions->count()>5)<span>+{{$role->permissions->count()-5}} more</span>@endif</div></div>@endforeach</div>
@endsection
