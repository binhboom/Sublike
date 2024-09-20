<ul class="pc-navbar">
     @if (Auth::check() && Auth::user()->role === 'admin')
        <li class="pc-item pc-caption">
            <label>Admin</label>
        </li>
        <li class="pc-item">
            <a href="{{ route('admin.dashboard') }}" class="pc-link">
                <span class="pc-micon">
                    <img src="{{ asset('assets/images/world.png') }}" class="wid-35" alt="">
                </span>
                <span class="pc-mtext">Trang Quản Trị</span>
            </a>
        </li>
    @endif
    <li class="pc-item pc-caption">
        <label>Bảng Điều Khiển</label>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="javascript:;" class="pc-link">
            <span class="pc-micon">
                <img src="{{ asset('assets/pack-lbd/images/dashboard.png') }}" class="wid-35" alt="">
            </span>
            <span class="pc-mtext">Quản lý chung</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu" style="list-style-type: none !important;">
            <li class="pc-item">
                <a href="{{ route('home') }}" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/home.png') }}" class="wid-35" alt="">
                    </span>
                    <span class="pc-mtext">Trang Chủ</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="{{ route('account.profile') }}" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/profile.png') }}" class="wid-35" alt="">
                    </span>
                    <span class="pc-mtext">Thông Tin Cá Nhân</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="{{ route('account.recharge') }}" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/payment-method.png') }}" class="wid-35"
                            alt="">
                    </span>
                    <span class="pc-mtext">Nạp Tiền </span>
                </a>
            </li>
            <li class="pc-item">
                <a href="{{ route('account.transactions') }}" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/transactions.png') }}" class="wid-35" alt="">
                    </span>
                    <span class="pc-mtext">Nhật Ký</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="{{ route('account.progress') }}" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/bills.png') }}" class="wid-35" alt="">
                    </span>
                    <span class="pc-mtext">Tiến Trình</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="{{ route('account.services') }}" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/success.png') }}" class="wid-35" alt="">
                    </span>
                    <span class="pc-mtext">Cấp Bậc & Dịch Vụ</span>
                </a>
            </li>
           <li class="pc-item">
                <a href="{{ route('ticket') }}" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/ticket.png') }}" class="wid-35" alt="">
                    </span>
                    <span class="pc-mtext">Hỗ Trợ Ticket</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="{{ route('create.website') }}" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/www.png') }}" class="wid-35" alt="">
                    </span>
                    <span class="pc-mtext">Tạo Website Riêng</span>
                </a>
            </li>
            <li class="pc-item">
                <a href="https://documenter.getpostman.com/view/19496902/2sA3Bt29se" class="pc-link">
                    <span class="pc-micon">
                        <img src="{{ asset('assets/pack-lbd/images/suitcase.png') }}" class="wid-35" alt="">
                    </span>
                    <span class="pc-mtext">Tài Liệu Kết Nối</span>
                </a>
            </li>
        </ul>
    </li>


    <li class="pc-item pc-caption">
        <label>Danh Sách Dịch Vụ</label>
    </li>
    @foreach (\App\Models\ServicePlatform::where('domain', env('APP_MAIN_SITE'))->where('status', 'active')->orderBy('order', 'asc')->get() as $platform)
        <li class="pc-item pc-hasmenu">
            <a href="javascript:;" class="pc-link">
                <span class="pc-micon">
                    <img src="{{ $platform->image }}" class="wid-35" alt="{{ $platform->name }}">
                </span>
                <span class="pc-mtext">{{ $platform->name }}</span>
                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            </a>
            <ul class="pc-submenu">
                @foreach ($platform->services as $service)
                    <li class="pc-item">
                        <a href="{{ route('service', ['service' => $service->slug, 'platform' => $platform->slug]) }}"
                            class="pc-link">
                            <span class="pc-mtext">{{ $service->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach

    {{-- <li class="pc-item pc-hasmenu">
        <a href="javascript:;" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-status-up"></use>
                </svg>
            </span>
            <span class="pc-mtext">Dashboard</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="index.html">Default</a></li>
            <li class="pc-item"><a class="pc-link" href="analytics.html">Analytics</a></li>
        </ul>
    </li> --}}
</ul>
