<x-mail::message>
# URGENT SOS ALERT!

Hello **{{ $contactName }}**,

This is an automated notification that **{{ $userName }}** has activated SOS mode on the SafePath application.

**Time of Incident:** {{ $time }}

@if ($location)
**Last Known Location:**
Latitude: {{ $location['latitude'] }}
Longitude: {{ $location['longitude'] }}

<br>
<a href="https://maps.google.com/?q={{ $location['latitude'] }},{{ $location['longitude'] }}" target="_blank" style="text-decoration: none; display: inline-block;">
    <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $location['latitude'] }},{{ $location['longitude'] }}&zoom=15&size=500x280&maptype=roadmap&markers=color:red%7C{{ $location['latitude'] }},{{ $location['longitude'] }}&key=YOUR_Maps_API_KEY" alt="Map of {{ $userName }}'s Last Known Location" style="max-width: 100%; height: auto; border: 1px solid #ccc;">
</a>
<br>
<x-mail::button :url="'https://maps.google.com/?q=' . $location['latitude'] . ',' . $location['longitude']">
View on Google Maps
</x-mail::button>
*(Please replace YOUR_Maps_API_KEY in the image URL if you configure static maps, or rely on the button above.)*
@else
The user's location could not be retrieved at this time.
@endif

Please try to contact **{{ $userName }}** immediately or check on their well-being.

If this is a life-threatening emergency, please contact local authorities (e.g., 112 or your local emergency number).

Thank you,
SafePath Team
</x-mail::message>