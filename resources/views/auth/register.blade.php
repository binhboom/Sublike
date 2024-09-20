<!doctype html>
<html class="no-js" lang="{{ str_replace('-', '_', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Đăng Kí Tài Khoản</title>

    <!-- [SEO] -->
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
    <meta name="twitter:label1" content="Written By Lương Bình Dương">
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
    <!--[if lt IE 8]>
        <p class="browserupgrade">You Are Using An <strong>Outdated</strong> Browser. Please <a href="http://browsehappy.com/">Upgrade Your Browser</a> To Improve Your Experience.</p>
    <![endif]-->
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
                            <a href="{{ route('register') }}" class="fxt-logo"><img src="{{ siteValue('logo') ?? '/assets/pack-lbd/img/logo-34.png' }}"
                                    alt="Logo"></a>
                        </div>
                        <div class="fxt-transformX-L-50 fxt-transition-delay-5">
                            <div class="fxt-middle-content">
                                <h1 class="fxt-main-title">Đăng Kí Tài Khoản</h1>
                                <div class="fxt-switcher-description1">Nếu Bạn Đã Có Tài Khoản?<a
                                        href="{{ route('login') }}" class="fxt-switcher-text ms-2">Đăng Nhập</a></div>
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

                        <div class="fxt-form">
                            <form method="POST" action="{{ route('register.post') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Họ Và Tên</label>
                                    <input type="text" id="name" class="form-control" name="name"
                                        placeholder="Nhập Họ Và Tên" value="{{ old('name') }}" autofocus
                                        autocomplete="name">
                                </div>
                                <div class="form-group">
                                    <label for="email">Địa Chỉ Email</label>
                                    <input type="text" id="email" class="form-control" name="email"
                                        placeholder="Nhập Địa Chỉ Email" value="{{ old('email') }}"
                                        autocomplete="email">
                                </div>
                                <div class="form-group">
                                    <label for="username" class="form-label">Tài Khoản</label>
                                    <input type="text" id="username" class="form-control" name="username"
                                        placeholder="Nhập Tên Tài Khoản" value="{{ old('username') }}"
                                        autocomplete="username">
                                </div>
                                <div class="form-group">
                                    <label for="password" class="input-label">Mật Khẩu</label>
                                    <input id="password" type="password" class="form-control" name="password"
                                        placeholder="********">
                                    <i toggle="#password" class="fa fa-fw fa-eye toggle-password field-icon"></i>
                                </div>
                                <div class="form-group">
                                    <div class="fxt-checkbox-box">
                                        <input id="checkbox1" type="checkbox" checked>
                                        <label for="checkbox1" class="ps-4">I Agree With <a class="terms-link"
                                                href="#">Terms</a> And <a class="terms-link"
                                                href="#">Privacy Policy</a></label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="fxt-btn-fill">Đăng Kí Tài Khoản</button>
                                </div>
                            </form>
                        </div>
                        <div class="fxt-style-line">
                            <span>Or Continus With</span>
                        </div>
                        <ul class="fxt-socials">
                            <li class="fxt-google">
                                <a href="#" title="Google"><i class="fab fa-google-plus-g"></i></a>
                            </li>
                            <li class="fxt-apple">
                                <a href="#" title="Apple"><i class="fab fa-apple"></i></a>
                            </li>
                            <li class="fxt-facebook">
                                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
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
