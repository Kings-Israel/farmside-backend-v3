<x-mail::message>
# Hello

A new contact form submission has been received with the following details:

<x-mail::panel>
- **Name:** {{ $name }}
- **Email:** {{ $email }}
- **Phone Number:** {{ $phone_number }}
</x-mail::panel>
<br>
**Message:**
<x-mail::panel>
 {{ $message }}
</x-mail::panel>

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
