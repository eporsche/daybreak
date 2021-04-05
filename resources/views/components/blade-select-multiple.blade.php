<div>
    <select {{ $attributes->except('options') }} multiple="multiple">
        @foreach($attributes['options'] as $key => $option)
            <option
                selected
                value="{{ $key }}"
            >{{ $option }}</option>
        @endforeach
    </select>
</div>
