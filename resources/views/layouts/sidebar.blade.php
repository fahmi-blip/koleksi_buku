<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="assets/images/faces/face1.jpg" alt="profile" />
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="mb-2 font-weight-bold">{{ Auth::user()->name }}</span>
          <span class="text-secondary text-small">{{ Auth::user()->role }}</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    {{-- Menu Home --}}
    <li class="nav-item {{ Request::is('/') || Request::is('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('home') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    {{-- Menu Kategori --}}
    <li class="nav-item {{ Request::is('kategori*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('kategori.index') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-table-large menu-icon"></i>
      </a>
    </li>

    {{-- Menu Buku --}}
    <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('buku.index') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-variant menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ Request::is('dokumen/surat*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('pengumuman') }}" target="_blank">
        <span class="menu-title">Surat Resmi (PDF)</span>
        <i class="mdi mdi-certificate menu-icon"></i>
      </a>
    </li>

    {{-- Menu Barang --}}
    <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('barang.index') }}">
        <span class="menu-title">Barang & Label Harga</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
  </ul>
</nav>