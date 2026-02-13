@include('layouts.header')

      <body>
        <div class="container-scroller">
          @include('layouts.navbar')
          <div class="container-fluid page-body-wrapper">
            @include('layouts.sidebar')
            <div class="main-panel">
              <div class="content-wrapper">
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> Periksa kembali inputan Anda.
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @yield('content')
</div>
            @include('layouts.footer')
        </div>
        </div>
        </div>
        @include('layouts.scripts')
      </body>