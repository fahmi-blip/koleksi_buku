<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="<?php echo e(asset('assets/images/faces/face1.jpg')); ?>" alt="profile" />
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="mb-2 font-weight-bold"><?php echo e(Auth::user()->name); ?></span>
          <span class="text-secondary text-small"><?php echo e(Auth::user()->role); ?></span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <?php if(auth()->guard()->check()): ?>
    
       <?php if(Auth::user()->role == 'admin'): ?>
       <li class="nav-item <?php echo e(Request::is('/') || Request::is('dashboard') ? 'active' : ''); ?>">
        <a class="nav-link" href="<?php echo e(route('home')); ?>">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('antrian*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('antrian.admin')); ?>">
        <span class="menu-title">Manajemen Antrian</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>
    
    
    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('kategori*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('kategori.index')); ?>">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-table-large menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    
    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('buku*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('buku.index')); ?>">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-variant menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('dokumen/surat*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('pengumuman')); ?>" target="_blank">
        <span class="menu-title">Surat Resmi (PDF)</span>
        <i class="mdi mdi-certificate menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    
    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('pos*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('pos.index')); ?>">
        <span class="menu-title">Point of Sales</span>
        <i class="mdi mdi-cash-register menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('wilayah*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('wilayah')); ?>">
        <span class="menu-title">Wilayah</span>
        <i class="mdi mdi-cash-register menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('kunjungan-toko*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('kunjungan.index')); ?>">
        <span class="menu-title">Kunjungan Toko</span>
        <i class="mdi mdi-map-marker-radius menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>
    
    
    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(request()->routeIs('customer*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route("customer.index")); ?>">
        <span class="menu-title">Customer</span>
        <i class="mdi mdi-account-multiple menu-icon"></i>
      </a>

    </li>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('barang*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('barang.index')); ?>">
        <span class="menu-title">Barang & Label Harga</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('admin/vendor*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('admin.vendor.index')); ?>">
        <span class="menu-title">Master Vendor</span>
        <i class="mdi mdi-store menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('datatables*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('latihan.datatables')); ?>">
        <span class="menu-title">Datatables</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(request()->routeIs('latihan.table') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('latihan.table')); ?>">
        <span class="menu-title">Table</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('select*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('latihan.select')); ?>">
        <span class="menu-title">Select</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>
    
    <?php if(Auth::user()->role == 'admin'): ?>
    <li class="nav-item <?php echo e(Request::is('kantin*') ? 'active' : ''); ?>">
      <a class="nav-link" href="<?php echo e(route('kantin.order')); ?>">
        <span class="menu-title">Kantin</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>
    
    
    <?php endif; ?>
  </ul>
</nav><?php /**PATH C:\laragon\www\koleksi-buku\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>