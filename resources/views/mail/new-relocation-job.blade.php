@component('mail::message')
# New relocation JOB

Dear {{$relocation->employee->full_name}},<br/>

<p>We hereby to confirm that a new relocation / immigration job (from: {{$relocation->from}} to {{$relocation->to}}) has just been created. </p>
<p>Our JOB number is <strong>{{$relocation->job}}</strong>. With this number we can better identify your practice so please make sure to include this number in any conversation.</p>

<p>In the next hours you will receive other communications about this JOB so please in order to accelerate the practice check your email frequently.<br/>
But not worries, if you miss any important communication where we require one action from your part one of our counsellor will call you.</p>

<p>Feel free to contact us at info@principalrelocationcompany.it for any issue or concern.</p>


Thanks,<br>
{{ config('app.name') }}<br/>
This email has been generated from an automatic system. DO NOT REPLAY TO THIS EMAIL.
@endcomponent
