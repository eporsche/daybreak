@props(['id'])

@php
$id = $id ?? md5($attributes->wire('model'));
@endphp

<div
    x-data="{
        show: @entangle($attributes->wire('model'))
    }"
    x-show="show"
    x-cloak
    id="{{ $id }}"
    style="display: none;"
>
    {{ $slot }}
</div>
