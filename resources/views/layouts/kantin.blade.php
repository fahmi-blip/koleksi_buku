<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kantin Online') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --panel: rgba(15, 23, 42, 0.72);
            --panel-2: rgba(255, 255, 255, 0.08);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --brand: #38bdf8;
            --brand-2: #f59e0b;
            --success: #22c55e;
            --danger: #ef4444;
            --border: rgba(148, 163, 184, 0.2);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Space Grotesk', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.24), transparent 30%),
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.2), transparent 26%),
                linear-gradient(180deg, #020617 0%, #0f172a 55%, #111827 100%);
        }

        a { color: inherit; text-decoration: none; }
        .shell { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
        .topbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 24px 0;
        }
        .brand { font-size: 1.1rem; font-weight: 700; letter-spacing: 0.04em; }
        .brand small { display: block; color: var(--muted); font-size: 0.78rem; font-weight: 500; letter-spacing: 0; }
        .top-actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .chip {
            padding: 10px 14px; border: 1px solid var(--border); border-radius: 999px;
            background: rgba(255,255,255,0.04); color: var(--text);
        }
        .chip.primary { background: linear-gradient(135deg, var(--brand), #2563eb); border-color: transparent; color: #fff; }
        .hero {
            padding: 28px; border: 1px solid var(--border); border-radius: 28px;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.66));
            box-shadow: 0 24px 80px rgba(2, 6, 23, 0.45);
            overflow: hidden;
        }
        .hero-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px; align-items: center; }
        .eyebrow { color: var(--brand); text-transform: uppercase; letter-spacing: 0.18em; font-size: 0.8rem; }
        h1 { margin: 12px 0 12px; font-size: clamp(2.2rem, 4vw, 4.3rem); line-height: 0.95; }
        .lead { color: var(--muted); font-size: 1.02rem; line-height: 1.7; max-width: 62ch; }
        .hero-card {
            padding: 20px; border-radius: 24px; border: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(56, 189, 248, 0.12), rgba(255,255,255,0.04));
        }
        .stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 20px; }
        .stat { padding: 16px; border-radius: 18px; background: rgba(255,255,255,0.04); border: 1px solid var(--border); }
        .stat strong { display: block; font-size: 1.2rem; }
        .stat span { color: var(--muted); font-size: 0.84rem; }
        .content { padding: 24px 0 48px; }
        .alert {
            padding: 14px 16px; border-radius: 16px; margin-bottom: 18px; border: 1px solid var(--border);
            background: rgba(255,255,255,0.05);
        }
        .alert.success { border-color: rgba(34, 197, 94, 0.3); }
        .alert.error { border-color: rgba(239, 68, 68, 0.3); }
        @media (max-width: 960px) {
            .hero-grid { grid-template-columns: 1fr; }
            .stats { grid-template-columns: 1fr; }
            .topbar { gap: 16px; flex-direction: column; align-items: flex-start; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="shell">
        <div class="topbar">
            <div class="brand">
                Kantin Online
                <small>Pesan tanpa login, bayar virtual account atau QRIS</small>
            </div>
            <div class="top-actions">
                <a class="chip primary" href="{{ route('kantin.order') }}">Mulai Pesan</a>
                <a class="chip primary" href="{{ route('kantin.vendor.scan-qr') }}">Vendor Scan</a>
            </div>
        </div>

        <section >
            <div >
                <div>
                    <div class="eyebrow">Mini aplikasi pemesanan kantin</div>
                    <h1>Pemesanan cepat, pembayaran rapi, status langsung lunas.</h1>
                    <p class="lead">Customer memilih vendor, memilih menu bertingkat, lalu menyelesaikan pembayaran dengan alur yang siap dipakai untuk virtual account atau QRIS. Admin mengelola menu vendor dan riwayat pesanan pending maupun lunas dari satu dashboard.</p>
                </div>
            </div>
        </section>

        <main class="content">
            @if ($message = Session::get('success'))
                <div class="alert success">{{ $message }}</div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert error">{{ $message }}</div>
            @endif

            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>