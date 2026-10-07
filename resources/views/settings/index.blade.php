@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">SYSTEM</div><h1>Settings</h1><p class="muted">Branding, currency and thermal receipt configuration.</p></div></div>
<form class="glass form-grid settings-card" method="post" action="{{route('settings.update')}}" enctype="multipart/form-data">@csrf
<label>Shop name<input name="shop_name" value="{{$settings['shop_name']??'Retail POS'}}" required></label>
<div class="settings-profile-hint"><strong>Admin controls</strong><span>Staff profile photos and details are managed from User Management. Profile cards are view-only.</span></div><label>Currency symbol<input name="currency_symbol" value="{{$settings['currency_symbol']??'৳'}}" required></label>

<label>Thermal receipt width<select name="receipt_width"><option value="58mm" @selected(($settings['receipt_width']??'80mm')==='58mm')>58 mm</option><option value="80mm" @selected(($settings['receipt_width']??'80mm')==='80mm')>80 mm</option></select></label>
<div class="full logo-setting"><div><h3>App Logo</h3><p class="muted">Upload JPG, PNG or WebP. The logo updates dynamically in the sidebar and receipt.</p></div><div class="logo-preview">@if(!empty($settings['logo_path']))<img src="{{route('media.file', ['path' => $settings['logo_path']])}}" alt="Shop logo">@else<span>No logo</span>@endif</div><input type="file" name="logo" accept="image/jpeg,image/png,image/webp"></div>
<div class="full settings-actions"><button class="primary" type="submit">Save Settings</button>@if(!empty($settings['logo_path']))<button class="danger" type="submit" formaction="{{route('settings.logo.remove')}}" formmethod="post" onclick="return confirm('Remove the current logo?')">Remove Logo</button>@endif</div>
</form>
@if(auth()->user()->hasPermission('backup.manage'))
<section class="glass backup-card backup-card-stack"><div><h2>Database Backup & Restore</h2><p class="muted">Download a native MySQL <strong>.sql</strong> backup. If the database is deleted or damaged, an Admin can upload that SQL file here and restore the database.</p></div><div class="backup-actions"><a class="secondary" href="{{ route('settings.backup.download') }}">⬇ Download MySQL Backup (.sql)</a><form method="post" action="{{ route('settings.backup.restore') }}" enctype="multipart/form-data" onsubmit="return confirm('Restore this SQL backup? Current database data may be replaced. Continue?')"><input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="file" name="backup" accept=".sql,.txt" required><button class="danger" type="submit">↥ Restore Database</button></form></div><p class="backup-warning">⚠ Restore is Admin-only and should only use a backup generated for this Retail POS MySQL database.</p></section>
@endif
@endsection
