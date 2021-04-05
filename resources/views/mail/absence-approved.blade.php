@component('mail::message')
{{ __("The absence from :employee has been approved.", ['employee' => $employee->name]) }}

@component('mail::button', ['url' => route('absence')])
{{ __("Open Daybreak")}}
@endcomponent

@endcomponent
