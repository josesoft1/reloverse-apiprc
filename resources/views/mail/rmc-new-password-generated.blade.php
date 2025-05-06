<x-mail::message>
# Your new PRC password:

Dear {{$rmc->name}}, <br>
we have just generated a new password for your web application access. Here the details: <br>

Link to the web application: <a href="https://rmc.mediacrm.it">https://rmc.mediacrm.it</a>

Email: {{$rmc->email}}<br>
Password: {{$new_password}}<br>
<br>
Please never share this password and archive this email in a safe place.<br>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
