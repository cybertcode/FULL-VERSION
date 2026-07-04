@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (setting('site_logo'))
<img src="{{ url(\Illuminate\Support\Facades\Storage::url(setting('site_logo'))) }}" class="logo" alt="{{ setting('site_name', config('app.name')) }}">
@else
<span style="font-size: 20px; font-weight: 700; color: #1340A0;">{{ setting('site_name', config('app.name')) }}</span>
@endif
</a>
</td>
</tr>
