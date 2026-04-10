@extends('layouts.public-legal')

@section('title', 'Privacy Policy')
@section('content')
    <h1>Privacy policy</h1>
    <div style="color:var(--text-muted);font-size:0.95rem;line-height:1.65;">
            <p>
                This platform supports constitutional learning, digital library access, dialogue moderation, certificates, and administration.
                We only collect information necessary to operate these services and to protect the integrity of the system.
            </p>

            <h3 style="color:var(--text-main);margin-top:1rem;">Information we collect</h3>
            <ul>
                <li>Account information (name, surname, email).</li>
                <li>Profile details required for Academy participation (e.g. province, national ID number where required).</li>
                <li>Learning activity (enrolments, assessment attempts, certificates).</li>
                <li>Dialogue content you submit (threads, messages, and any attachments you upload).</li>
                <li>Security and audit information (IP address, user agent, admin actions) to detect abuse and maintain oversight.</li>
            </ul>

            <h3 style="color:var(--text-main);margin-top:1rem;">How we use information</h3>
            <ul>
                <li>To authenticate users and enforce access control.</li>
                <li>To deliver Academy content, track progress, and issue certificates.</li>
                <li>To moderate dialogue and uphold platform rules.</li>
                <li>To operate analytics and improve quality and reliability.</li>
                <li>To comply with lawful requests and internal governance requirements.</li>
            </ul>

            <h3 style="color:var(--text-main);margin-top:1rem;">Retention</h3>
            <p>
                We retain information for as long as needed to provide the service, meet governance/audit requirements, and comply with applicable law.
            </p>

            <h3 style="color:var(--text-main);margin-top:1rem;">Contact</h3>
            <p>
                For privacy questions, contact your system administrator.
            </p>
@endsection

