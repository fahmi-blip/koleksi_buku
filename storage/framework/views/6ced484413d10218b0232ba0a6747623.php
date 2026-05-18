

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h3 class="page-title">Barcode Barang</h3>
    <a href="<?php echo e(route('barang.index')); ?>" class="btn btn-light">Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title mb-3"><?php echo e($barang->nama); ?></h4>
                <p class="mb-4">
                    ID Barang: <span class="badge badge-outline-info"><?php echo e($barang->id_barang); ?></span>
                </p>

                <div class="d-flex justify-content-center align-items-center overflow-auto">
                    <?php echo $barcodeHtml; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\koleksi-buku\resources\views\pages\barcode.blade.php ENDPATH**/ ?>