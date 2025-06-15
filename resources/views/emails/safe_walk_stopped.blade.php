<x-mail::message>
# Safe Walk Ended: {{ $userName }}

Hello {{ $emergencyContactName }},

This is an automated notification to inform you that **{{ $userName }}** has manually stopped their Safe Walk session.

Details:
- **Ended At:** {{ $endTime }}

They have indicated they have stopped their journey. You may wish to confirm their status if you haven't heard from them.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>