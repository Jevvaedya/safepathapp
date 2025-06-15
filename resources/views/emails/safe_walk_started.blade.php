<x-mail::message>
# Safe Walk Started: {{ $userName }}

Hello {{ $emergencyContactName }},

This is an automated notification to inform you that **{{ $userName }}** has started a Safe Walk session using the SafePath application.

Session details:
- **Start Time:** {{ $startTime }}
- **Estimated Duration:** {{ $duration }} minutes
- **Starting Location (approximate):**
    Latitude: {{ $latitude }}
    Longitude: {{ $longitude }}

<x-mail::button :url="$mapsUrl" color="success">
View on Map (Google Maps)
</x-mail::button>

Please be aware. We recommend you stay in touch with {{ $userName }} during their journey.

If you have any concerns or cannot reach them after the estimated duration, please take appropriate action.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>