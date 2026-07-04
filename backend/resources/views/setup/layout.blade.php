<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Installation') • ZANUPF</title>
    @include('partials.favicon')
    <style>
        :root{
            --page-bg:#0f766e;
            --page-bg-deep:#115e59;
            --zanupf-green:#166534;
            --zanupf-red:#c10102;
            --zanupf-gold:#facc15;
            --ink:#1e293b;
            --muted:#64748b;
            --line:#e2e8f0;
            --card:#ffffff;
        }
        *{ box-sizing:border-box; }
        body{
            margin:0;
            min-height:100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, "Noto Sans", sans-serif;
            color:var(--ink);
            background:
                linear-gradient(160deg, var(--page-bg) 0%, var(--page-bg-deep) 55%, #134e4a 100%);
            padding: 32px 20px 48px;
        }
        .page{
            max-width: 920px;
            margin: 0 auto;
        }
        .page-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            margin-bottom: 22px;
            color:#fff;
        }
        .brand{
            display:flex;
            align-items:center;
            gap:12px;
            min-width:0;
        }
        .brand-logo{
            width:44px;
            height:44px;
            border-radius:10px;
            background:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            flex-shrink:0;
            box-shadow: 0 6px 16px rgba(0,0,0,.18);
        }
        .brand-logo img{ width:36px; height:36px; object-fit:contain; }
        .brand-name{
            font-size:20px;
            font-weight:800;
            letter-spacing:.2px;
            white-space:nowrap;
        }
        .page-title{
            font-size:20px;
            font-weight:800;
            text-align:right;
            white-space:nowrap;
        }
        @media (max-width:640px){
            .page-header{ flex-direction:column; align-items:flex-start; }
            .page-title{ text-align:left; font-size:18px; }
        }
        .card{
            background:var(--card);
            border-radius: 6px;
            box-shadow: 0 18px 50px rgba(2,6,23,.22);
            overflow:hidden;
        }
        /* Horizontal stepper (reference-style) */
        .stepper{
            padding: 28px 36px 8px;
            border-bottom: 1px solid var(--line);
        }
        @media (max-width:720px){
            .stepper{ padding:20px 16px 6px; overflow-x:auto; }
        }
        .stepper-track{
            display:flex;
            align-items:flex-end;
            position:relative;
            min-width: 680px;
        }
        .stepper-track::before{
            content:'';
            position:absolute;
            left:8%;
            right:8%;
            bottom:9px;
            height:2px;
            background:var(--line);
            z-index:0;
        }
        .stepper-item{
            flex:1;
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:10px;
            position:relative;
            z-index:1;
            text-align:center;
        }
        .stepper-label{
            font-size:12px;
            color:var(--muted);
            font-weight:500;
            line-height:1.25;
            padding:0 4px;
        }
        .stepper-item.active .stepper-label,
        .stepper-item.done .stepper-label{
            color:var(--ink);
            font-weight:700;
        }
        .stepper-dot{
            width:18px;
            height:18px;
            border-radius:50%;
            background:#fff;
            border:2px solid #cbd5e1;
            transition: all .15s ease;
        }
        .stepper-item.done .stepper-dot{
            border-color:var(--zanupf-green);
            background:var(--zanupf-green);
            box-shadow: inset 0 0 0 3px #fff;
        }
        .stepper-item.active .stepper-dot{
            width:20px;
            height:20px;
            border-color:var(--zanupf-green);
            background:var(--zanupf-green);
            box-shadow: 0 0 0 4px rgba(22,101,52,.15);
        }
        .card-body{
            padding: 28px 36px 32px;
        }
        @media (max-width:640px){
            .card-body{ padding:20px 18px 24px; }
        }
        .step-heading{
            margin:0 0 14px;
            font-size:22px;
            font-weight:800;
            color:var(--ink);
        }
        .step-lead{
            margin:0 0 18px;
            font-size:14px;
            line-height:1.65;
            color:var(--muted);
        }
        .step-lead strong{ color:var(--ink); font-weight:700; }
        .body.two-col{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        @media (max-width:768px){ .body.two-col{ grid-template-columns:1fr; } }
        .section{
            margin-bottom: 20px;
        }
        .section h3{
            margin:0 0 8px;
            font-size:15px;
            font-weight:700;
            color:var(--ink);
        }
        .section p{
            margin:0 0 10px;
            font-size:13px;
            line-height:1.55;
            color:var(--muted);
        }
        .grid{ display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        @media (max-width:620px){ .grid{ grid-template-columns:1fr; } }
        .full{ grid-column:1 / -1; }
        label{ display:block; font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px; }
        .wizard-label{
            display:flex;
            align-items:center;
            gap:6px;
            flex-wrap:wrap;
        }
        .field-label{
            display:flex;
            align-items:center;
            gap:6px;
            font-size:12px;
            color:var(--muted);
            font-weight:600;
        }
        .field-tip{
            position:relative;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:16px;
            height:16px;
            border-radius:50%;
            background:#334155;
            color:#fff;
            font-size:10px;
            font-weight:800;
            font-style:normal;
            cursor:help;
            flex-shrink:0;
            line-height:1;
        }
        .field-tip-text{
            display:none;
            position:absolute;
            left:50%;
            bottom:calc(100% + 8px);
            transform:translateX(-50%);
            width:min(280px, 72vw);
            padding:10px 12px;
            border-radius:4px;
            background:#0f172a;
            color:#f8fafc;
            font-size:11px;
            font-weight:500;
            line-height:1.45;
            text-align:left;
            box-shadow:0 8px 24px rgba(2,6,23,.28);
            z-index:20;
        }
        .field-tip-text strong{ color:#f8fafc; font-weight:700; }
        .field-tip:hover .field-tip-text,
        .field-tip:focus .field-tip-text,
        .field-tip:focus-within .field-tip-text{
            display:block;
        }
        .section-heading{
            display:flex;
            align-items:center;
            gap:8px;
            flex-wrap:wrap;
        }
        .section-heading h3{ margin:0; }
        .tog-title{
            display:flex;
            align-items:flex-start;
            gap:6px;
        }
        .tog-title strong{ flex:1; min-width:0; }
        input[type="text"], input[type="email"], input[type="url"], input[type="password"]{
            width:100%;
            padding:10px 12px;
            border-radius:4px;
            border:1px solid #cbd5e1;
            font-size:14px;
            outline:none;
            background:#fff;
        }
        input:focus{
            border-color:var(--zanupf-green);
            box-shadow:0 0 0 3px rgba(22,101,52,.12);
        }
        .row{ margin-bottom:12px; }
        .tog{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            padding:12px 14px;
            border:1px solid var(--line);
            border-radius:4px;
            background:#f8fafc;
        }
        .tog strong{ font-size:13px; display:block; }
        .tog small{ display:block; color:var(--muted); font-size:12px; margin-top:3px; line-height:1.35; }
        .flash-ok, .flash-err, .err{
            padding:12px 14px;
            border-radius:4px;
            font-size:13px;
            margin-bottom:16px;
        }
        .flash-ok{ background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; }
        .flash-err, .err{ background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
        .hint{
            padding:12px 14px;
            border-radius:4px;
            background:#fffbeb;
            border:1px solid #fde68a;
            color:#78350f;
            font-size:13px;
            line-height:1.5;
            margin-top:12px;
        }
        .warn{
            margin-top:12px;
            padding:12px 14px;
            border-radius:4px;
            background:#fef2f2;
            border:1px solid #fecaca;
            color:#991b1b;
            font-size:13px;
            line-height:1.5;
        }
        code{ background:#f1f5f9; padding:.1rem .35rem; border-radius:4px; font-size:12px; }
        .check-list{ list-style:none; margin:0; padding:0; border:1px solid var(--line); border-radius:4px; overflow:hidden; }
        .check-list li{
            display:flex;
            gap:12px;
            align-items:flex-start;
            padding:12px 14px;
            border-bottom:1px solid var(--line);
            font-size:13px;
            background:#fff;
        }
        .check-list li:last-child{ border-bottom:none; }
        .check-list li:nth-child(even){ background:#f8fafc; }
        .status{
            flex:0 0 auto;
            font-size:10px;
            font-weight:800;
            padding:4px 8px;
            border-radius:3px;
            text-transform:uppercase;
            letter-spacing:.04em;
        }
        .status.pass{ background:#dcfce7; color:#166534; }
        .status.warn{ background:#fef9c3; color:#854d0e; }
        .status.fail{ background:#fee2e2; color:#991b1b; }
        .check-body strong{ display:block; margin-bottom:2px; font-size:13px; }
        .check-body span{ color:var(--muted); font-size:12px; line-height:1.4; }
        .config-box{
            margin-top:10px;
            padding:12px 14px;
            border:1px solid var(--line);
            border-radius:4px;
            background:#f8fafc;
        }
        .config-box-title{ font-weight:700; font-size:13px; margin-bottom:6px; }
        .kv{ width:100%; border-collapse:collapse; font-size:12px; }
        .kv td{ padding:7px 0; border-bottom:1px solid var(--line); vertical-align:top; }
        .kv tr:last-child td{ border-bottom:none; }
        .kv .k{ color:var(--muted); width:38%; padding-right:8px; }
        .kv .v{ font-family:ui-monospace,Consolas,monospace; word-break:break-all; }
        .pill{
            display:inline-block;
            font-size:10px;
            padding:2px 7px;
            border-radius:999px;
            background:#e2e8f0;
            color:#475569;
            margin-left:6px;
            font-weight:600;
        }
        .card-footer{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            padding:16px 36px 20px;
            border-top:1px solid var(--line);
            background:#fafafa;
        }
        @media (max-width:640px){
            .card-footer{ flex-direction:column; align-items:stretch; padding:16px 18px; }
        }
        .footer-link{
            color:#2563eb;
            font-size:14px;
            font-weight:600;
            text-decoration:none;
            background:none;
            border:none;
            cursor:pointer;
            padding:0;
        }
        .footer-link:hover{ text-decoration:underline; }
        .footer-link-muted{ color:var(--muted); }
        .btn-next{
            display:inline-flex;
            align-items:center;
            gap:8px;
            border:none;
            border-radius:4px;
            padding:12px 22px;
            font-size:14px;
            font-weight:700;
            cursor:pointer;
            color:#fff;
            background:var(--zanupf-red);
            box-shadow: 0 4px 14px rgba(193,1,2,.28);
            text-decoration:none;
        }
        .btn-next:hover{ filter:brightness(1.05); }
        .btn-next:disabled{ opacity:.45; cursor:not-allowed; filter:none; }
        .btn-next svg{ flex-shrink:0; }
        .footer-actions{ display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        @media (max-width:640px){ .footer-actions{ justify-content:stretch; } .btn-next{ justify-content:center; width:100%; } }
        .footer-left{ min-width:0; }
        .splash{
            margin: -28px -36px 0;
            min-height: 320px;
            background:
                linear-gradient(105deg, rgba(22,101,52,.88) 0%, rgba(15,118,110,.78) 45%, rgba(2,6,23,.62) 100%),
                url("{{ asset('bg-1.jpg') }}") center/cover no-repeat;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            text-align:center;
            padding: 40px 28px 36px;
            color:#fff;
        }
        @media (max-width:640px){
            .splash{ margin:-20px -18px 0; min-height:280px; padding:32px 20px; }
        }
        .splash-logo{
            width:96px;
            height:96px;
            border-radius:18px;
            background:rgba(255,255,255,.97);
            border:3px solid rgba(250,204,21,.75);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 16px 40px rgba(2,6,23,.28);
            margin-bottom:18px;
        }
        .splash-logo img{ width:78px; height:78px; object-fit:contain; }
        .splash h2{
            margin:0 0 10px;
            font-size:28px;
            font-weight:800;
            line-height:1.2;
            max-width:20ch;
        }
        .splash p{
            margin:0;
            font-size:14px;
            line-height:1.6;
            color:rgba(255,255,255,.93);
            max-width:46ch;
        }
        .splash-badge{
            display:inline-block;
            margin-top:16px;
            padding:6px 14px;
            border-radius:999px;
            background:var(--zanupf-gold);
            color:#14532d;
            font-size:11px;
            font-weight:800;
            letter-spacing:.05em;
            text-transform:uppercase;
        }
        a.btn-next{ text-decoration:none; }
    </style>
    @stack('head')
</head>
<body>
@php
    $currentStep = $step ?? 1;
    $steps = [
        1 => 'Welcome',
        2 => 'System checks',
        3 => 'Administrator',
        4 => 'Platform',
        5 => 'Install content',
        6 => 'Complete',
    ];
@endphp
<div class="page">
    <header class="page-header">
        <div class="brand">
            <div class="brand-logo">
                <img src="{{ asset('Logo.png') }}" alt="ZANU PF">
            </div>
            <span class="brand-name">ZANU PF</span>
        </div>
        <div class="page-title">Platform Setup Wizard</div>
    </header>

    <div class="card">
        <nav class="stepper" aria-label="Installation steps">
            <div class="stepper-track">
                @foreach ($steps as $num => $label)
                    <div class="stepper-item {{ $num === $currentStep ? 'active' : ($num < $currentStep ? 'done' : '') }}">
                        <span class="stepper-label">{{ $label }}</span>
                        <span class="stepper-dot" aria-hidden="true"></span>
                    </div>
                @endforeach
            </div>
        </nav>

        <div class="card-body">
            @if (session('success'))
                <div class="flash-ok">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash-err">{{ session('error') }}</div>
            @endif

            @hasSection('splash')
                @yield('splash')
            @else
                <h2 class="step-heading">@yield('heading', 'Installation')</h2>
                @hasSection('subheading')
                    <p class="step-lead">@yield('subheading')</p>
                @endif

                <div class="@yield('body_class')">
                    @yield('content')
                </div>
            @endif
        </div>

        <footer class="card-footer">
            <div class="footer-left">
                @yield('footer_left')
            </div>
            <div class="footer-actions">
                @yield('footer_right')
            </div>
        </footer>
    </div>
</div>
</body>
</html>
