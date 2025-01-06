<x-mail::message>
# Password Reset Link

Click the button below to reset your passowrd:

<x-mail::button :url="$url">
Forgot Password
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>