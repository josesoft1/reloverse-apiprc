@component('mail::message')
# New message

***PLESE DO NOT REPLY TO THIS EMAIL. INSTEAD GO TO PRC CUSTOMER AREA***</br>

{!!$message->content ?? ''!!}

@component('mail::button', ['url' => 'https://prc.mediacrm.it'])
Click here to reply
@endcomponent

Thanks,<br>
Principal relocation company
@endcomponent
