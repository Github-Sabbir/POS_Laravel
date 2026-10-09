@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">USER PROFILE</div><h1>{{ $isAdminView ? 'User Details' : 'My Profile' }}</h1><p class="muted">{{ $isAdminView ? 'Admin view: account details are read-only here.' : 'Your account details. Profile changes are controlled by Admin.' }}</p></div>@if(!$isAdminView)<form method="post" action="{{ route('logout') }}">@csrf<button class="danger" type="submit">Logout</button></form>@else<a class="secondary" href="{{ route('users.edit',$user) }}">Edit User</a>@endif</div>
<div class="profile-layout">
<section class="glass profile-card">
<div class="profile-avatar-wrap">@if($user->image)<img class="profile-avatar-image" src="{{ route('media.file',['path'=>$user->image]) }}?v={{ $user->updated_at?->timestamp ?? time() }}" alt="{{ $user->name }}">@else<div class="profile-avatar-fallback">{{ strtoupper(substr($user->name,0,1)) }}</div>@endif</div>
<h2>{{ $user->name }}</h2><span class="pill">{{ optional($user->roleModel)->name ?? ucfirst($user->role) }}</span>
<div class="profile-details"><div><span>Name</span><strong>{{ $user->name }}</strong></div><div><span>Phone</span><strong>{{ $user->phone ?: 'Not added' }}</strong></div><div><span>WhatsApp</span><strong>{{ $user->whatsapp ?: 'Not added' }}</strong></div><div><span>Email</span><strong>{{ $user->email }}</strong></div><div class="full"><span>Address</span><strong>{{ $user->address ?: 'Not added' }}</strong></div></div>
</section>
<section class="glass profile-info-panel"><div class="section-head"><div><h2>{{ $isAdminView ? 'Account Preview' : 'Logged-in Account' }}</h2><p class="muted">{{ $isAdminView ? 'Admin can review this staff profile here.' : 'View only. Ask an Admin to change account details.' }}</p></div><span class="status {{ $user->active ? 'ok' : 'off' }}">{{ $user->active ? 'Active' : 'Inactive' }}</span></div><div class="profile-readonly-note"><strong>Profile changes are Admin-controlled.</strong><span>Name, phone, WhatsApp, email, address and photo can be changed only from User Management by an Admin.</span></div>@if($isAdminView && auth()->user()->role === 'admin')<a class="primary" href="{{ route('users.edit',$user) }}">Edit this user</a>@endif</section>
</div>
@endsection
