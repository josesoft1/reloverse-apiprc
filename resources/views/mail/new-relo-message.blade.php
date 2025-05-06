@component('mail::message')
# New Message

{!!nl2br($message->content)!!}

Thanks,<br>
PRC Backoffice
@endcomponent
