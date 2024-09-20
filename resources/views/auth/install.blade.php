<!doctype html>
<html class="no-js" lang="{{ str_replace('-', '_', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Cài Đặt Website</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/assets/pack-lbd/img/favicon.png">
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
                            <a href="login-34.html" class="fxt-logo"><img src="/assets/pack-lbd/img/logo-34.png"
                                    alt="Logo"></a>
                        </div>
                        <div class="fxt-transformX-L-50 fxt-transition-delay-5">
                            <div class="fxt-middle-content">
                                <h1 class="fxt-main-title">Cài Đặt Website</h1>
                                <div class="fxt-switcher-description1">Bạn Cần Cài Đặt Website Để Tiếp Tục</div>
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
                            <form method="POST" action="{{ route('install.post') }}">
                                @csrf
                                @if (env('APP_MAIN_SITE') !== request()->getHost())
                                    <div class="form-group">
                                        <label for="api_token">Api Token</label>
                                        <input type="text" id="api_token" class="form-control" name="api_token"
                                            placeholder="Nhập Api Token Ở Web Mẹ" value="{{ old('api_token') }}"
                                            autofocus autocomplete="off">
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="name">Họ Và Tên</label>
                                    <input type="text" id="name" class="form-control" name="name"
                                        placeholder="Nhập Họ Và Tên" value="{{ old('name') }}" autofocus
                                        autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="email">Địa Chỉ Email</label>
                                    <input type="text" id="email" class="form-control" name="email"
                                        placeholder="Nhập Địa Chỉ Email" value="{{ old('email') }}"
                                        autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label for="username" class="form-label">Tài Khoản</label>
                                    <input type="text" id="username" class="form-control" name="username"
                                        placeholder="Nhập Tên Tài Khoản" value="{{ old('username') }}"
                                        autocomplete="off">
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
                                    <button type="submit" class="fxt-btn-fill">Cài Đặt Website</button>
                                </div>
                            </form>
                        </div>
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
