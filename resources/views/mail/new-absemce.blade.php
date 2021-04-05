@component('mail::message')
{{ __('A new absence from :employee is waiting for your approval!', ['employee' => absence->employee->name]) }}

@component('mail::button', ['url' => route('/')])
Open Daybreak
@endcomponent

@endcomponent
