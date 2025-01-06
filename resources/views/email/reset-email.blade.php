<x-mail::message>
# Email Reset Link

Click the button below to reset your email:

<x-mail::button :url="$url">
Change Email
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>