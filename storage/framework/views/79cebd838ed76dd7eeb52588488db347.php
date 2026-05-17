

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h3 class="page-title"> Manajemen Kategori </h3>
</div>

<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Kategori Baru</h4>
                <form class="forms-sample" id="formKategori" action="<?php echo e(route('kategori.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="namaKategori">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" id="namaKategori" placeholder="Contoh: Novel" required>
                    </div>
                </form>

                <button type="button" id="btnSimpanKategori" class="btn btn-gradient-primary me-2" onclick="submitKategori()">Simpan</button>

                <script>
                function submitKategori() {
                    const form = document.getElementById('formKategori');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    const btn = document.getElementById('btnSimpanKategori');
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
                    form.submit();
                }
                </script>
            </div>
        </div>
    </div>

    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Kategori</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $data_kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($kategori->nama_kategori); ?></td>
                                <td>
                                    <a href="<?php echo e(route('kategori.edit', $kategori->idkategori)); ?>" class="mb-1 btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                     <form action="<?php echo e(route('kategori.destroy', $kategori->idkategori)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="mb-1 btn btn-sm btn-danger">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>  
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data kategori.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\koleksi-buku\resources\views/pages/kategori.blade.php ENDPATH**/ ?>