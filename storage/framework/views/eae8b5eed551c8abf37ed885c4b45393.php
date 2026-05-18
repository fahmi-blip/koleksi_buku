

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h3 class="page-title"> Edit Kategori </h3>
    <a href="<?php echo e(route('kategori.index')); ?>" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Kategori: <?php echo e($kategori->judul); ?></h4>
                <form class="forms-sample" id="formEditKategori" action="<?php echo e(route('kategori.update', $kategori->idkategori)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" value="<?php echo e(old('nama_kategori', $kategori->nama_kategori)); ?>" required>
                    </div>
                </form>

                <button type="button" id="btnUpdateKategori" class="btn btn-gradient-primary me-2" onclick="submitEditKategori()">Update Kategori</button>

                <script>
                function submitEditKategori() {
                    const form = document.getElementById('formEditKategori');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    const btn = document.getElementById('btnUpdateKategori');
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
                    form.submit();
                }
                </script>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\koleksi-buku\resources\views\pages\kategori\edit.blade.php ENDPATH**/ ?>