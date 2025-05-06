@component('mail::message')
# Your action required

<p>Dear customer,</br>
In order to complete the relocation / immigration job we are asking to you some documents.</p>
<p>We need these document/s:<br/>
<ul>
    <li>{{$dr->description}}</li>
</ul>

@component('mail::button', ['url' => $dr->generateUrl()])
Upload now
@endcomponent

Thanks,<br>
PRC
@endcomponent
