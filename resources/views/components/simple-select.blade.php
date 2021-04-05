@props(['options', 'placeholder' => null])

<div class="relative">
    <select class="block appearance-none w-full bg-gray-200
        border border-gray-200 text-gray-700 py-3 px-4 pr-8
        rounded leading-tight focus:outline-none focus:bg-white
         focus:border-gray-500" id="grid-state"
        {{ $attributes }}
         >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $option)
            <option value="{{ $key }}">{{ $option }}</option>
        @endforeach
    </select>
</div>
