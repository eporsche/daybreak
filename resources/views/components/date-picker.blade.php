<div class="relative">
    <input
    type="text"
    class="block appearance-none w-full
        border border-gray-800 text-gray-700 py-3 px-4 pr-8
        rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-600"
        x-data
        x-init="new Pikaday({
            field: $refs.input,
            format: 'DD.MM.YYYY'
        })"
        x-ref="input"
        onchange="this.dispatchEvent(new InputEvent('input'))"
        {{ $attributes }}
    >
    <div class="absolute top-0 right-0 px-3 py-2">
        <svg class="h-6 w-6 text-gray-400"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
</div>
