@props(['type' => 'success', 'message'])

@php
    $bg = $type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
@endphp

<div class="{{ $bg }} px-4 py-2 rounded mb-4">
    {{ $message }}
</div>
