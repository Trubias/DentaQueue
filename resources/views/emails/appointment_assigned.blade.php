<html>
<body>
    <p>Dear {{ $appointment->fullname }},</p>

    @if($appointment)
        <p>Your appointment has been scheduled for {{ $appointment->scheduled_at ? $appointment->scheduled_at->format('l, F j, Y \a\t g:i A') : 'TBD' }}.</p>
        <p>Type: {{ $appointment->type }}</p>
    @else
        <p>You have an important announcement from the clinic. Please log in to your dashboard for details.</p>
    @endif

    <p>Thank you,<br/>DentaQueue Clinic</p>
</body>
</html>
