<!DOCTYPE html>
<html lang="{{ str_replace('-', '_', app()->getLocale()) }}">
<!-- [Head] start -->
@include('admin.layouts.header')
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-2" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme_contrast=""
    data-pc-theme="light">

    {!! site('script_body') !!}

    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ route('admin.dashboard') }}" class="b-brand text-primary">
                    <!-- ========   Change your logo from here   ============ -->
                    <img src="{{ siteValue('logo') }}" class="img-fluid logo-lg" alt="logo">
                </a>
            </div>
            <div class="navbar-content mb-3">
                @include('admin.layouts.menu')
            </div>
        </div>
    </nav>
    <!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="javascript:;" class="pc-head-link ms-0" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="javascript:;" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none m-0 trig-drp-search" data-bs-toggle="dropdown"
                            href="javascript:;" role="button" aria-haspopup="false" aria-expanded="false">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-search-normal-1"></use>
                            </svg>
                        </a>
                        <div class="dropdown-menu pc-h-dropdown drp-search">
                            <form class="px-3 py-2">
                                <input type="search" class="form-control border-0 shadow-none"
                                    placeholder="Search here. . ." />
                            </form>
                        </div>
                    </li>
                    <li class="dropdown pc-h-item">
                        {{-- toàn bộ link cron --}}
                        <button type="button" class="btn btn-outline-primary btn-sm text-sm" data-bs-toggle="modal"
                            data-bs-target="#exampleModalCenter" fdprocessedid="jv8mqe">Link Cron</button>
                    </li>
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
            <div class="ms-auto">
                <ul class="list-unstyled">
                    <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="javascript:;" role="button" aria-haspopup="false" aria-expanded="false">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sun-1"></use>
                            </svg>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                            <a href="javascript:;" class="dropdown-item" onclick="layout_change('light')">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-sun-1"></use>
                                </svg>
                                <span>Sáng</span>
                            </a>
                            <a href="javascript:;" class="dropdown-item" onclick="layout_change('dark')">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-moon"></use>
                                </svg>
                                <span>Tối</span>
                            </a>
                        </div>
                    </li>
                    <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="javascript:;" role="button" aria-haspopup="false" aria-expanded="false">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-notification"></use>
                            </svg>
                            <span class="badge bg-success pc-h-badge">3</span>
                        </a>
                        <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header d-flex align-items-center justify-content-between">
                                <h5 class="m-0">Notifications</h5>
                                <a href="javascript:;" class="btn btn-link btn-sm">Mark all read</a>
                            </div>
                            <div class="dropdown-body text-wrap header-notification-scroll position-relative"
                                style="max-height: calc(100vh - 215px)">
                                <p class="text-span">Today</p>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <svg class="pc-icon text-primary">
                                                    <use xlink:href="#custom-layer"></use>
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <span class="float-end text-sm text-muted">2 min ago</span>
                                                <h5 class="text-body mb-2">UI/UX Design</h5>
                                                <p class="mb-0">Lorem Ipsum has been the industry's standard dummy
                                                    text ever since the 1500s, when an unknown printer took a galley of
                                                    type and scrambled it to make a type</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <svg class="pc-icon text-primary">
                                                    <use xlink:href="#custom-sms"></use>
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <span class="float-end text-sm text-muted">1 hour ago</span>
                                                <h5 class="text-body mb-2">Message</h5>
                                                <p class="mb-0">Lorem Ipsum has been the industry's standard dummy
                                                    text ever since the 1500.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-span">Yesterday</p>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <svg class="pc-icon text-primary">
                                                    <use xlink:href="#custom-document-text"></use>
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <span class="float-end text-sm text-muted">2 hour ago</span>
                                                <h5 class="text-body mb-2">Forms</h5>
                                                <p class="mb-0">Lorem Ipsum has been the industry's standard dummy
                                                    text ever since the 1500s, when an unknown printer took a galley of
                                                    type and scrambled it to make a type</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <svg class="pc-icon text-primary">
                                                    <use xlink:href="#custom-user-bold"></use>
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <span class="float-end text-sm text-muted">12 hour ago</span>
                                                <h5 class="text-body mb-2">Challenge invitation</h5>
                                                <p class="mb-2"><span class="text-dark">Jonny aber</span> invites
                                                    to join the challenge</p>
                                                <button class="btn btn-sm btn-outline-secondary me-2">Decline</button>
                                                <button class="btn btn-sm btn-primary">Accept</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <svg class="pc-icon text-primary">
                                                    <use xlink:href="#custom-security-safe"></use>
                                                </svg>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <span class="float-end text-sm text-muted">5 hour ago</span>
                                                <h5 class="text-body mb-2">Security</h5>
                                                <p class="mb-0">Lorem Ipsum has been the industry's standard dummy
                                                    text ever since the 1500s, when an unknown printer took a galley of
                                                    type and scrambled it to make a type</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center py-2">
                                <a href="javascript:;" class="link-danger">Clear all Notifications</a>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="javascript:;" role="button" aria-haspopup="false" data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?background=random&name={{ Auth::user()->name }}"
                                alt="user-image" class="user-avtar" />
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header d-flex align-items-center justify-content-between">
                                <h5 class="m-0">Thông tin</h5>
                            </div>
                            <div class="dropdown-body">
                                <div class="profile-notification-scroll position-relative"
                                    style="max-height: calc(100vh - 225px)">
                                    <div class="d-flex mb-1">
                                        <div class="flex-shrink-0">
                                            <img src="https://ui-avatars.com/api/?background=random&name={{ Auth::user()->name }}"
                                                alt="user-image" class="user-avtar wid-35" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                            <span>{{ Auth::user()->email }}</span>
                                        </div>
                                    </div>
                                    <hr class="border-secondary border-opacity-50" />
                                    <div class="d-grid mb-3">
                                        <a href="{{ route('home') }}" class="btn btn-primary">
                                            <svg class="pc-icon me-2">
                                                <use xlink:href="#custom-logout-1-outline"></use>
                                            </svg>Quay lại trang chủ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement"
        aria-labelledby="announcementLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <p class="text-span">Today</p>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                        <div class="badge bg-light-success f-12">Big News</div>
                        <p class="mb-0 text-muted">2 min ago</p>
                        <span class="badge dot bg-warning"></span>
                    </div>
                    <h5 class="mb-3">Able Pro is Redesigned</h5>
                    <p class="text-muted">Able Pro is completely renowed with high aesthetics User Interface.</p>
                    <img src="/assets/images/layout/img-announcement-1.png" alt="img" class="img-fluid mb-3" />
                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid"><a class="btn btn-outline-secondary"
                                    href="https://1.envato.market/zNkqj6" target="_blank">Check Now</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                        <div class="badge bg-light-warning f-12">Offer</div>
                        <p class="mb-0 text-muted">2 hour ago</p>
                        <span class="badge dot bg-warning"></span>
                    </div>
                    <h5 class="mb-3">Able Pro is in best offer price</h5>
                    <p class="text-muted">Download Able Pro exclusive on themeforest with best price. </p>
                    <a href="https://1.envato.market/zNkqj6" target="_blank"><img
                            src="/assets/images/layout/img-announcement-2.png" alt="img"
                            class="img-fluid" /></a>
                </div>
            </div>

            <p class="text-span mt-4">Yesterday</p>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                        <div class="badge bg-light-primary f-12">Blog</div>
                        <p class="mb-0 text-muted">12 hour ago</p>
                        <span class="badge dot bg-warning"></span>
                    </div>
                    <h5 class="mb-3">Featured Dashboard Template</h5>
                    <p class="text-muted">Do you know Able Pro is one of the featured dashboard template selected by
                        Themeforest team.?</p>
                    <img src="/assets/images/layout/img-announcement-3.png" alt="img" class="img-fluid" />
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                        <div class="badge bg-light-primary f-12">Announcement</div>
                        <p class="mb-0 text-muted">12 hour ago</p>
                        <span class="badge dot bg-warning"></span>
                    </div>
                    <h5 class="mb-3">Buy Once - Get Free Updated lifetime</h5>
                    <p class="text-muted">Get the lifetime free updates once you purchase the Able Pro.</p>
                    <img src="/assets/images/layout/img-announcement-4.png" alt="img" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
    <!-- [ Header ] end -->



    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header mb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item fw-bold"><a
                                        href="{{ route('home') }}">{{ ucwords(request()->getHost()) }}</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">@yield('title')</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            @yield('content')
            <!-- [ Main Content ] end -->
        </div>
    </div>

    <div id="exampleModalCenter" class="modal fade" tabindex="-1" aria-labelledby="exampleModalCenterTitle"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Danh sách Link Cron</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Loại Cron</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (env('APP_MAIN_SITE') == request()->getHost())
                                    <tr>
                                        <td>Nạp tiền Momo</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.payment', ['code' => 'Momo']) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nạp tiền Mbbank</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.payment', ['code' => 'Mbbank']) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nạp tiền Techcombank</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.payment', ['code' => 'Techcombank']) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nạp tiền ACB</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.payment', ['code' => 'ACB']) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nạp thẻ cào</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('cron-job.recharge-card.status') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kiểm tra bill nạp hết hạn</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.check-bill-expired') }}</span>
                                        </td>
                                    </tr>
                                  
                                    </tr>
                                    <tr>
                                        <td>Update cấp bậc cho khách hàng</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.check-level') }}</span>
                                        </td>
                                    </tr>
                                    <h3>Cron Cập nhật trạng thái đơn (api)</h3>
                                    <h3>Thuê Cron Giá Rẻ Tại portal.vpnfast.vn </h3>

                                    <tr>
                                        
                                        <td>
                                            
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>Nạp tiền Momo</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.payment', ['code' => 'Momo']) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nạp tiền Mbbank</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.payment', ['code' => 'Mbbank']) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nạp tiền Techcombank</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.payment', ['code' => 'Techcombank']) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nạp tiền ACB</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.payment', ['code' => 'ACB']) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nạp thẻ cào</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('cron-job.recharge-card.status') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kiểm tra bill nạp hết hạn</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.check-bill-expired') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Auto Check giá</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.price-update-service') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Cập nhật đơn từ Site Mẹ</td>
                                        <td><span
                                                class="badge bg-primary badge-sm">{{ route('api.update.orders') }}</span>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- [ Main Content ] end -->
    @include('guard.layouts.footer')
    <!-- [Page Specific JS] start -->
    {{-- jquery --}}
    <script src="/assets/js/plugins/jquery-3.7.1.min.js"></script>
    <script src="/assets/js/pages/dashboard-default.js"></script>
    <!-- Required Js -->
    <script src="/assets/js/plugins/popper.min.js"></script>
    <script src="/assets/js/plugins/simplebar.min.js"></script>
    <script src="/assets/js/plugins/bootstrap.min.js"></script>
    <script src="/assets/js/fonts/custom-font.js"></script>
    <script src="/assets/js/pcoded.js"></script>
    <script src="/assets/js/plugins/feather.min.js"></script>
    <script src="/assets/js/plugins/toastr.min.js"></script>
    <script src="/assets/js/plugins/sweetalert2.all.min.js"></script>
    <script src="/assets/js/app.js?lbd-time={{ time() }}"></script>
    <script src="/assets/js/codedynamic.js?lbd-time={{ time() }}"></script>

    {!! site('script_footer') !!}

    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Thành công',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'Xác nhận'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Lỗi',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'Xác nhận'
            })
        </script>
    @endif

    @yield('script')
</body>
<!-- [Body] end -->

</html>
