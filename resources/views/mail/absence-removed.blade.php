@component('mail::message')
{{ __("Your absence has been removed. Please check you absences for correctness.") }}

@component('mail::button', ['url' => route('absence')])
{{ __("Open Daybreak")}}
@endcomponent

@endcomponent
