<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="http://nutriclock.test:81/images/logo_vertical.png" class="logo" alt="">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>