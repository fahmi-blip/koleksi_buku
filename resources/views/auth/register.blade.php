<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register - Koleksi Buku</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="flex-grow row">
            <div class="mx-auto col-lg-4">
              <div class="p-5 text-left auth-form-light">
                <div class="brand-logo">
                  <img src="{{ asset('assets/images/logo.svg') }}">
                </div>
                <h4>Baru di sini?</h4>
                <h6 class="font-weight-light">Mendaftar itu mudah. Hanya butuh beberapa langkah.</h6>
                
                <form class="pt-3" method="POST" action="{{ route('register') }}">
                  @csrf

                  <div class="form-group">
                    <input type="text" name="name" class="form-control form-control-lg" id="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus autocomplete="name">
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger" />
                  </div>

                  <div class="form-group">
                    <input type="email" name="email" class="form-control form-control-lg" id="email" placeholder="Email" value="{{ old('email') }}" required autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                  </div>

                  <div class="form-group">
                    <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="Password" required autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                  </div>

                  <div class="form-group">
                    <input type="password" name="password_confirmation" class="form-control form-control-lg" id="password_confirmation" placeholder="Konfirmasi Password" required autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
                  </div>

                  <div class="mb-4">
                    <div class="form-check">
                      <label class="form-check-label text-muted">
                        <input type="checkbox" class="form-check-input" required> Saya setuju dengan semua Syarat & Ketentuan </label>
                    </div>
                  </div>

                  <div class="gap-2 mt-3 d-grid">
                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                        DAFTAR
                    </button>
                  </div>

                  <div class="mt-4 text-center font-weight-light"> 
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-primary">Login</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
  </body>
</html>