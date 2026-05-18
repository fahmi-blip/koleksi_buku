<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode <?php echo e($toko->name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 32px;
        }
        .card {
            max-width: 460px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
        }
        .meta {
            margin: 12px 0 20px;
            color: #444;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .card { border: none; }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2><?php echo e($toko->name); ?></h2>
        <div class="meta">Barcode: <?php echo e($toko->barcode); ?></div>
        <div style="display:flex; justify-content:center; overflow:auto; margin-bottom:18px;">
            <?php echo $barcodeHtml; ?>

        </div>               
        <button class="no-print" onclick="window.print()">Cetak Ulang</button>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\koleksi-buku\resources\views\pages\toko_barcode.blade.php ENDPATH**/ ?>