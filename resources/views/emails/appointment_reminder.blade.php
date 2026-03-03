<html>
<body>
    <p>Hi {{ $appointment->fullname }},</p>

    <p>This is a reminder for your upcoming appointment scheduled on {{ $appointment->scheduled_at ? $appointment->scheduled_at->format('l, F j, Y \a\t g:i A') : 'TBD' }}.</p>

    <p>Type: {{ $appointment->type }}</p>

    <p>If you need to reschedule, reply to this email or contact the clinic.</p>

    <p>Thanks,<br/>DentaQueue Clinic</p>
</body>
</html>
