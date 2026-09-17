<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Reasuransi Excel')</title>
    <style>
        :root {
            --bg: #f4f6fb;
            --card: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #1d4ed8;
            --primary-dark: #1e40af;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: var(--bg); color: var(--text); }
        .navbar { background: #0f172a; color: #fff; }
        .navbar-inner { max-width: 1100px; margin: 0 auto; padding: 0.9rem 1.25rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
        .brand { font-weight: 700; font-size: 1.05rem; color: #fff; text-decoration: none; white-space: nowrap; }
        .brand small { display: block; font-weight: 400; font-size: 0.72rem; color: #94a3b8; }
        .nav-links { display: flex; gap: 0.35rem; flex-wrap: wrap; margin-left: auto; }
        .nav-links a { color: #cbd5e1; text-decoration: none; font-size: 0.9rem; padding: 0.45rem 0.8rem; border-radius: 8px; }
        .nav-links a:hover { background: #1e293b; color: #fff; }
        .nav-links a.active { background: var(--primary); color: #fff; }
        .container { max-width: 1100px; margin: 0 auto; padding: 1.75rem 1.25rem 3rem; }
        .page-head { margin-bottom: 1.25rem; }
        .page-head h1 { margin: 0 0 0.35rem; font-size: 1.5rem; }
        .page-head p { margin: 0; color: var(--muted); font-size: 0.95rem; }
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 1.1rem 1.2rem; }
        .card h3 { margin: 0 0 0.4rem; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--muted); }
        .card .value { font-size: 1.45rem; font-weight: 700; }
        .card .sub { color: var(--muted); font-size: 0.82rem; margin-top: 0.25rem; }
        .panel { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; margin-top: 1.25rem; }
        .panel h2 { margin: 0 0 0.6rem; font-size: 1.05rem; }
        .panel p, .panel li { font-size: 0.93rem; color: #334155; }
        .table-wrap { overflow-x: auto; margin-top: 1rem; border: 1px solid var(--border); border-radius: 12px; background: var(--card); }
        table { width: 100%; border-collapse: collapse; font-size: 0.88rem; min-width: 760px; }
        th, td { padding: 0.65rem 0.8rem; border-bottom: 1px solid var(--border); text-align: left; white-space: nowrap; }
        th { background: #f8fafc; color: var(--muted); font-weight: 600; font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.04em; }
        tr:last-child td { border-bottom: none; }
        td.num { text-align: right; font-variant-numeric: tabular-nums; }
        .empty { padding: 2rem 1.25rem; text-align: center; color: var(--muted); }
        .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.78rem; background: #eef2ff; color: var(--primary-dark); border: 1px solid #c7d2fe; }
        .btn { display: inline-block; padding: 0.6rem 1.1rem; border-radius: 9px; border: 1px solid var(--border); background: var(--primary); color: #fff; text-decoration: none; font-size: 0.9rem; cursor: pointer; }
        .btn[disabled] { opacity: 0.55; cursor: not-allowed; }
        .btn-secondary { background: #fff; color: var(--text); }
        .page-head-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .alert { border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.9rem; }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-error ul { margin: 0.35rem 0 0; padding-left: 1.1rem; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .btn-row { display: flex; gap: 0.6rem; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; gap: 0.3rem; }
        .form-group label { font-size: 0.83rem; font-weight: 600; color: #334155; }
        .form-group input { border: 1px solid var(--border); border-radius: 8px; padding: 0.55rem 0.7rem; font-size: 0.9rem; }
        .field-error { font-size: 0.8rem; color: #b91c1c; }
        .form-actions { display: flex; gap: 0.6rem; justify-content: flex-end; margin-top: 1.25rem; }
        td.actions { white-space: nowrap; }
        td.actions form { display: inline; }
        .link { background: none; border: none; padding: 0; color: var(--primary); font-size: 0.85rem; cursor: pointer; text-decoration: none; }
        .link:hover { text-decoration: underline; }
        .link-danger { color: #b91c1c; margin-left: 0.6rem; }
        .footer { text-align: center; color: var(--muted); font-size: 0.8rem; padding: 1.5rem 0 2rem; }
        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 560px) {
            .grid { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .form-actions { justify-content: stretch; flex-direction: column-reverse; }
            .form-actions .btn { text-align: center; }
            .nav-links { margin-left: 0; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <a class="brand" href="{{ route('dashboard') }}">Reasuransi Excel<small>Laporan Produksi &amp; Klaim</small></a>
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('produksi.index') }}" class="{{ request()->routeIs('produksi.*') ? 'active' : '' }}">Produksi &amp; Premi</a>
                <a href="{{ route('klaim.index') }}" class="{{ request()->routeIs('klaim.*') ? 'active' : '' }}">Klaim</a>
                <a href="{{ route('export.index') }}" class="{{ request()->routeIs('export.*') ? 'active' : '' }}">Export Excel</a>
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <div class="footer">Aplikasi laporan reasuransi &mdash; Phase 1 fondasi (Laravel 13, MySQL).</div>
</body>
</html>
