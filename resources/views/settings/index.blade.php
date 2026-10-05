@extends('layouts.app')
@section('content')
<div class="page-head"><div><div class="eyebrow">SYSTEM</div><h1>Settings</h1><p class="muted">Branding, currency and thermal receipt configuration.</p></div></div>
<form class="glass form-grid settings-card" method="post" action="{{route('settings.update')}}" enctype="multipart/form-data">@csrf
<label>Shop name<input name="shop_name" value="{{$settings['shop_name']??'Retail POS'}}" required></label>
<label>Currency symbol<input name="currency_symbol" value="{{$settings['currency_symbol']??'৳'}}" required></label>
<label>Tax %<input name="tax" type="number" step=".01" min="0" max="100" value="{{$settings['tax']??0}}" required></label>
<label>Thermal receipt width<select name="receipt_width"><option value="58mm" @selected(($settings['receipt_width']??'80mm')==='58mm')>58 mm</option><option value="80mm" @selected(($settings['receipt_width']??'80mm')==='80mm')>80 mm</option></select></label>
<div class="full logo-setting"><div><h3>App Logo</h3><p class="muted">Upload JPG, PNG or WebP. The logo updates dynamically in the sidebar and receipt.</p></div><div class="logo-preview">@if(!empty($settings['logo_path']))<img src="{{route('media.file', ['path' => $settings['logo_path']])}}" alt="Shop logo">@else<span>No logo</span>@endif</div><input type="file" name="logo" accept="image/jpeg,image/png,image/webp"></div>
<div class="full settings-actions"><button class="primary" type="submit">Save Settings</button>@if(!empty($settings['logo_path']))<button class="danger" type="submit" formaction="{{route('settings.logo.remove')}}" formmethod="post" onclick="return confirm('Remove the current logo?')">Remove Logo</button>@endif</div>
</form>
@if(auth()->user()->hasPermission('backup.manage'))
<section class="glass backup-card"><div><h2>Database Backup</h2><p class="muted">Download the latest MySQL database as a native <strong>.sql</strong> backup. MySQL backups are SQL dumps rather than SQLite .db files, and can be restored in phpMyAdmin or MySQL.</p></div><a class="secondary" href="{{ route('settings.backup.download') }}">⬇ Download MySQL Backup (.sql)</a></section>
@endif
@endsection
