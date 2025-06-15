<x-mail::message>
# Safe Walk Alert: Timer Expired for {{ $userName }}

Hello {{ $emergencyContactName }},

This is an automated alert from SafePath. The Safe Walk session for **{{ $userName }}** has ended because the set duration of **{{ $duration }} minutes** has expired, and they have not manually stopped the session.

Session Details:
- **Started At:** {{ $startTime }}
- **Originally Set For:** {{ $duration }} minutes
- **Expired At:** {{ $expiredTime }}
- **Starting Location (approximate):**
    Latitude: {{ $latitude }}
    Longitude: {{ $longitude }}

<x-mail::button :url="$mapsUrl" color="error">
View Last Known Start Location
</x-mail::button>

Please try to contact **{{ $userName }}** immediately to ensure their safety. If you cannot reach them or have concerns, please consider taking appropriate action.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>