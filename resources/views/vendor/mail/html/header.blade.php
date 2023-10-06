@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="https://www.speechbuddy.com/blog/wp-content/uploads/2014/06/Reading_is_for_Super_Heroes.png" class="logo" alt="reading Logo">

@if (trim($slot) === 'Laravel')
<img src="https://www.speechbuddy.com/blog/wp-content/uploads/2014/06/Reading_is_for_Super_Heroes.png" class="logo" alt="reading Logo">
@else
{{-- {{ $slot }} --}}
@endif
</a>
</td>
</tr>
