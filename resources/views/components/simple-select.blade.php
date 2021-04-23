@props(['options', 'placeholder' => null])

<div class="relative p-2">
    <select id="grid-state"
        class="
        appearance-none w-full text-left bg-white cursor-pointer border-gray-800
        form-input rounded-md shadow-sm block
        focus:ring-1 focus:ring-blue-600
        sm:text-sm"
        {{ $attributes }} >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $option)
            <option value="{{ $key }}">{{ $option }}</option>
        @endforeach
    </select>
</div>
