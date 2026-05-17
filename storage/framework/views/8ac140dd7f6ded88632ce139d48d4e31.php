<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Sistem Antrian')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #08111f;
            --bg-soft: rgba(10, 19, 33, 0.78);
            --panel: rgba(255, 255, 255, 0.06);
            --panel-strong: rgba(255, 255, 255, 0.1);
            --text: #e5eefc;
            --muted: #99a9c2;
            --accent: #4cc9f0;
            --accent-2: #f59e0b;
            --success: #22c55e;
            --border: rgba(148, 163, 184, 0.18);
        }

        * { box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(76, 201, 240, 0.18), transparent 30%),
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.16), transparent 28%),
                linear-gradient(180deg, #020814 0%, #08111f 45%, #0b1729 100%);
        }

        a { color: inherit; text-decoration: none; }
        .shell {
            width: min(700px, calc(100% - 32px));
            margin: 0 auto;
            padding: 22px 0 36px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 22px;
        }
        .brand { font-size: 1.05rem; font-weight: 800; letter-spacing: 0.02em; }
        .brand small { display: block; margin-top: 4px; color: var(--muted); font-size: 0.82rem; font-weight: 500; }
        .chip-row { display: flex; flex-wrap: wrap; gap: 10px; }
        .chip {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.04);
        }
        .chip.primary {
            border-color: transparent;
            background: linear-gradient(135deg, var(--accent), #2563eb);
            color: #fff;
        }
        .panel {
            border: 1px solid var(--border);
            border-radius: 28px;
            background: linear-gradient(180deg, var(--bg-soft), rgba(11, 23, 41, 0.56));
            box-shadow: 0 24px 80px rgba(2, 6, 23, 0.34);
            overflow: hidden;
        }
        .panel-inner { padding: 20px; }
        .eyebrow {
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.18em;
            font-size: 0.76rem;
            margin-bottom: 10px;
        }
        .title {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.8rem);
            line-height: 0.98;
        }
        .lead {
            margin-top: 14px;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
        }
        .content { margin-top: 20px; }
        .alert {
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 18px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.05);
        }
        .alert.success { border-color: rgba(34, 197, 94, 0.3); }
        .alert.error { border-color: rgba(239, 68, 68, 0.3); }
        .card {
            border: 1px solid var(--border);
            border-radius: 24px;
            background: var(--panel);
            backdrop-filter: blur(14px);
        }
        .card-body { padding: 20px; }
        .btn-soft {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.06);
            color: var(--text);
            cursor: pointer;
        }
        .btn-soft.primary {
            border-color: transparent;
            background: linear-gradient(135deg, var(--accent), #2563eb);
            color: #fff;
        }
        .field {
            width: 100%;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.05);
            color: var(--text);
            padding: 13px 14px;
            outline: none;
        }
        .field:focus { border-color: rgba(76, 201, 240, 0.5); }
        .label { display: block; margin-bottom: 8px; color: var(--muted); font-size: 0.92rem; }
        @media (max-width: 760px) {
            .topbar { flex-direction: column; align-items: flex-start; }
            .panel-inner { padding: 18px; }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="shell">
        <div class="topbar">
            <div class="brand">
                Antrian Toko Buku
            </div>
            <div class="chip-row">
                <a class="chip primary" href="<?php echo e(route('antrian.guest')); ?>">Daftar Antrian</a>
                <a class="chip" href="<?php echo e(route('antrian.papan')); ?>">Papan Antrian</a>
            </div>
        </div>

        <?php if($message = Session::get('success')): ?>
            <div class="alert success"><?php echo e($message); ?></div>
        <?php endif; ?>

        <?php if($message = Session::get('error')): ?>
            <div class="alert error"><?php echo e($message); ?></div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\koleksi-buku\resources\views/layouts/antrian.blade.php ENDPATH**/ ?>