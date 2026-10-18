@extends('layouts.app')
@section('content')
@php($grouped = $permissions->groupBy('group'))
<div class="page-head"><div><div class="eyebrow">ACCESS CONTROL</div><h1>Roles & Permissions</h1><p class="muted">Clean, grouped access control. Give each role only the modules it needs.</p></div><a class="secondary" href="{{route('users.index')}}">← Users</a></div>
<section class="glass form-card role-create-card">
    <div class="section-head"><div><h2>Create Custom Role</h2><p class="muted">Create a role and choose its permissions.</p></div></div>
    <form method="post" action="{{route('roles.store')}}">@csrf
        <div class="role-meta-grid"><label>Role name<input name="name" placeholder="e.g. Store Manager" required></label><label>Description<input name="description" placeholder="Short description"></label></div>
        <div class="permission-toolbar"><strong>New role access</strong><button class="secondary" type="button" onclick="toggleAll(this.closest('form'),true)">Select All</button><button class="secondary" type="button" onclick="toggleAll(this.closest('form'),false)">Clear All</button></div>
        <div class="permission-groups">@foreach($grouped as $group=>$items)<div class="permission-group"><div class="permission-group-head"><span>{{ $group }}</span><button type="button" class="text-btn" onclick="toggleGroup(this,true)">All</button></div><div class="permission-checks">@foreach($items as $p)<label class="permission"><input type="checkbox" name="permissions[]" value="{{$p->id}}"><span><strong>{{$p->name}}</strong><small>{{$p->slug}}</small></span></label>@endforeach</div></div>@endforeach</div>
        <button class="primary" type="submit">Create Role</button>
    </form>
</section>
<div class="role-list">
@foreach($roles as $role)
<section class="glass form-card role-card">
    <form method="post" action="{{route('roles.update',$role)}}">@csrf @method('PUT')
        <div class="role-card-head"><div><div class="role-title"><input name="name" value="{{$role->name}}" required><span class="pill">{{$role->is_system?'System':'Custom'}}</span></div><input name="description" value="{{$role->description}}" placeholder="Role description"></div><div class="role-summary"><strong>{{ $role->permissions->count() }}</strong> permissions</div></div>
        <div class="permission-toolbar"><strong>Access Matrix</strong><button class="secondary" type="button" onclick="toggleAll(this.closest('form'),true)">Select All</button><button class="secondary" type="button" onclick="toggleAll(this.closest('form'),false)">Clear All</button></div>
        <div class="permission-groups">@foreach($grouped as $group=>$items)<div class="permission-group"><div class="permission-group-head"><span>{{ $group }}</span><button type="button" class="text-btn" onclick="toggleGroup(this,true)">All</button></div><div class="permission-checks">@foreach($items as $p)<label class="permission"><input type="checkbox" name="permissions[]" value="{{$p->id}}" @checked($role->permissions->contains($p->id))><span><strong>{{$p->name}}</strong><small>{{$p->slug}}</small></span></label>@endforeach</div></div>@endforeach</div>
        <div class="actions role-actions"><button class="primary" type="submit">Save Changes</button>@if(!$role->is_system)<button class="danger" type="submit" formaction="{{route('roles.destroy',$role)}}" formmethod="post" onclick="return confirm('Delete this custom role?')">Delete Role</button>@endif</div>
    </form>
</section>
@endforeach
</div>
@endsection
@push('scripts')
<script>
function toggleAll(form,state){form.querySelectorAll('input[name="permissions[]"]').forEach(i=>i.checked=state)}
function toggleGroup(button,state){button.closest('.permission-group').querySelectorAll('input[name="permissions[]"]').forEach(i=>i.checked=state)}
</script>
@endpush
