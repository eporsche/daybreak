@component('mail::message')
{{ __(":employee created a new absence and it's waiting for approval!", ['employee' => $employee->name]) }}

@component('mail::button', ['url' => route('absence')])
{{ __("Open Daybreak")}}
@endcomponent

@endcomponent
