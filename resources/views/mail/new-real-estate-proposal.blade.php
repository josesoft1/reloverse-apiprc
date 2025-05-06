@component('mail::message')
# New real estate proposal

<p>{{$proposal->relocation->employee->full_name}},<br/>
We have added a new real estate proposal for your relocation job. </p>

Description: <strong>{{$proposal->description}}</strong><br/>

@if(!empty($proposal->link))
Link: <a href="{{trim($proposal->link)}}">Insertion link</a>
@endif
<br/>

<p>Please let us know if this proposal can accomodate your requirements.</p><br/>

@if(!empty($proposal->attachment_id))
@component('mail::button', ['url' => $proposal->generateAttachmentUrl()])
Announcement card
@endcomponent
@endif

Thanks,<br>
Principal relocation company
@endcomponent
