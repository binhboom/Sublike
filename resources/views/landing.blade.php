<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ env('APP_NAME') }}    | SMM PANEL - Social Media Marketing</title>

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

    <link rel="icon" href="{{ siteValue('favicon') }}" type="image/x-icon"> <!-- [Font] Family -->
    <link rel="shortcut icon" href="{{ siteValue('favicon') }}" type="image/x-icon">

    <!-- bootstrap -->
    <link rel="stylesheet" href="/landing/assets/css/bootstrap.min.css">
    <!-- fontawesome -->
    <link rel="stylesheet" href="/landing/assets/css/fontawesome.min.css">
    <!-- Flat Icon -->
    <link rel="stylesheet" href="/landing/assets/css/flaticon.css">
    <!-- animate -->
    <link rel="stylesheet" href="/landing/assets/css/animate.css">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="/landing/assets/css/owl.carousel.min.css">
    <!-- magnific popup -->
    <link rel="stylesheet" href="/landing/assets/css/magnific-popup.css">
    <!-- AOS css -->
    <link rel="stylesheet" href="/landing/assets/css/aos.css">
    <!-- stylesheet -->
    <link rel="stylesheet" href="/landing/assets/css/style.css">
    <!-- responsive -->
    <link rel="stylesheet" href="/landing/assets/css/responsive.css">
</head>

<body class="home5">

    <!-- preloader area start -->
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="cube-wrapper">
                <div class="cube-folding">
                    <span class="leaf1"></span>
                    <span class="leaf2"></span>
                    <span class="leaf3"></span>
                    <span class="leaf4"></span>
                </div>
                <span class="loading" data-name="Loading">Đang Loading Vui Lòng Chờ...</span>
            </div>
        </div>
    </div>
    <!-- preloader area end -->

    <!-- Navebar Area start -->
    <header class="navigation">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="index.html">
                            <img src="{{ site('logo') }}" alt="">
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu"
                            aria-controls="mainmenu" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="mainmenu">
                            <ul class="navbar-nav ml-auto">
                                <li class="nav-item active">
                                    <a class="nav-link" href="{{ route('home') }}">Trang Chủ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#about">Về Chúng Tôi</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        Đăng Nhập
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- Navebar Area End -->

    <!-- Hero Area Start -->
    <div id="home" class="hero-area">
        <img class="shape" src="/landing/assets/images/shape-pattern.png" alt="">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 d-flex align-self-center">
                    <div class="left-content">
                        <div class="content">
                            <h1 class="title">
                               Hệ Thống Mạng Xã Hội - SmmPanel   Social Media Marketing
                            </h1>

                            <p class="subtitle">
                               Trang Web Của Chúng Tôi Cung Cấp Giải Pháp SMM Rẻ Và Hiệu Quả, Giúp Bạn Cải Thiện Tốt Hơn Trong Việc Quản Lý Và Phát Triển Mạng Xã Hội Của Mình. Với Những Công Cụ Và Dịch Vụ Được Cung Cấp, Bạn Có Thể Dễ Dàng Tăng Lượng Người Theo Dõi, Thích, Xem Cho Tài Khoản Của Mình Hoặc Khách Hàng Một Cách Hiệu Quả Và Nhanh Chóng Tự Động 
                            </p>
                            <div class="links">
                                <a href="{{ route('login') }}" class="mybtn3 mybtn-light"><span>Tiếp tục</span> </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-first order-lg-last">
                    <div class="right-img">
                        {{-- <div class="discount-circle">
                            <div class="discount-circle-inner">
                                <div class="price">
                                    50%
                                    <span>OFF</span>
                                </div>
                            </div>
                        </div> --}}
                        <img class="img-fluid img rounded" src="{{ asset('assets/images/2345687_308504-P8TOH2-638.png') }}" style="border-radius: 120px;" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero Area End -->

    <!-- About Area Start -->
    <section class="about" id="about">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="section-title extra">
                        <h2 class="title">Giải Pháp Của Chúng Tôi Là </h2>
                        <p>
                            {{ site('domain') }} Là Việc Sử Dụng Các Nền Tảng Truyền Thông Xã Hội Như Instagram, Facebook, YouTube, TikTok, Shopee Và Nhiều Nền Tảng Khác Để Quảng Bá Bản Thân Hoặc Công Ty Của Bạn.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="box" data-aos="fade-right">
                        <div class="inner-box">
                            <div class="icon">
                                <img src="{{ asset('assets/images/analytics.png') }}" width="50" alt="">
                            </div>
                            <h4 class="title">Phân Tích Dữ Liệu</h4>
                            <p class="text">
                                Với Nhiều Năm Hoạt Động Trong Lĩnh Vực Digital Marketing, Chúng Tôi Đã Thiết Lập Hệ Thống Kinh Doanh Cho Rất Nhiều Khách Hàng Với Rất Nhiều Dịch Vụ.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="box" data-aos="fade-up">
                        <div class="inner-box">
                            <div class="icon">
                                <img src="{{ asset('assets/images/encrypted.png') }}" width="50" alt="">
                            </div>
                            <h4 class="title">Quản Lý Bảo Mật</h4>
                            <p class="text">Chúng Tôi Cam Kết Sẽ Bảo Mật Thông Tin Người Dùng Một Cách Tốt Nhất. Không Để Thất Thoát Dữ Liệu Thông Tin Cá Nhân.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="box" data-aos="fade-left">
                        <div class="inner-box">
                            <div class="icon">
                                <img src="{{ asset('assets/images/seo.png') }}" width="50" alt="">
                            </div>
                            <h4 class="title">Gia Tăng Lợi Nhuận</h4>
                            <p class="text">
                            Với Tôn Chỉ Hoạt Động Của Chúng Tôi “Tạo Ra Lợi Nhuận Cho Khách Hàng”, Lợi Nhuận Của Khách Hàng Chính Là Sự Sống Còn Của Chúng Tôi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About Area End -->

    <!-- We are best Area Start -->
    <section class="whaybest">
        <div class="container">
            <div class="row row-one">
                <div class="col-lg-6 d-flex">
                    <div class="about-img">
                        <img src="{{ asset('assets/images/home-version-one-main-banner-side-img-20210823100039.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="info" data-aos="fade-left">
                        <h2 class="info-title">
                            Nền Tảng Tăng Tương Tác Uy Tín Và Tin Cậy Nhất Cho Các Dịch Vụ Truyền Thông Mạng Xã Hội.
                        </h2>
                        <ul class="feature-list">
                            </li>
                            <li>
                                <div class="icon">
                                    <i class="flaticon-speech-bubble"></i>
                                </div>
                                <div class="content">
                                    <h4>Đồng Hành Phát Triển Cùng Chúng Tôi Nhé !!!</h4>
                                    <p>Chúng Tôi Cung Cấp Các Giải Pháp Website Và Marketing, Đồng Hành Cùng Doanh Nghiệp Trong Việc Tối Ưu Nguồn Nhân Lực, Giảm Thiểu Chi Phí Vận Hành, Tăng Trưởng Doanh Thu Và Giảm Tải Khối Lượng Công Việc.
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--We are best Area Start -->

    <!-- Subscribe Area Start -->
    <div class="subscribe-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="section-title extra">
                        <h2 class="title">Bạn Còn Chờ Đợi Gì Nữa?</h2>
                        <p>
                            Hãy Sử Dụng Thử Dịch Vụ Của Chúng Tôi Nhé.
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="download-buttons">
                        <p class="text">Tiếp Tục Sử Dụng</p>
                        <a href="{{ route('login') }}">
                            Đăng Nhập
                        </a>
                        <a href="{{ route('register') }}">
                            Đăng Ký
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Subscribe Area End -->

    <!-- Footer Section Start -->
    <footer class="footer" id="footer">

        <img class="shape" src="/landing/assets/images/shape-pattern.png" alt="">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="footer-info-area">
                        <div class="footer-logo">
                            <a href="#" class="logo-link">
                                <img src="{{ site('logo') }}" alt="">
                            </a>
                        </div>
                        <div class="text">
                            <p>
                                {{ site('description') }}
                            </p>
                        </div>
                    </div>
                    <div class="fotter-social-links">
                        <ul>
                            <li>
                                <a href="https://www.facebook.com/pnmediapluss" class="facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="copy-bg text-center">
            <p>Copyright © 2023-2034 Bảo Vệ Bản Quyền Bởi 
                <a href="{{ route('home') }}">{{ site('domain') }}</a>
            </p>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Back to Top Start -->
    <div class="bottomtotop">
        <i class="fa fa-chevron-right"></i>
    </div>
    <!-- Back to Top End -->


    <!-- jquery -->
    <script src="/landing/assets/js/jquery.js"></script>
    <!-- popper -->
    <script src="/landing/assets/js/popper.min.js"></script>
    <!-- bootstrap -->
    <script src="/landing/assets/js/bootstrap.min.js"></script>
    <!-- way poin js-->
    <script src="/landing/assets/js/waypoints.min.js"></script>
    <!-- owl carousel -->
    <script src="/landing/assets/js/owl.carousel.min.js"></script>
    <!-- magnific popup -->
    <script src="/landing/assets/js/jquery.magnific-popup.js"></script>
    <!-- aos js-->
    <script src="/landing/assets/js/aos.js"></script>
    <!-- counterup js-->
    <script src="/landing/assets/js/jquery.countdown.min.js"></script>
    <!-- easing js-->
    <script src="/landing/assets/js/jquery.easing.1.3.js"></script>
    <!-- main -->
    <script src="/landing/assets/js/contact.js"></script>
    <!-- main -->
    <script src="/landing/assets/js/main.js"></script>
</body>

</html>
