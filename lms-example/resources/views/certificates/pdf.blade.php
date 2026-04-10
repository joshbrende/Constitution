<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Certificate – {{ $certificate->course->title ?? 'Course' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Serif, serif;
            padding: 20px;
            background: #fff;
        }
        .page {
            width: 100%;
            min-height: 170mm;
            background: #fff;
            border: 3px solid #1a4d2e;
            padding: 0;
        }
        .inner {
            margin: 14px;
            border: 1px solid #1a4d2e;
            padding: 0;
            min-height: 160mm;
        }
        .banner {
            background: #1a4d2e;
            color: #fff;
            text-align: center;
            padding: 14px 20px;
            margin: 20px 24px 0;
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .content-wrap {
            padding: 24px 24px 50px 24px;
        }
        .cert-body {
            text-align: center;
            margin: 0 auto 16px;
        }
        .cert-body .intro {
            font-size: 11pt;
            color: #333;
            margin-bottom: 6px;
        }
        .cert-body .name {
            font-size: 20pt;
            font-weight: bold;
            color: #5c4033;
            margin: 12px 0 8px;
            line-height: 1.3;
        }
        .cert-body .course-title {
            font-size: 14pt;
            color: #1a4d2e;
            font-weight: bold;
            margin: 10px 0 6px;
        }
        .cert-body .statement {
            font-size: 11pt;
            color: #333;
            margin-top: 8px;
        }
        .org {
            font-size: 11pt;
            color: #1a4d2e;
            font-weight: bold;
            margin-top: 16px;
        }
        .foot {
            margin-top: 20px;
            font-size: 9pt;
            color: #555;
        }
        .foot .number { margin-bottom: 4px; }
        .foot .date { color: #666; }
        .seal {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #c9a227;
            border: 3px solid #a68522;
            color: #5c4a0a;
            font-size: 6pt;
            font-weight: bold;
            text-align: center;
            margin: 20px auto 0;
            line-height: 70px;
            letter-spacing: 0.05em;
        }
        .brand {
            text-align: right;
            margin-top: 12px;
            padding-right: 24px;
            font-size: 8pt;
            color: #888;
        }
        .brand strong { color: #1a4d2e; }
    </style>
</head>
<body>
    <div class="page">
        <div class="inner">
            <div class="banner">Certificate of Completion</div>

            <div class="content-wrap">
                <div class="cert-body">
                    <p class="intro">This certificate is to certify that</p>
                    <p class="name">{{ $certificate->user->name ?? 'Participant' }}{{ $certificate->user->surname ? ' ' . $certificate->user->surname : '' }}</p>
                    <p class="course-title">{{ $certificate->course->title ?? 'Course' }}</p>
                    <p class="statement">has successfully completed the above course.</p>
                    <p class="org">TTM Group</p>
                    <p class="foot">
                        <span class="number">Certificate no. {{ $certificate->certificate_number }}</span><br>
                        <span class="date">{{ $certificate->issued_at ? $certificate->issued_at->format('d F Y') : '' }}</span>
                    </p>
                </div>

                <div class="seal">COMPLETION</div>
            </div>

            <div class="brand"><strong>TTM Group</strong> Learning Management System</div>
        </div>
    </div>
</body>
</html>
