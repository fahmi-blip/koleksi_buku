<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Koleksi Buku</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth auth-bg-1 lg-res-padding">
                <div class="row w-100">
                    <div class="mx-auto col-lg-4">
                        <div class="p-5 text-left auth-form-light">
                            <div class="brand-logo">
                                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo">
                            </div>
                            <h4>Halo! Mari mulai</h4>
                            <h6 class="font-weight-light">Masuk untuk melanjutkan.</h6>

                            @if (session('status'))
                                <div class="mb-4 alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form class="pt-3" method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" placeholder="Password" required autocomplete="current-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        MASUK
                                    </button>
                                </div>

                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" name="remember" class="form-check-input"> Biarkan saya tetap masuk
                                        </label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-black auth-link">Lupa password?</a>
                                    @endif
                                </div>

                                <div class="mt-4 text-center font-weight-light">
                                    Belum punya akun? <a href="{{ route('register') }}" class="text-primary">Buat Akun</a>
                                </div>

                                <div class="mt-4">
                                    <div class="mb-3 d-flex align-items-center">
                                        <hr class="flex-grow-1">
                                        <span class="mx-2 text-muted">atau</span>
                                        <hr class="flex-grow-1">
                                    </div>
                                    <a href="{{ route('auth.google') }}" class="btn btn-block btn-danger btn-lg font-weight-medium">
                                        <i class="mr-2 mdi mdi-google"></i> Login dengan Google
                                    </a>
                                </div>

                                @if (session('error'))
                                    <div class="mt-3 alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
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
</body>
</html>