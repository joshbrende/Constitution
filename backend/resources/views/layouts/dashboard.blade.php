<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard')</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                :root {
                    --zanupf-green: #15803d;
                    --zanupf-gold: #facc15;
                    --zanupf-red: #b91c1c;
                    --bg-surface: #0b1120;
                    --bg-panel: #020617;
                    --bg-sidebar: #020617;
                    --text-main: #f9fafb;
                    --text-muted: #9ca3af;
                    --border-subtle: #1f2937;
                }
                * {
                    box-sizing: border-box;
                }
                body {
                    margin: 0;
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                }
                body.theme-dark {
                    background: #020617;
                    color: var(--text-main);
                }
                body.theme-light {
                    --bg-surface: #f9fafb;
                    --bg-panel: #ffffff;
                    --bg-sidebar: #0f172a;
                    --text-main: #020617;
                    --text-muted: #6b7280;
                    --border-subtle: #e5e7eb;
                    background: #f3f4f6;
                    color: #020617;
                }
                .skip-to-main {
                    position: absolute;
                    left: -9999px;
                    top: 0.75rem;
                    z-index: 10000;
                    padding: 0.5rem 1rem;
                    border-radius: 0.375rem;
                    background: #facc15;
                    color: #020617;
                    font-weight: 600;
                    font-size: 0.85rem;
                    text-decoration: none;
                }
                .skip-to-main:focus {
                    left: 0.75rem;
                    outline: 2px solid #facc15;
                    outline-offset: 2px;
                }
                .dash-shell {
                    min-height: 100vh;
                    display: flex;
                    color: var(--text-main);
                }
                .dash-sidebar {
                    width: 240px;
                    background: var(--bg-sidebar);
                    border-right: 1px solid var(--border-subtle);
                    display: flex;
                    flex-direction: column;
                    padding: 1.25rem 1rem;
                    gap: 1.25rem;
                }
                .dash-logo {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-weight: 700;
                    font-size: 1rem;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                }
                .dash-logo-mark {
                    width: 26px;
                    height: 26px;
                    border-radius: 0.5rem;
                    background: conic-gradient(from 180deg, var(--zanupf-green), var(--zanupf-gold), var(--zanupf-red), var(--zanupf-green));
                    border: 2px solid #020617;
                }
                .dash-nav-group-label {
                    font-size: 0.7rem;
                    text-transform: uppercase;
                    letter-spacing: 0.12em;
                    color: var(--text-muted);
                    margin: 0.75rem 0 0.3rem 0.35rem;
                }
                .dash-nav {
                    list-style: none;
                    margin: 0;
                    padding: 0;
                }
                .dash-nav-item {
                    margin-bottom: 0.1rem;
                }
                .dash-nav-link {
                    display: flex;
                    align-items: center;
                    gap: 0.55rem;
                    padding: 0.45rem 0.7rem;
                    border-radius: 0.5rem;
                    font-size: 0.85rem;
                    color: var(--text-muted);
                    text-decoration: none;
                    cursor: pointer;
                    transition: background-color 0.15s ease, color 0.15s ease;
                }
                .dash-nav-link span.dot {
                    width: 8px;
                    height: 8px;
                    border-radius: 999px;
                    background: #4b5563;
                }
                .dash-nav-link.is-active {
                    background: rgba(15,118,110,0.35);
                    color: var(--text-main);
                }
                .dash-nav-link.is-active span.dot {
                    background: var(--zanupf-gold);
                }
                .dash-nav-link:hover {
                    background: rgba(31,41,55,0.9);
                    color: var(--text-main);
                }
                .dash-sidebar-footer {
                    margin-top: auto;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 0.75rem;
                    padding-top: 0.75rem;
                    border-top: 1px solid var(--border-subtle);
                    font-size: 0.8rem;
                }
                .dash-user {
                    display: flex;
                    align-items: center;
                    gap: 0.4rem;
                }
                .dash-user-avatar {
                    width: 26px;
                    height: 26px;
                    border-radius: 999px;
                    background: #111827;
                    border: 1px solid var(--border-subtle);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 0.7rem;
                    font-weight: 600;
                }
                .dash-user-meta small {
                    color: var(--text-muted);
                    display: block;
                }
                .dash-btn-ghost {
                    border: none;
                    background: transparent;
                    color: var(--text-muted);
                    font-size: 0.78rem;
                    cursor: pointer;
                    text-decoration: underline;
                    text-underline-offset: 3px;
                }
                .dash-main {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    padding: 1.25rem 1.5rem;
                    gap: 1rem;
                }
                .dash-footer {
                    margin-top: auto;
                    padding: 0.9rem 0;
                    border-top: 1px solid rgba(31,41,55,0.9);
                    color: var(--text-muted);
                    font-size: 0.78rem;
                    display: flex;
                    justify-content: space-between;
                    gap: 1rem;
                }
                .dash-footer strong {
                    color: var(--text-main);
                    font-weight: 600;
                }
                .dash-footer-links {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.75rem;
                    flex-wrap: wrap;
                    justify-content: flex-end;
                }
                .dash-footer a {
                    color: var(--text-muted);
                    text-decoration: none;
                    border-bottom: 1px solid transparent;
                    padding-bottom: 2px;
                }
                .dash-footer a:hover {
                    color: var(--zanupf-gold);
                    border-bottom-color: rgba(250,204,21,0.55);
                }
                .dash-topbar {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                }
                .dash-search {
                    flex: 1;
                    max-width: 360px;
                    position: relative;
                }
                .dash-search input {
                    width: 100%;
                    padding: 0.45rem 0.65rem 0.45rem 1.8rem;
                    border-radius: 999px;
                    border: 1px solid var(--border-subtle);
                    background: rgba(15,23,42,0.9);
                    color: var(--text-main);
                    font-size: 0.8rem;
                    outline: none;
                }
                .dash-search input::placeholder {
                    color: var(--text-muted);
                }
                .dash-search-icon {
                    position: absolute;
                    left: 0.6rem;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 0.8rem;
                    color: var(--text-muted);
                }
                .dash-search-menu {
                    position: absolute;
                    left: 0;
                    top: calc(100% + 10px);
                    width: min(560px, 92vw);
                    border-radius: 0.9rem;
                    border: 1px solid var(--border-subtle);
                    background: rgba(2,6,23,0.98);
                    box-shadow: 0 18px 50px rgba(0,0,0,0.45);
                    padding: 0.6rem;
                    z-index: 75;
                    display: none;
                }
                .dash-search-menu.is-open { display: block; }
                .dash-search-group-title {
                    font-size: 0.72rem;
                    letter-spacing: 0.1em;
                    text-transform: uppercase;
                    color: var(--text-muted);
                    margin: 0.55rem 0.35rem 0.25rem 0.35rem;
                }
                .dash-search-item {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 0.75rem;
                    padding: 0.5rem 0.6rem;
                    border-radius: 0.75rem;
                    color: var(--text-main);
                    text-decoration: none;
                    border: 1px solid transparent;
                }
                .dash-search-item:hover {
                    border-color: rgba(250,204,21,0.35);
                    background: rgba(15,23,42,0.9);
                }
                .dash-search-item small { color: var(--text-muted); display: block; margin-top: 0.1rem; }
                .dash-search-empty {
                    padding: 0.7rem 0.65rem;
                    color: var(--text-muted);
                    font-size: 0.82rem;
                }
                .dash-kpis {
                    display: flex;
                    gap: 0.75rem;
                    flex-wrap: wrap;
                    justify-content: flex-end;
                }
                .dash-topbar-actions {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    position: relative;
                }
                .dash-icon-btn {
                    width: 36px;
                    height: 36px;
                    border-radius: 999px;
                    border: 1px solid var(--border-subtle);
                    background: rgba(15,23,42,0.9);
                    color: var(--text-muted);
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: transform 0.12s ease, border-color 0.12s ease, color 0.12s ease;
                    position: relative;
                }
                .dash-icon-btn:hover {
                    border-color: rgba(250,204,21,0.8);
                    color: var(--zanupf-gold);
                    transform: translateY(-1px);
                }
                .dash-icon-badge {
                    position: absolute;
                    top: -6px;
                    right: -6px;
                    min-width: 18px;
                    height: 18px;
                    padding: 0 5px;
                    border-radius: 999px;
                    background: var(--zanupf-gold);
                    color: #020617;
                    font-size: 0.7rem;
                    font-weight: 800;
                    line-height: 18px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 0 0 2px rgba(2,6,23,0.95);
                }
                .dash-bell-menu {
                    position: absolute;
                    right: 0;
                    top: calc(100% + 10px);
                    width: 320px;
                    border-radius: 0.9rem;
                    border: 1px solid var(--border-subtle);
                    background: rgba(2,6,23,0.98);
                    box-shadow: 0 18px 50px rgba(0,0,0,0.45);
                    padding: 0.65rem;
                    z-index: 70;
                    display: none;
                }
                .dash-bell-menu.is-open { display: block; }
                .dash-bell-title {
                    font-size: 0.78rem;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                    color: var(--text-muted);
                    margin: 0.15rem 0.25rem 0.45rem 0.25rem;
                }
                .dash-bell-item {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 0.75rem;
                    padding: 0.55rem 0.65rem;
                    border-radius: 0.75rem;
                    color: var(--text-main);
                    text-decoration: none;
                    border: 1px solid transparent;
                }
                .dash-bell-item:hover {
                    border-color: rgba(250,204,21,0.35);
                    background: rgba(15,23,42,0.9);
                }
                .dash-bell-item small { color: var(--text-muted); display: block; margin-top: 0.1rem; }

                .dash-drawer-overlay {
                    position: fixed;
                    inset: 0;
                    background: rgba(2,6,23,0.65);
                    backdrop-filter: blur(2px);
                    z-index: 80;
                    display: none;
                }
                .dash-drawer-overlay.is-open { display: block; }
                .dash-drawer {
                    position: fixed;
                    top: 0;
                    right: 0;
                    height: 100vh;
                    width: min(420px, 92vw);
                    background: rgba(2,6,23,0.98);
                    border-left: 1px solid var(--border-subtle);
                    box-shadow: -18px 0 55px rgba(0,0,0,0.5);
                    z-index: 90;
                    transform: translateX(105%);
                    transition: transform 0.18s ease;
                    display: flex;
                    flex-direction: column;
                }
                .dash-drawer.is-open { transform: translateX(0); }
                .dash-drawer-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                    padding: 1.1rem 1rem 0.75rem 1rem;
                    border-bottom: 1px solid rgba(31,41,55,0.9);
                }
                .dash-drawer-title { font-size: 1.05rem; font-weight: 700; }
                .dash-drawer-subtitle { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; }
                .dash-drawer-body {
                    padding: 0.95rem 1rem 1.1rem 1rem;
                    overflow: auto;
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                }
                .dash-drawer-section {
                    border: 1px solid rgba(31,41,55,0.9);
                    background: rgba(15,23,42,0.9);
                    border-radius: 0.9rem;
                    padding: 0.8rem 0.85rem;
                }
                .dash-drawer-section h3 {
                    margin: 0 0 0.55rem 0;
                    font-size: 0.78rem;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                    color: var(--text-muted);
                }
                .dash-quick-links {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 0.5rem;
                }
                .dash-quick-link {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0.5rem 0.6rem;
                    border-radius: 0.7rem;
                    border: 1px solid rgba(250,204,21,0.22);
                    background: rgba(2,6,23,0.35);
                    color: var(--text-main);
                    text-decoration: none;
                    font-size: 0.82rem;
                }
                .dash-quick-link:hover {
                    border-color: rgba(250,204,21,0.55);
                    color: var(--zanupf-gold);
                }
                .dash-toggle-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 0.75rem;
                    padding: 0.45rem 0.2rem;
                    font-size: 0.85rem;
                }
                .dash-toggle-row small { display:block; color: var(--text-muted); margin-top: 0.15rem; }
                .dash-switch {
                    appearance: none;
                    width: 42px;
                    height: 24px;
                    border-radius: 999px;
                    border: 1px solid rgba(107,114,128,0.8);
                    background: rgba(15,23,42,0.9);
                    position: relative;
                    cursor: pointer;
                    outline: none;
                }
                .dash-switch:checked { border-color: rgba(34,197,94,0.7); background: rgba(21,128,61,0.35); }
                .dash-switch::after {
                    content: '';
                    position: absolute;
                    top: 3px;
                    left: 3px;
                    width: 18px;
                    height: 18px;
                    border-radius: 999px;
                    background: #e5e7eb;
                    transition: transform 0.14s ease;
                }
                .dash-switch:checked::after { transform: translateX(18px); }

                body.dash-hide-kpis .dash-kpi-pill { display: none; }
                body.dash-compact .dash-tile { padding: 0.65rem 0.75rem; }
                body.dash-compact .dash-tile-title { font-size: 0.9rem; }
                body.dash-compact .dash-tile-text { font-size: 0.78rem; }
                .dash-mode-toggle {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.35rem;
                    padding: 0.35rem 0.75rem;
                    border-radius: 999px;
                    border: 1px solid var(--border-subtle);
                    background: rgba(15,23,42,0.9);
                    font-size: 0.72rem;
                    color: var(--text-muted);
                    cursor: default;
                }
                .dash-mode-toggle-pill {
                    padding: 0.2rem 0.55rem;
                    border-radius: 999px;
                    background: #0f172a;
                    color: var(--text-muted);
                }
                .dash-mode-toggle-pill.is-active {
                    background: var(--zanupf-green);
                    color: #f9fafb;
                }
                .dash-kpi-pill {
                    min-width: 110px;
                    padding: 0.45rem 0.75rem;
                    border-radius: 999px;
                    border: 1px solid #1f2937;
                    background: rgba(15,23,42,0.9);
                    font-size: 0.75rem;
                }
                .dash-kpi-label {
                    color: var(--text-muted);
                    display: block;
                }
                .dash-kpi-value {
                    font-weight: 600;
                    color: var(--zanupf-gold);
                }
                .dash-content {
                    display: grid;
                    grid-template-columns: minmax(0, 2.2fr) minmax(0, 1fr);
                    gap: 1rem;
                    align-items: flex-start;
                }
                .dash-panel {
                    background: rgba(15,23,42,0.9);
                    border-radius: 0.9rem;
                    border: 1px solid var(--border-subtle);
                    padding: 0.9rem 1rem;
                }
                .dash-panel-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 0.75rem;
                }
                .dash-panel-title {
                    font-size: 0.95rem;
                    font-weight: 600;
                }
                .dash-panel-subtitle {
                    font-size: 0.78rem;
                    color: var(--text-muted);
                }
                .dash-tag {
                    font-size: 0.7rem;
                    padding: 0.15rem 0.5rem;
                    border-radius: 999px;
                    border: 1px solid #4b5563;
                    color: var(--text-muted);
                }
                .dash-alert {
                    padding: 0.6rem 0.9rem;
                    border-radius: 0.5rem;
                    font-size: 0.85rem;
                    margin-bottom: 1rem;
                }
                .dash-alert--success {
                    background: rgba(34,197,94,0.16);
                    border: 1px solid rgba(34,197,94,0.4);
                    color: #bbf7d0;
                }
                .dash-alert--error {
                    background: rgba(239,68,68,0.16);
                    border: 1px solid rgba(239,68,68,0.4);
                    color: #fecaca;
                }
                table.dash-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 0.8rem;
                }
                table.dash-table th,
                table.dash-table td {
                    padding: 0.35rem 0.4rem;
                    border-bottom: 1px solid rgba(31,41,55,0.9);
                }
                table.dash-table th {
                    text-align: left;
                    font-weight: 500;
                    color: var(--text-muted);
                    font-size: 0.75rem;
                }
                table.dash-table tr:last-child td {
                    border-bottom: none;
                }
                .status-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.25rem;
                    padding: 0.12rem 0.5rem;
                    border-radius: 999px;
                    font-size: 0.7rem;
                }
                .status-pill span.dot {
                    width: 6px;
                    height: 6px;
                    border-radius: 999px;
                }
                .status-pill--active {
                    background: rgba(34,197,94,0.16);
                    color: #bbf7d0;
                }
                .status-pill--active span.dot {
                    background: #22c55e;
                }
                .status-pill--pending {
                    background: rgba(250,204,21,0.16);
                    color: #fef9c3;
                }
                .status-pill--pending span.dot {
                    background: #facc15;
                }
                .status-pill--review {
                    background: rgba(59,130,246,0.16);
                    color: #bfdbfe;
                }
                .status-pill--review span.dot {
                    background: #3b82f6;
                }
                .dash-metric-row {
                    display: flex;
                    flex-direction: column;
                    gap: 0.6rem;
                }
                .dash-metric-item {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    font-size: 0.8rem;
                }
                .dash-metric-label {
                    color: var(--text-muted);
                }
                .dash-metric-value {
                    font-weight: 600;
                }
                .dash-metric-bar {
                    width: 100%;
                    height: 4px;
                    border-radius: 999px;
                    background: #111827;
                    overflow: hidden;
                    margin-top: 0.2rem;
                }
                .dash-metric-bar-fill {
                    height: 100%;
                    background: linear-gradient(90deg, var(--zanupf-green), var(--zanupf-gold), var(--zanupf-red));
                }
                .dash-main-content {
                    margin-top: 0.5rem;
                }
                .const-layout {
                    display: grid;
                    grid-template-columns: 260px minmax(0, 1fr);
                    gap: 1rem;
                    align-items: flex-start;
                }
                .const-nav {
                    background: var(--bg-panel);
                    border-radius: 0.9rem;
                    border: 1px solid var(--border-subtle);
                    padding: 0.75rem 0.8rem;
                    max-height: calc(100vh - 140px);
                    overflow: auto;
                    font-size: 0.82rem;
                }
                .const-nav-chapter {
                    margin-bottom: 0.4rem;
                }
                .const-nav-chapter-title {
                    font-size: 0.78rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                    color: var(--text-muted);
                    margin-bottom: 0.25rem;
                }
                .const-nav-sections {
                    list-style: none;
                    margin: 0;
                    padding: 0;
                }
                .const-nav-link {
                    display: block;
                    padding: 0.25rem 0.35rem;
                    border-radius: 0.4rem;
                    text-decoration: none;
                    color: var(--text-muted);
                    cursor: pointer;
                }
                .const-nav-link span.number {
                    font-weight: 600;
                    margin-right: 0.3rem;
                    color: var(--zanupf-gold);
                }
                .const-nav-link.is-active {
                    background: rgba(21,128,61,0.22);
                    color: var(--text-main);
                }
                .const-doc-link.is-active {
                    color: var(--zanupf-gold);
                    font-weight: 600;
                }
                .const-reader {
                    background: var(--bg-panel);
                    border-radius: 0.9rem;
                    border: 1px solid var(--border-subtle);
                    padding: 0.9rem 1rem;
                    max-height: calc(100vh - 140px);
                    overflow: auto;
                }
                .const-reader-title {
                    font-size: 1rem;
                    font-weight: 600;
                    margin-bottom: 0.25rem;
                }
                .const-reader-meta {
                    font-size: 0.78rem;
                    color: var(--text-muted);
                    margin-bottom: 0.6rem;
                }
                .const-toolbar {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.4rem;
                    margin-bottom: 0.65rem;
                }
                .const-tool-btn {
                    padding: 0.25rem 0.5rem;
                    border-radius: 999px;
                    border: 1px solid var(--border-subtle);
                    background: transparent;
                    color: var(--text-muted);
                    font-size: 0.75rem;
                    cursor: default;
                }
                .const-body {
                    font-size: 0.9rem;
                    line-height: 1.6;
                    white-space: pre-wrap;
                }
                .dash-tiles {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
                    gap: 1rem;
                    margin-top: 0.75rem;
                }
                .dash-tile {
                    background: var(--bg-panel);
                    border-radius: 0.9rem;
                    border: 1px solid var(--border-subtle);
                    padding: 0.85rem 0.95rem;
                    text-decoration: none;
                    color: inherit;
                    cursor: pointer;
                    display: flex;
                    flex-direction: column;
                    gap: 0.35rem;
                    transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease, background-color 0.15s ease;
                }
                body.theme-dark .dash-tile {
                    background: rgba(15,23,42,0.95);
                }
                .dash-tile:hover {
                    border-color: var(--zanupf-gold);
                    box-shadow: 0 14px 30px rgba(0,0,0,0.45);
                    transform: translateY(-1px);
                }
                .dash-tile-title {
                    font-size: 0.95rem;
                    font-weight: 600;
                }
                .dash-tile-text {
                    font-size: 0.8rem;
                    color: var(--text-muted);
                }
                .dash-tile-footer {
                    margin-top: 0.3rem;
                    font-size: 0.78rem;
                    color: var(--zanupf-gold);
                }
            </style>
        @endif
    </head>
    <body>
        <a href="#main-content" class="skip-to-main">Skip to main content</a>
        <div class="dash-shell">
            <aside class="dash-sidebar" aria-label="Primary navigation">
                <div class="dash-logo">
                    <div class="dash-logo-mark"></div>
                    <span>ZANU PF</span>
                </div>

                <div>
                    <div class="dash-nav-group-label">Main</div>
                    <ul class="dash-nav">
                        <li class="dash-nav-item">
                            <a href="{{ route('dashboard') }}" class="dash-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Overview</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <div class="dash-nav-group-label">Constitution & Learning</div>
                    <ul class="dash-nav">
                        <li class="dash-nav-item">
                            <a href="{{ route('constitution.home', ['doc' => 'zanupf']) }}" class="dash-nav-link {{ (request()->routeIs('constitution.*') && request()->route('doc', 'zanupf') === 'zanupf') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>ZANU PF Constitution</span>
                            </a>
                        </li>
                        <li class="dash-nav-item">
                            <a href="{{ route('constitution.home', ['doc' => 'zimbabwe']) }}" class="dash-nav-link {{ (request()->routeIs('constitution.*') && request()->route('doc', 'zanupf') === 'zimbabwe') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Zimbabwe Constitution</span>
                            </a>
                        </li>
                        <li class="dash-nav-item">
                            <a href="{{ route('constitution.home', ['doc' => 'amendment3']) }}" class="dash-nav-link {{ (request()->routeIs('constitution.*') && request()->route('doc', 'zanupf') === 'amendment3') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Amendment Bill No. 3</span>
                            </a>
                        </li>
                        <li class="dash-nav-item">
                            <a href="{{ route('academy.home') }}" class="dash-nav-link {{ request()->routeIs('academy.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Academy</span>
                            </a>
                        </li>
                        <li class="dash-nav-item">
                            <a href="{{ route('library.home') }}" class="dash-nav-link {{ request()->routeIs('library.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Digital Library</span>
                            </a>
                        </li>
                        <li class="dash-nav-item">
                            <a href="{{ route('dialogue.home') }}" class="dash-nav-link {{ request()->routeIs('dialogue.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Opinion & Dialogue</span>
                            </a>
                        </li>
                    </ul>
                </div>

                @if(app(\App\Services\AdminAccessService::class)->hasAnyAdminAccess(auth()->user()))
                <div>
                    <div class="dash-nav-group-label">Help &amp; resources</div>
                    <ul class="dash-nav">
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.guide.documentation') }}" class="dash-nav-link {{ request()->routeIs('admin.guide.documentation') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Documentation</span>
                            </a>
                        </li>
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.guide.help') }}" class="dash-nav-link {{ request()->routeIs('admin.guide.help') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Help</span>
                            </a>
                        </li>
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.guide.settings') }}" class="dash-nav-link {{ request()->routeIs('admin.guide.settings') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="dash-nav-group-label">Administration</div>
                    <ul class="dash-nav">
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.home') }}" class="dash-nav-link {{ request()->routeIs('admin.home') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Admin & Oversight</span>
                            </a>
                        </li>
                        @canAccessSection('constitution')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.constitution.index') }}" class="dash-nav-link {{ request()->routeIs('admin.constitution.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Manage Constitution</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('academy')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.academy.index') }}" class="dash-nav-link {{ request()->routeIs('admin.academy.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Manage Academy</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('library')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.library.index') }}" class="dash-nav-link {{ request()->routeIs('admin.library.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Manage Digital Library</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('party')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.party.index') }}" class="dash-nav-link {{ request()->routeIs('admin.party.index') || request()->routeIs('admin.party.update') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Manage the Party</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('party_leagues')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.party-leagues.index') }}" class="dash-nav-link {{ request()->routeIs('admin.party-leagues.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Party Leagues</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('presidium')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.presidium.index') }}" class="dash-nav-link {{ request()->routeIs('admin.presidium.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Manage Presidium</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('party_organs')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.party-organs.index') }}" class="dash-nav-link {{ request()->routeIs('admin.party-organs.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Manage Party Organs</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('priority_projects')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.priority-projects.index') }}" class="dash-nav-link {{ request()->routeIs('admin.priority-projects.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Manage Priority Projects</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('home_banners')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.home-banners.index') }}" class="dash-nav-link {{ request()->routeIs('admin.home-banners.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Home banners (carousel)</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('certificates')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.certificates.index') }}" class="dash-nav-link {{ request()->routeIs('admin.certificates.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Certificates</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('users')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.users.index') }}" class="dash-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Users</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('members')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.members.index') }}" class="dash-nav-link {{ request()->routeIs('admin.members.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Members</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('static_pages')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.static-pages.index') }}" class="dash-nav-link {{ request()->routeIs('admin.static-pages.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Manage Help &amp; legal pages</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('analytics')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.analytics.index') }}" class="dash-nav-link {{ request()->routeIs('admin.analytics.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Analytics &amp; reports</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('dialogue')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.dialogue.index') }}" class="dash-nav-link {{ request()->routeIs('admin.dialogue.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Dialogue</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('audit_logs')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.audit-logs.index') }}" class="dash-nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Audit logs</span>
                            </a>
                        </li>
                        @endcanAccessSection
                        @canAccessSection('roles')
                        <li class="dash-nav-item">
                            <a href="{{ route('admin.roles.index') }}" class="dash-nav-link {{ request()->routeIs('admin.roles.*') ? 'is-active' : '' }}">
                                <span class="dot"></span>
                                <span>Roles</span>
                            </a>
                        </li>
                        @endcanAccessSection
                    </ul>
                </div>
                @endif

                <div class="dash-sidebar-footer">
                    <div class="dash-user">
                        <div class="dash-user-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="dash-user-meta">
                            <div>{{ auth()->user()->name }} {{ auth()->user()->surname }}</div>
                            <small>{{ auth()->user()->email }}</small>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dash-btn-ghost">Logout</button>
                    </form>
                </div>
            </aside>

            <main id="main-content" class="dash-main" tabindex="-1">
                <div class="dash-topbar">
                    <div>
                        <div style="font-size:0.8rem;color:var(--text-muted);">Dashboard</div>
                        <div style="font-size:1.1rem;font-weight:600;">@yield('page_heading', 'Overview')</div>
                    </div>
                    <div class="dash-search">
                        <span class="dash-search-icon">🔍</span>
                        <input type="text" id="dash-quick-search" name="q" placeholder="Search users, courses, sections, library, certificates…" autocomplete="off" aria-label="Quick search across users, courses, sections, library, and certificates">
                        <div class="dash-search-menu" id="dash-search-menu" role="listbox" aria-label="Quick search results"></div>
                    </div>
                    <div class="dash-kpis">
                        <div class="dash-topbar-actions">
                            <button
                                type="button"
                                class="dash-icon-btn"
                                id="dash-bell-btn"
                                aria-label="Notifications"
                                aria-haspopup="menu"
                                aria-expanded="false"
                                data-latest-audit-id="{{ (int) ($dashBellLatestAuditId ?? 0) }}"
                                data-unread-count="{{ (int) ($dashBellUnreadCount ?? 0) }}"
                            >
                                <x-icons.workflow-icon key="system.bell" size="18" />
                                @if (($dashBellUnreadCount ?? 0) > 0)
                                    <span class="dash-icon-badge" aria-hidden="true" id="dash-bell-badge">{{ (int) $dashBellUnreadCount }}</span>
                                @endif
                            </button>
                            <div class="dash-bell-menu" id="dash-bell-menu" role="menu" aria-label="Notifications menu">
                                <div class="dash-bell-title">
                                    Admin updates
                                    @if (($dashBellUnreadCount ?? 0) > 0)
                                        <span style="color:var(--zanupf-gold);margin-left:0.4rem;">({{ (int) $dashBellUnreadCount }} new)</span>
                                    @endif
                                </div>
                                @if (!empty($dashBellActivities))
                                    @foreach ($dashBellActivities as $it)
                                        <a class="dash-bell-item" href="{{ $it['url'] }}" role="menuitem">
                                            <div>
                                                <div style="font-weight:600;">{{ $it['title'] }}</div>
                                                <small>{{ $it['subtitle'] }} @if(!empty($it['when'])) • {{ $it['when'] }} @endif</small>
                                            </div>
                                            <span style="color:var(--text-muted);">›</span>
                                        </a>
                                    @endforeach
                                @else
                                    <div style="padding:0.6rem 0.65rem;color:var(--text-muted);font-size:0.82rem;">
                                        No recent activity yet.
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="dash-icon-btn" id="dash-gear-btn" aria-label="Dashboard settings" aria-haspopup="dialog" aria-expanded="false">
                                <x-icons.workflow-icon key="system.gear" size="18" />
                            </button>
                        </div>
                        <button type="button" class="dash-mode-toggle" id="theme-toggle">
                            <span>Theme</span>
                            <span class="dash-mode-toggle-pill" data-mode="dark">Dark</span>
                            <span class="dash-mode-toggle-pill" data-mode="light">Light</span>
                        </button>
                        <div class="dash-kpi-pill">
                            <span class="dash-kpi-label">Constitution articles</span>
                            <span class="dash-kpi-value">@yield('kpi_articles', '—')</span>
                        </div>
                        <div class="dash-kpi-pill">
                            <span class="dash-kpi-label">Active learners</span>
                            <span class="dash-kpi-value">@yield('kpi_learners', '—')</span>
                        </div>
                    </div>
                </div>

                <div class="dash-main-content">
                    @yield('content')
                </div>

                <footer class="dash-footer" role="contentinfo">
                    <div>© 2026, <strong>Created by TTM Group</strong>.</div>
                    <div class="dash-footer-links">
                        <a href="{{ route('legal.privacy') }}">Privacy policy</a>
                        <a href="{{ route('legal.terms') }}">Terms of use</a>
                        <a href="{{ route('legal.cookies') }}">Cookies</a>
                        <a href="{{ route('admin.guide.help') }}">Help</a>
                    </div>
                </footer>
            </main>
        </div>

        <div class="dash-drawer-overlay" id="dash-drawer-overlay" aria-hidden="true"></div>
        <aside class="dash-drawer" id="dash-drawer" role="dialog" aria-modal="true" aria-label="Dashboard settings">
            <div class="dash-drawer-header">
                <div>
                    <div class="dash-drawer-title">Dashboard settings</div>
                    <div class="dash-drawer-subtitle">Quick actions and layout controls for administrators.</div>
                </div>
                <button type="button" class="dash-icon-btn" id="dash-drawer-close" aria-label="Close settings">
                    <span style="font-size:18px;line-height:1;">×</span>
                </button>
            </div>
            <div class="dash-drawer-body">
                <div class="dash-drawer-section">
                    <h3>Quick actions</h3>
                    <div class="dash-quick-links">
                        @canAccessSection('academy')
                            <a class="dash-quick-link" href="{{ route('admin.academy.index') }}">Manage Academy</a>
                        @endcanAccessSection
                        @canAccessSection('library')
                            <a class="dash-quick-link" href="{{ route('admin.library.index') }}">Manage Library</a>
                        @endcanAccessSection
                        @canAccessSection('dialogue')
                            <a class="dash-quick-link" href="{{ route('admin.dialogue.index') }}">Manage Dialogue</a>
                        @endcanAccessSection
                        @canAccessSection('users')
                            <a class="dash-quick-link" href="{{ route('admin.users.index') }}">Manage Users</a>
                        @endcanAccessSection
                        @canAccessSection('analytics')
                            <a class="dash-quick-link" href="{{ route('admin.analytics.index') }}">Analytics</a>
                        @endcanAccessSection
                        @canAccessSection('audit_logs')
                            <a class="dash-quick-link" href="{{ route('admin.audit-logs.index') }}">Audit logs</a>
                        @endcanAccessSection
                    </div>
                </div>

                <div class="dash-drawer-section">
                    <h3>Layout</h3>
                    <div class="dash-toggle-row">
                        <div>
                            <div style="font-weight:600;">Compact tiles</div>
                            <small>Fit more panels on one screen.</small>
                        </div>
                        <input type="checkbox" class="dash-switch" id="dash-toggle-compact" />
                    </div>
                    <div class="dash-toggle-row">
                        <div>
                            <div style="font-weight:600;">Show KPI pills</div>
                            <small>Constitution + learner snapshot in the header.</small>
                        </div>
                        <input type="checkbox" class="dash-switch" id="dash-toggle-kpis" />
                    </div>
                </div>

                <div class="dash-drawer-section">
                    <h3>Account</h3>
                    <div style="font-size:0.9rem;font-weight:700;">{{ auth()->user()->name }} {{ auth()->user()->surname }}</div>
                    <div style="font-size:0.82rem;color:var(--text-muted);margin-top:0.2rem;">{{ auth()->user()->email }}</div>
                    <div style="margin-top:0.6rem;font-size:0.82rem;color:var(--text-muted);">
                        Access in this console is determined by your assigned admin roles and allowed sections.
                    </div>
                </div>

                <div class="dash-drawer-section">
                    <h3>Help</h3>
                    <div class="dash-quick-links" style="grid-template-columns: 1fr;">
                        <a class="dash-quick-link" href="{{ route('admin.guide.documentation') }}">Documentation</a>
                        <a class="dash-quick-link" href="{{ route('admin.guide.settings') }}">Settings</a>
                        <a class="dash-quick-link" href="{{ route('admin.guide.help') }}">Help</a>
                        <a class="dash-quick-link" href="{{ route('admin.guide.faq') }}">FAQ</a>
                    </div>
                </div>

                <div class="dash-drawer-section">
                    <h3>Session</h3>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dash-quick-link" style="width:100%;justify-content:center;border-color:rgba(185,28,28,0.45);">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const body = document.body;
                const toggle = document.getElementById('theme-toggle');
                const stored = localStorage.getItem('zanupf-theme');
                let current = stored === 'light' ? 'light' : 'dark';

                function applyTheme(mode) {
                    body.classList.remove('theme-dark', 'theme-light');
                    body.classList.add('theme-' + mode);
                    document.querySelectorAll('.dash-mode-toggle-pill').forEach(pill => {
                        pill.classList.toggle('is-active', pill.dataset.mode === mode);
                    });
                }

                applyTheme(current);

                if (toggle) {
                    toggle.addEventListener('click', function () {
                        current = current === 'dark' ? 'light' : 'dark';
                        localStorage.setItem('zanupf-theme', current);
                        applyTheme(current);
                    });
                }

                // Drawer + topbar actions (admin dashboard only)
                const gearBtn = document.getElementById('dash-gear-btn');
                const drawer = document.getElementById('dash-drawer');
                const overlay = document.getElementById('dash-drawer-overlay');
                const closeBtn = document.getElementById('dash-drawer-close');
                const bellBtn = document.getElementById('dash-bell-btn');
                const bellMenu = document.getElementById('dash-bell-menu');
                const searchInput = document.getElementById('dash-quick-search');
                const searchMenu = document.getElementById('dash-search-menu');

                const compactToggle = document.getElementById('dash-toggle-compact');
                const kpiToggle = document.getElementById('dash-toggle-kpis');

                function setAriaExpanded(el, val) {
                    if (!el) return;
                    el.setAttribute('aria-expanded', val ? 'true' : 'false');
                }

                function openDrawer() {
                    if (!drawer || !overlay) return;
                    overlay.classList.add('is-open');
                    drawer.classList.add('is-open');
                    overlay.setAttribute('aria-hidden', 'false');
                    setAriaExpanded(gearBtn, true);
                    closeBell();
                }
                function closeDrawer() {
                    if (!drawer || !overlay) return;
                    overlay.classList.remove('is-open');
                    drawer.classList.remove('is-open');
                    overlay.setAttribute('aria-hidden', 'true');
                    setAriaExpanded(gearBtn, false);
                }
                function toggleDrawer() {
                    if (!drawer) return;
                    drawer.classList.contains('is-open') ? closeDrawer() : openDrawer();
                }

                function openBell() {
                    if (!bellMenu || !bellBtn) return;
                    bellMenu.classList.add('is-open');
                    setAriaExpanded(bellBtn, true);

                    // Mark activity as read when the admin opens the menu.
                    const latestId = Number(bellBtn.dataset.latestAuditId || '0');
                    const unread = Number(bellBtn.dataset.unreadCount || '0');
                    if (latestId > 0 && unread > 0) {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        fetch("{{ route('admin.activity.seen') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token || '',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ last_seen_audit_log_id: latestId }),
                            credentials: 'same-origin',
                        }).then(() => {
                            const badge = document.getElementById('dash-bell-badge');
                            if (badge) badge.remove();
                            bellBtn.dataset.unreadCount = '0';
                        }).catch(() => { /* ignore */ });
                    }
                }
                function closeBell() {
                    if (!bellMenu || !bellBtn) return;
                    bellMenu.classList.remove('is-open');
                    setAriaExpanded(bellBtn, false);
                }
                function toggleBell() {
                    if (!bellMenu) return;
                    bellMenu.classList.contains('is-open') ? closeBell() : openBell();
                }

                // Quick search (DB-backed)
                let searchTimer = null;
                let lastSearchToken = 0;

                function openSearchMenu() {
                    if (!searchMenu) return;
                    searchMenu.classList.add('is-open');
                }
                function closeSearchMenu() {
                    if (!searchMenu) return;
                    searchMenu.classList.remove('is-open');
                }

                function escapeHtml(s) {
                    return String(s)
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function renderSearch(json) {
                    if (!searchMenu) return;
                    const groups = json?.data?.groups || [];
                    if (!Array.isArray(groups) || groups.length === 0) {
                        searchMenu.innerHTML = '<div class="dash-search-empty">No results.</div>';
                        openSearchMenu();
                        return;
                    }

                    let html = '';
                    for (const g of groups) {
                        html += `<div class="dash-search-group-title">${escapeHtml(g.title || '')}</div>`;
                        const items = Array.isArray(g.items) ? g.items : [];
                        for (const it of items) {
                            const label = escapeHtml(it.label || '');
                            const meta = escapeHtml(it.meta || '');
                            const url = String(it.url || '#');
                            html += `<a class="dash-search-item" href="${url}" role="option">
                              <div>
                                <div style="font-weight:600;">${label}</div>
                                ${meta ? `<small>${meta}</small>` : ''}
                              </div>
                              <span style="color:var(--text-muted);">›</span>
                            </a>`;
                        }
                    }
                    searchMenu.innerHTML = html;
                    openSearchMenu();
                }

                async function runQuickSearch(query) {
                    const token = ++lastSearchToken;
                    const url = new URL("{{ route('admin.quick-search') }}", window.location.origin);
                    url.searchParams.set('q', query);
                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                    if (!res.ok) return;
                    const json = await res.json();
                    if (token !== lastSearchToken) return;
                    renderSearch(json);
                }

                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        const q = (searchInput.value || '').trim();
                        if (searchTimer) window.clearTimeout(searchTimer);
                        if (q.length < 2) {
                            closeSearchMenu();
                            return;
                        }
                        searchTimer = window.setTimeout(() => {
                            runQuickSearch(q).catch(() => { /* ignore */ });
                        }, 280);
                    });
                    searchInput.addEventListener('focus', function () {
                        const q = (searchInput.value || '').trim();
                        if (q.length >= 2 && searchMenu && searchMenu.innerHTML.trim() !== '') {
                            openSearchMenu();
                        }
                    });
                    searchInput.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') {
                            closeSearchMenu();
                            searchInput.blur();
                        }
                    });
                }

                if (gearBtn) gearBtn.addEventListener('click', toggleDrawer);
                if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
                if (overlay) overlay.addEventListener('click', closeDrawer);

                if (bellBtn) bellBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleBell();
                });
                if (bellMenu) bellMenu.addEventListener('click', function (e) {
                    // let clicks on links work, but don't bubble to document close handler
                    e.stopPropagation();
                });

                // Persisted layout toggles
                function applyLayoutPrefs() {
                    const compact = localStorage.getItem('zanupf-dash-compact') === '1';
                    const showKpis = localStorage.getItem('zanupf-dash-show-kpis');
                    const show = showKpis == null ? true : showKpis === '1';

                    body.classList.toggle('dash-compact', compact);
                    body.classList.toggle('dash-hide-kpis', !show);
                    if (compactToggle) compactToggle.checked = compact;
                    if (kpiToggle) kpiToggle.checked = show;
                }
                applyLayoutPrefs();

                if (compactToggle) {
                    compactToggle.addEventListener('change', function () {
                        localStorage.setItem('zanupf-dash-compact', compactToggle.checked ? '1' : '0');
                        applyLayoutPrefs();
                    });
                }
                if (kpiToggle) {
                    kpiToggle.addEventListener('change', function () {
                        localStorage.setItem('zanupf-dash-show-kpis', kpiToggle.checked ? '1' : '0');
                        applyLayoutPrefs();
                    });
                }

                // Global close behaviors
                document.addEventListener('click', function () {
                    closeBell();
                    closeSearchMenu();
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        closeBell();
                        closeDrawer();
                        closeSearchMenu();
                    }
                });
            });
        </script>
    </body>
</html>

