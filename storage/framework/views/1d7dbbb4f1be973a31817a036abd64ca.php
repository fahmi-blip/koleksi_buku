

<?php $__env->startSection('content'); ?>
<section class="panel">
    <div class="panel-inner" style="text-align:center;padding:48px 24px;">
        <div class="eyebrow">Tiket antrian</div>
        <div style="font-size:clamp(4rem, 14vw, 8rem); font-weight:800; line-height:0.95; color:#ffd86b; text-shadow:0 0 30px rgba(255, 216, 107, 0.25);">
            <?php echo e(str_pad($antrian->nomor,3,'0',STR_PAD_LEFT)); ?>

        </div>
        <div style="margin-top:18px; font-size:clamp(1.5rem, 4vw, 2.6rem); font-weight:700;"><?php echo e($antrian->name); ?></div>
        <div style="margin-top:10px; color:var(--muted); font-size:1rem;">Tunjukkan halaman ini saat dipanggil.</div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Auto-print ticket on load, then redirect back to registration page
(function(){
    const returnUrl = "<?php echo e(route('antrian.guest')); ?>";

    function redirectBack(){
        try{ window.location.href = returnUrl; }catch(e){}
    }

    function doPrint(){
        try{ window.print(); }catch(e){}

        if (typeof window.onafterprint !== 'undefined') {
            window.onafterprint = redirectBack;
        } else {
            // fallback: redirect after small delay
            setTimeout(redirectBack, 1200);
        }
    }

    window.addEventListener('load', function(){
        // small delay to ensure fonts/assets loaded
        setTimeout(doPrint, 350);
    });

    // Provide keyboard shortcut: press 'r' to return immediately
    window.addEventListener('keydown', function(e){ if (e.key === 'r') redirectBack(); });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.antrian', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\koleksi-buku\resources\views\antrian\ticket.blade.php ENDPATH**/ ?>