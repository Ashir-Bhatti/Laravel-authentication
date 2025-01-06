@component('mail::message')

Hi! 
<br>
    You have a new invitation for your organization!
    Your Credentials are <br>
    Username: <strong>{{ $email }}</strong>
    <br>
    Password: <strong>{{ $password }}</strong>
<br>
Thanks,
<br>
{{ config('app.name') }}
@endcomponent