<ul class="pc-navbar">
    <li class="pc-item pc-caption">
        <label>Bảng Điều Khiển</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.dashboard') }}" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-status-up"></use>
                </svg>
            </span>
            <span class="pc-mtext">Trang thống kê</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.website.config') }}" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-setting-2"></use>
                </svg>
            </span>
            <span class="pc-mtext">Cấu hình hệ thống</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.telegram.config') }}" class="pc-link">
            <span class="pc-micon">
                <i class="fab fa-telegram"></i>
            </span>
            <span class="pc-mtext">Cấu hình Telegram</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.payment.config') }}" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-dollar-square"></use>
                </svg>
            </span>
            <span class="pc-mtext">Cấu hình nạp tiền</span>
        </a>
    </li>
    {{-- <li class="pc-item">
        <a href="{{ route('admin.payment.config') }}" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-dollar-square"></use>
                </svg>
            </span>
            <span class="pc-mtext">Vé Ticket</span>
        </a>
    </li> --}}
    @if (request()->getHost() === env('APP_MAIN_SITE'))
        <li class="pc-item">
            <a href="{{ route('admin.website.partner') }}" class="pc-link">
                <span class="pc-micon">
                    <i class="fas fa-network-wired"></i>
                </span>
                <span class="pc-mtext">Danh sách Web con</span>
            </a>
        </li>
    @endif
    <li class="pc-item pc-hasmenu">
        <a href="javascript:;" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-notification"></use>
                </svg>
            </span>
            <span class="pc-mtext">Quản lí thông báo</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.notify.system') }}">Thông báo hệ thống</a>
            </li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.notify.service') }}">Thông báo hoạt động</a></li>
        </ul>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="javascript:;" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-user"></use>
                </svg>
            </span>
            <span class="pc-mtext">Quản lí thành viên</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.user') }}">Danh sách thành viên</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.user.balance') }}">Thay đổi số dư</a></li>
        </ul>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="javascript:;" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-direct-inbox"></use>
                </svg>
            </span>
            <span class="pc-mtext">Dịch vụ </span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            @if (request()->getHost() === env('APP_MAIN_SITE'))
                <li class="pc-item"><a class="pc-link" href="{{ route('admin.service.platform') }}">Danh sách nền
                        tảng</a>
                </li>
                <li class="pc-item"><a class="pc-link" href="{{ route('admin.service') }}">Danh sách dịch vụ</a></li>
                <li class="pc-item"><a class="pc-link" href="{{ route('admin.service.smm') }}">Danh sách SmmPanel</a></li>
                <li class="pc-item"><a class="pc-link" href="{{ route('admin.server') }}">Danh sách máy chủ</a></li>
            @else
                <li class="pc-item"><a class="pc-link" href="{{ route('admin.server') }}">Danh sách máy chủ</a></li>
            @endif
        </ul>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="javascript:;" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-clipboard"></use>
                </svg>
            </span>
            <span class="pc-mtext">Lịch sử & Dữ liệu</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.history.user') }}">Lịch sử giao dịch</a>
            </li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.history.orders') }}">Lịch sử tạo đơn</a>
            </li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.history.payment') }}">Lịch sử nạp tiền</a>
            </li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.history.transactions') }}">Lịch sử hoạt động</a>
            </li>
        </ul>
    </li>

</ul>
