<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
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
    @auth
    {{-- Menu Home --}}
       @if(Auth::user()->role == 'admin')
       <li class="nav-item {{ Request::is('/') || Request::is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    @endif

    {{-- Menu Kategori --}}
    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('kategori*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('kategori.index') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-table-large menu-icon"></i>
      </a>
    </li>
    @endif

    {{-- Menu Buku --}}
    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('buku.index') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-variant menu-icon"></i>
      </a>
    </li>
    @endif

    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('dokumen/surat*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('pengumuman') }}" target="_blank">
        <span class="menu-title">Surat Resmi (PDF)</span>
        <i class="mdi mdi-certificate menu-icon"></i>
      </a>
    </li>
    @endif

    {{-- Menu POS --}}
    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('pos*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('pos.index') }}">
        <span class="menu-title">Point of Sales</span>
        <i class="mdi mdi-cash-register menu-icon"></i>
      </a>
    </li>
    @endif

    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('wilayah*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('wilayah') }}">
        <span class="menu-title">Wilayah</span>
        <i class="mdi mdi-cash-register menu-icon"></i>
      </a>
    </li>
    @endif
    
    {{-- Menu Barang --}}
    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ request()->routeIs('customer*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route("customer.index") }}">
        <span class="menu-title">Customer</span>
        <i class="mdi mdi-account-multiple menu-icon"></i>
      </a>

    </li>
    @endif

    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('barang.index') }}">
        <span class="menu-title">Barang & Label Harga</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    @endif

    {{-- @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('qr-scanner*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('qr-scanner.index') }}">
        <span class="menu-title">QR Scanner</span>
        <i class="mdi mdi-qrcode-scan menu-icon"></i>
      </a>
    </li>
    @endif --}}

    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('admin/vendor*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.vendor.index') }}">
        <span class="menu-title">Master Vendor</span>
        <i class="mdi mdi-store menu-icon"></i>
      </a>
    </li>
    @endif

    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('datatables*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('latihan.datatables') }}">
        <span class="menu-title">Datatables</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    @endif

    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ request()->routeIs('latihan.table') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('latihan.table') }}">
        <span class="menu-title">Table</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    @endif

    @if(Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('select*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('latihan.select') }}">
        <span class="menu-title">Select</span>
        <i class="mdi mdi-tag-multiple menu-icon"></i>
      </a>
    </li>
    @endif
    @endauth
  </ul>
</nav>