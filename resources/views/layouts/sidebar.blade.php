<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
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

    <li class="nav-item {{ Request::is('dokumen/sertifikat*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('sertifikat') }}" target="_blank">
        <span class="menu-title">Sertifikat (PDF)</span>
        <i class="mdi mdi-certificate menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ Request::is('dokumen/surat*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('pengumuman') }}" target="_blank">
        <span class="menu-title">Surat Resmi (PDF)</span>
        <i class="mdi mdi-certificate menu-icon"></i>
      </a>
    </li>
  </ul>
</nav>