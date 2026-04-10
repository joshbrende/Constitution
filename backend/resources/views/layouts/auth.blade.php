<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'ZANU PF Academy')</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body {
                    margin: 0;
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                    background: #000;
                    color: #f9fafb;
                }
                .auth-shell {
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 2rem;
                    position: relative;
                    overflow: hidden;
                    background-image: url('{{ asset('bg-1.jpg') }}');
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                }
                .auth-shell::before {
                    content: "";
                    position: absolute;
                    inset: 0;
                    background: rgba(255, 255, 255, 0.75);
                }
                .auth-card {
                    position: relative;
                    z-index: 1;
                    width: 100%;
                    max-width: 420px;
                    background: #111827;
                    border-radius: 0.75rem;
                    border: 1px solid #065f46; /* deep green */
                    box-shadow: 0 20px 40px rgba(0,0,0,0.6);
                    padding: 2rem 2.25rem;
                }
                .auth-title {
                    font-size: 1.5rem;
                    font-weight: 700;
                    margin-bottom: 0.25rem;
                    color: #f9fafb;
                }
                .auth-subtitle {
                    font-size: 0.9rem;
                    color: #d1d5db;
                    margin-bottom: 1.5rem;
                }
                .badge-flag {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.25rem;
                    padding: 0.15rem 0.55rem;
                    border-radius: 999px;
                    font-size: 0.7rem;
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                    border: 1px solid #4b5563;
                    background: rgba(15, 23, 42, 0.9);
                    color: #f9fafb;
                    margin-bottom: 0.75rem;
                }
                .badge-dot {
                    width: 6px;
                    height: 6px;
                    border-radius: 999px;
                    background: #22c55e;
                }
                .form-group {
                    margin-bottom: 1rem;
                }
                label {
                    display: block;
                    font-size: 0.8rem;
                    font-weight: 600;
                    margin-bottom: 0.25rem;
                    color: #e5e7eb;
                }
                input[type="text"],
                input[type="email"],
                input[type="password"] {
                    width: 100%;
                    padding: 0.6rem 0.75rem;
                    border-radius: 0.5rem;
                    border: 1px solid #374151;
                    background: #020617;
                    color: #f9fafb;
                    font-size: 0.9rem;
                    outline: none;
                }
                input::placeholder {
                    color: #6b7280;
                }
                input:focus {
                    border-color: #facc15; /* gold */
                    box-shadow: 0 0 0 1px #facc15;
                }
                .password-wrapper {
                    position: relative;
                }
                .password-toggle {
                    position: absolute;
                    right: 0.6rem;
                    top: 50%;
                    transform: translateY(-50%);
                    border: none;
                    background: transparent;
                    color: #9ca3af;
                    cursor: pointer;
                    font-size: 0.75rem;
                }
                .actions-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 0.75rem;
                    margin-top: 1rem;
                }
                .btn-primary {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.35rem;
                    padding: 0.65rem 1.4rem;
                    border-radius: 999px;
                    border: 1px solid #15803d;
                    background: #15803d; /* solid deep green */
                    color: #f9fafb;
                    font-size: 0.9rem;
                    font-weight: 600;
                    cursor: pointer;
                }
                .btn-primary:hover {
                    filter: brightness(1.05);
                }
                .btn-ghost {
                    border: none;
                    background: transparent;
                    color: #e5e7eb;
                    font-size: 0.8rem;
                    text-decoration: underline;
                    text-underline-offset: 3px;
                    cursor: pointer;
                }
                .helper-text {
                    font-size: 0.75rem;
                    color: #9ca3af;
                    margin-top: 0.4rem;
                }
                .text-link {
                    color: #facc15;
                    text-decoration: underline;
                    text-underline-offset: 3px;
                    font-size: 0.8rem;
                }
                .error-text {
                    font-size: 0.75rem;
                    color: #fecaca;
                    margin-top: 0.2rem;
                }
                .checkbox-row {
                    display: flex;
                    align-items: flex-start;
                    gap: 0.5rem;
                    font-size: 0.8rem;
                    color: #e5e7eb;
                    margin-top: 0.5rem;
                }
                .checkbox-row input {
                    margin-top: 0.05rem;
                }
                .dash-grid {
                    display: grid;
                    grid-template-columns: repeat(1, minmax(0, 1fr));
                    gap: 0.9rem;
                    margin-top: 1.25rem;
                }
                @media (min-width: 768px) {
                    .dash-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                    }
                }
                .dash-card {
                    border-radius: 0.75rem;
                    border: 1px solid #1f2937;
                    background: radial-gradient(circle at top left, rgba(34,197,94,0.10), #020617);
                    padding: 0.9rem 1rem;
                    cursor: pointer;
                    transition: border-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
                }
                .dash-card:hover {
                    border-color: #facc15;
                    box-shadow: 0 12px 24px rgba(0,0,0,0.45);
                    transform: translateY(-1px);
                }
                .dash-card-title {
                    font-size: 0.95rem;
                    font-weight: 600;
                    margin-bottom: 0.15rem;
                    color: #f9fafb;
                }
                .dash-card-text {
                    font-size: 0.8rem;
                    color: #d1d5db;
                }
            </style>
        @endif
    </head>
    <body>
        <div class="auth-shell">
            <div class="auth-card">
                <div class="badge-flag">
                    <span class="badge-dot"></span>
                    <span>Secure access</span>
                </div>
                @yield('content')
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const targetId = this.getAttribute('data-toggle-password');
                        const input = document.getElementById(targetId);
                        if (!input) return;
                        if (input.type === 'password') {
                            input.type = 'text';
                            this.textContent = 'Hide';
                        } else {
                            input.type = 'password';
                            this.textContent = 'Show';
                        }
                    });
                });
            });
        </script>
    </body>
</html>

