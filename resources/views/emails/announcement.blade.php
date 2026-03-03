<html>
<body>
    <p>Dear {{ $announcement->user ? $announcement->user->name : 'Client' }},</p>

    <div>
        {!! nl2br(e($announcement->body)) !!}
    </div>

    <p>--<br/>{{ config('app.name') }} Clinic</p>
</body>
</html>
