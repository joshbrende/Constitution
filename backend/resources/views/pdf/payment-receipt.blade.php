<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: helvetica, sans-serif; font-size: 11pt; color: #111; }
        h1 { font-size: 18pt; color: #15803d; margin-bottom: 4px; }
        h2 { font-size: 12pt; color: #444; margin-top: 0; font-weight: normal; }
        .meta { margin: 16px 0; border: 1px solid #ccc; padding: 12px; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 0; vertical-align: top; }
        .meta td.label { width: 38%; font-weight: bold; color: #333; }
        .amount { font-size: 16pt; font-weight: bold; color: #15803d; }
        .offices { margin-top: 14px; }
        .office { margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed #ddd; }
        .instructions { margin-top: 14px; font-size: 10pt; line-height: 1.45; color: #333; }
        .footer { margin-top: 18px; font-size: 9pt; color: #666; }
    </style>
</head>
<body>
    <h1>ZANU PF Academy</h1>
    <h2>Certificate Payment Receipt</h2>

    <div class="meta">
        <table>
            <tr>
                <td class="label">Receipt number</td>
                <td>{{ $application->receipt_number }}</td>
            </tr>
            <tr>
                <td class="label">Payment reference</td>
                <td><strong>{{ $application->payment_reference_code }}</strong></td>
            </tr>
            <tr>
                <td class="label">Applicant</td>
                <td>{{ trim(($user->name ?? '').' '.($user->surname ?? '')) }}</td>
            </tr>
            <tr>
                <td class="label">National ID</td>
                <td>{{ $nationalIdMasked }}</td>
            </tr>
            @if ($provinceName)
            <tr>
                <td class="label">Province</td>
                <td>{{ $provinceName }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Course</td>
                <td>{{ $course->title ?? 'Academy course' }}</td>
            </tr>
            <tr>
                <td class="label">Exam passed</td>
                <td>{{ optional($application->exam_passed_at)->format('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Amount due</td>
                <td class="amount">{{ $application->fee_currency }} {{ number_format((float) $application->fee_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>{{ $application->status->label() }}</td>
            </tr>
        </table>
    </div>

    <div class="instructions">
        <strong>Payment instructions</strong><br>
        {{ $instructions }}
    </div>

    @if (count($offices) > 0)
    <div class="offices">
        <strong>Designated payment offices</strong>
        @foreach ($offices as $office)
        <div class="office">
            <div><strong>{{ $office['name'] }}</strong></div>
            <div>{{ $office['address'] }}</div>
            @if (! empty($office['phone']))
            <div>Phone: {{ $office['phone'] }}</div>
            @endif
            @if (! empty($office['hours']))
            <div>Hours: {{ $office['hours'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Present this receipt when paying. Quote the payment reference code above.
        Verification: {{ $verifyUrl }}
    </div>
</body>
</html>
