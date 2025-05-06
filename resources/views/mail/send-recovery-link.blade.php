@component('mail::message')
# Recupero reset

Dear {{$user->name}},
please click on the following button in order to reset your password.

@component('mail::button', ['url' => $recovery_link])
Reset
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
