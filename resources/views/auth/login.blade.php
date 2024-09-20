<!doctype html>
<html class="no-js" lang="{{ str_replace('-', '_', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Đăng Nhập Tài Khoản</title>
    <meta name="description" content="{{ siteValue('description') }}">
    <meta name="keywords" content="{{ siteValue('keywords') }}">
    <meta name="author" content="{{ siteValue('author') }}">
    <meta name="robots" content="index, follow">

    <!-- [Open Graph] -->
    <meta property="og:title" content="{{ siteValue('title') }}">
    <meta property="og:description" content="{{ siteValue('description') }}">
    <meta property="og:image" content="{{ siteValue('thumbnail') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ siteValue('title') }}">
    <meta property="og:type" content="website">

    {{-- twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ siteValue('title') }}">
    <meta name="twitter:title" content="{{ siteValue('title') }}">
    <meta name="twitter:description" content="{{ siteValue('description') }}">
    <meta name="twitter:image" content="{{ siteValue('thumbnail') }}">
    <meta name="twitter:image:alt" content="{{ siteValue('title') }}">
    <meta name="twitter:creator" content="{{ siteValue('author') }}">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:domain" content="{{ url()->current() }}">
    <meta name="twitter:data1" content="{{ siteValue('author') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ siteValue('favicon') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/assets/pack-lbd/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="/assets/pack-lbd/css/fontawesome-all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="/assets/pack-lbd/font/flaticon.css">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/pack-lbd/css/style.css">
</head>

<body>
    <div id="preloader" class="preloader">
        <div class='inner'>
            <div class='line1'></div>
            <div class='line2'></div>
            <div class='line3'></div>
        </div>
    </div>
    <section class="fxt-template-animation fxt-template-layout34" data-bg-image="/assets/pack-lbd/img/elements/bg1.png">
        <div class="fxt-shape">
            <div class="fxt-transformX-L-50 fxt-transition-delay-1">
                <img src="/assets/pack-lbd/img/elements/shape1.png" alt="Shape">
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="fxt-column-wrap justify-content-between">
                        <div class="fxt-animated-img">
                            <div class="fxt-transformX-L-50 fxt-transition-delay-10">
                                <img src="/assets/pack-lbd/img/figure/bg34-1.png" alt="Animated Image">
                            </div>
                        </div>
                        <div class="fxt-transformX-L-50 fxt-transition-delay-3">
                            <a href="login-34.html" class="fxt-logo"><img src="{{ siteValue('logo') ?? '/assets/pack-lbd/img/logo-34.png' }}"
                                    alt="Logo"></a>
                        </div>
                        <div class="fxt-transformX-L-50 fxt-transition-delay-5">
                            <div class="fxt-middle-content">
                                <h1 class="fxt-main-title">Đăng Nhập Tài Khoản</h1>
                                <div class="fxt-switcher-description1">Nếu Bạn Chưa Có Tài Khoản Bạn Có Thể<a
                                        href="{{ route('register') }}" class="fxt-switcher-text ms-2">Đăng Kí</a></div>
                            </div>
                        </div>
                        <div class="fxt-transformX-L-50 fxt-transition-delay-7">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="fxt-column-wrap justify-content-center">
                        @if (session('error'))
                            <div class="alert alert-danger bg-danger text-white alert-dismissible fade show rounded"
                                role="alert">
                                <strong>Thông Báo: </strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success bg-success text-white alert-dismissible fade show"
                                role="alert">
                                <strong>Thông Báo: </strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-info bg-info text-white alert-dismissible fade show" role="alert">
                                <strong>Thông Báo: </strong> {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="fxt-form">
                            {{--  Allow Request All To Route(login.post) --}}
                            <form method="POST" action="{{ route('login.post', ['url' => request()->all()]) }}">
                                @csrf
                                <div class="form-group">
                                    <label for="username" class="input-label">Tài Khoản</label>
                                    <input type="text" id="username" class="form-control" name="username"
                                        value="{{ old('username') }}" autofocus placeholder="Nhập Tài Khoản">
                                </div>
                                <div class="form-group">
                                    <label for="password" class="input-label">Mật Khẩu</label>
                                    <input id="password" type="password" class="form-control" name="password"
                                        placeholder="********">
                                    <i toggle="#password" class="fa fa-fw fa-eye toggle-password field-icon"></i>
                                </div>

                                @if (session('two_factor_auth'))
                                    <div class="form-group">
                                        <label for="two_factor_code" class="input-label">Mã Xác Thực 2 Bước</label>
                                        <p class="text-danger">Lưu Ý: Phiên Này Sẽ Hết Hạn Sau: 3 Phút Nếu Bạn Không Đăng Nhập Thì Sẽ Tự Mất Xác Thực 2 Yếu Tố</p>
                                        {{-- Lưu Ý Phiên Này Thời Gian Hết Hạn --}}
                                        <input id="two_factor_code" type="text" class="form-control" name="code" autocomplete="off"
                                            placeholder="Nhập Mã Xác Thực 2 Bước">
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check d-flex align-items-center">
                                        <label class="form-check-label" id="remember">
                                            <input type="checkbox" class="form-check-input" id="remember"
                                                name="remember" {{ old('remember') ? 'checked' : 'checked' }}>
                                            Ghi Nhớ Tài Khoản
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <div class="fxt-switcher-description2 text-right">
                                            <a href="#" class="fxt-switcher-text">Bạn Quên Mật Khẩu</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="fxt-btn-fill">Đăng Nhập Tài Khoản</button>
                                </div>
                            </form>
                        </div>
                        <div class="fxt-style-line">
                            <span>Or Continus With</span>
                        </div>
                        <ul class="fxt-socials">
                    <li class="fxt-google">
                        <a href="{{ route('auth.google') }}" title="google"><i class="fab fa-google-plus-g"></i></a>
                    </li>
                    <li class="fxt-apple">
                        <a href="#" title="apple"><i class="fab fa-apple"></i></a>
                    </li>
                    <li class="fxt-facebook">
                        <a href="{{ route('auth.facebook') }}" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    </li>
                </ul>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- jquery-->
    <script src="/assets/pack-lbd/js/jquery.min.js"></script>
    <!-- Bootstrap js -->
    <script src="/assets/pack-lbd/js/bootstrap.min.js"></script>
    <!-- Imagesloaded js -->
    <script src="/assets/pack-lbd/js/imagesloaded.pkgd.min.js"></script>
    <!-- Validator js -->
    <script src="/assets/pack-lbd/js/validator.min.js"></script>
    <!-- Custom Js -->
    <script src="/assets/pack-lbd/js/main.js"></script>

</body>

</html>
