@extends('layouts.admin')

@section('content')
    <h1>Reports - {{ $year }}</h1>
    <h2>Monthly totals</h2>
    <table width="100%" border="1" cellspacing="0" cellpadding="4">
        <thead><tr><th>Month</th><th>Total</th></tr></thead>
        <tbody>
            @for($m=1;$m<=12;$m++)
                <tr>
                    <td>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</td>
                    <td>{{ $monthly->has($m) ? $monthly[$m]->total : 0 }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <h2>Totals by type</h2>
    <table width="100%" border="1" cellspacing="0" cellpadding="4">
        <thead><tr><th>Type</th><th>Total</th></tr></thead>
        <tbody>
        @foreach($byType as $t)
            <tr><td>{{ $t->type }}</td><td>{{ $t->total }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <h2>No-shows</h2>
    <table width="100%" border="1" cellspacing="0" cellpadding="4">
        <thead><tr><th>Month</th><th>No-shows</th></tr></thead>
        <tbody>
        @for($m=1;$m<=12;$m++)
            <tr>
                <td>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</td>
                <td>{{ $noShows->has($m) ? $noShows[$m]->total : 0 }}</td>
            </tr>
        @endfor
        </tbody>
    </table>

    <h2>Appointments ({{ $appointments->count() }})</h2>
    <table width="100%" border="1" cellspacing="0" cellpadding="4">
        <thead><tr><th>When</th><th>Full name</th><th>Type</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($appointments as $a)
            <tr>
                <td>{{ date('M j, Y g:i A', strtotime($a->scheduled_at)) }}</td>
                <td>{{ $a->fullname }}</td>
                <td>{{ $a->type }}</td>
                <td>{{ $a->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Announcements ({{ $announcements->count() }})</h2>
    <table width="100%" border="1" cellspacing="0" cellpadding="4">
        <thead><tr><th>When</th><th>Title</th><th>Body</th><th>Related Appointment</th></tr></thead>
        <tbody>
        @foreach($announcements as $n)
            <tr>
                <td>{{ $n->created_at }}</td>
                <td>{{ $n->title }}</td>
                <td>{{ $n->body }}</td>
                <td>{{ $n->appointment_id }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection
