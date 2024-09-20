@extends('guard.layouts.master')

@section('title', 'Thông Tin Cá Nhân')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item col-6">
                            <a class="nav-link text-center fw-medium active" id="pills-home-tab" data-bs-toggle="pill"
                                href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                                <i class="ti ti-user-check"></i>
                                Thông tin
                            </a>
                        </li>
                        <li class="nav-item col-6">
                            <a class="nav-link text-center fw-medium " id="pills-profile-tab" data-bs-toggle="pill"
                                href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">
                                <i class="ti ti-lock"></i>
                                Bảo mật
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Thông tin cá nhân</h5>
                                </div>
                                <div class="card-body">
                                    <form action="">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="name" class="form-label">Họ và tên:</label>
                                                <input type="text" class="form-control" id="name" disabled
                                                    value="{{ Auth::user()->name }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="email" class="form-label">Địa chỉ Email:</label>
                                                <input type="text" class="form-control" id="email" disabled
                                                    value="{{ Auth::user()->email }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="username" class="form-label">Tài khoản:</label>
                                                <input type="text" class="form-control" id="username" disabled
                                                    value="{{ Auth::user()->username }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="created_at" class="form-label">Thời gian đăng kí:</label>
                                                <input type="text" class="form-control" id="created_at" disabled
                                                    value="{{ Auth::user()->created_at }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="balance" class="form-label">Số dư:</label>
                                                <input type="text" class="form-control" id="balance" disabled
                                                    value="{{ number_format(Auth::user()->balance) }}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="last_login" class="form-label">Đăng nhập gần đây:</label>
                                                <input type="text" class="form-control" id="last_login" disabled
                                                    value="{{ Auth::user()->last_login }}">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label for="api_token" class="form-label">Api Token</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="api_token" readonly
                                                        onclick="coppy('{{ Auth::user()->api_token ?? 'null' }}')"
                                                        value="{{ Auth::user()->api_token ?? 'Bạn chưa tạo Api Token!' }}"
                                                        placeholder="Bạn cần ấn thay đổi Token">
                                                    <button class="btn btn-primary" type="button" id="btn-reload-token">
                                                        <i class="ti ti-refresh"></i>
                                                        Thay đổi
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Đổi mật khẩu</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('account.change-password') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label for="current_password" class="form-label">Mật khẩu hiện
                                                    tại:</label>
                                                <input type="password" class="form-control" id="current_password"
                                                    name="current_password">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label for="new_password" class="form-label">Mật khẩu mới:</label>
                                                <input type="password" class="form-control" id="new_password"
                                                    name="new_password">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label for="confirm_password" class="form-label">Xác nhận mật
                                                    khẩu:</label>
                                                <input type="password" class="form-control" id="confirm_password"
                                                    name="confirm_password">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <button type="submit" class="btn btn-primary col-12">
                                                    <i class="ti ti-lock"></i>
                                                    Thay đổi mật khẩu
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Lịch sử hoạt động</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered nowrap dataTable"
                                            style="max-width: 1444px;">
                                            <thead>
                                                <tr>
                                                    <th>Thời gian</th>
                                                    <th>Hoạt động</th>
                                                    <th>IP</th>
                                                    <th>User Agent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (\App\Models\UserActivity::where('user_id', Auth::user()->id)->where('activity', 'auth')->orderBy('id', 'DESC')->get() as $activity)
                                                    <tr>
                                                        <td>{{ $activity->created_at }}</td>
                                                        <td>{{ $activity->note }}</td>
                                                        <td>{{ $activity->ip }}</td>
                                                        <td>{{ $activity->user_agent }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Bảo mật tài khoản</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 border-bottom py-2">
                                <h4 class="text-primary fw-bold fs-4 mb-3">Xác thực 2 yếu tố</h4>
                                <div class="alert alert-primary">
                                    <h4 class="alert-heading">Xác thực 2 yếu tố là gì?</h4>
                                    <p class="mb-0">Xác thực 2 yếu tố (2FA) là một phương pháp bảo mật mạnh mẽ hơn
                                        so với mật khẩu đơn lẻ. Khi bật xác thực 2 yếu tố, bạn sẽ cần nhập một mã xác
                                        thực được tạo ra từ ứng dụng xác thực trên điện thoại di động của bạn sau
                                        khi nhập mật khẩu của bạn. Điều này giúp bảo vệ tài khoản của bạn khỏi các
                                        cuộc tấn công xâm nhập và truy cập trái phép.</p>
                                </div>
                                {{-- trạng thái --}}
                                @if (Auth::user()->two_factor_auth === 'yes')
                                    <h6 class="text-muted fw-bold fs-6 mb-3">Trạng thái: <span
                                            class="badge bg-success badge-sm">Đã bật</span></h6>

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#two_factor_auth">
                                        Tắt xác thực
                                    </button>

                                    <div class="modal fade modal-animate" id="two_factor_auth" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tắt xác thực 2 yếu tố</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"> </button>
                                                </div>
                                                <form action="{{ route('account.two-factor-auth-disable') }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3 text-center">
                                                            <h4 class="text-gray"> Nhập mã xác thực để tắt xác thực 2
                                                                yếu tố</h4>
                                                        </div>
                                                        <div class="mb-3 form-group">
                                                            <label class="form-label">Nhập mã xác thực</label>
                                                            <input type="text" class="form-control" id="code"
                                                                autocomplete="off" name="code">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-primary shadow-2">Bật
                                                            xác
                                                            thực</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <h6 class="text-muted fw-bold fs-6 mb-3">Trạng thái: <span
                                            class="badge bg-danger badge-sm">Chưa bật</span></h6>

                                    <button data-pc-animate="slide-in-right" type="button" class="btn btn-primary"
                                        data-bs-toggle="modal" data-bs-target="#two_factor_auth">
                                        Xác thực
                                    </button>

                                    <div class="modal fade modal-animate" id="two_factor_auth" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Xác thực 2 yếu tố</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"> </button>
                                                </div>
                                                <form action="{{ route('account.two-factor-auth') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3 text-center">
                                                            <h4 class="text-gray">Quét mã QR bằng ứng dụng xác thực</h4>
                                                            <img src="{{ $qrCodeUrl }}" alt="QR Google Authenticate">
                                                        </div>
                                                        <div class="mb-3 text-center">
                                                            <h4 class="text-gray">Hoặc nhập mã bí mật</h4>
                                                            <p class="text-muted">Nhập mã bí mật vào ứng dụng xác thực nếu
                                                                không thể quét mã QR</p>
                                                            <input type="text" class="form-control" id="secret"
                                                                value="{{ $secret }}" disabled>

                                                            <button type="button" class="btn btn-primary mt-3"
                                                                id="copy-secret">
                                                                <i class="ti ti-clipboard"></i>
                                                                Sao chép mã bí mật
                                                            </button>
                                                        </div>
                                                        <div class="mb-3 form-group">
                                                            <label class="form-label">Nhập mã xác thực</label>
                                                            <input type="text" class="form-control" id="code"
                                                                autocomplete="off" name="code">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-primary shadow-2">Bật xác
                                                            thực</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3 border-bottom py-2">
                                <h4 class="text-primary fw-bold fs-4 mb-3">Liên kết Telegram</h4>
                                <div class="alert alert-primary">
                                    <h4 class="alert-heading">Cấu hình Telegram</h4>
                                    <ul>
                                        <li>Để bảo mật tài khoản của bạn, bạn có thể liên kết tài khoản của mình với
                                            Telegram. Khi liên kết, bạn sẽ nhận được thông báo qua Telegram khi có hoạt
                                            động đăng nhập từ thiết bị không xác định.</li>
                                        <li>Nên Cấu Hình Để Sử Dụng Nhằm Bảo Vệ Tài Khoản Và Cập Nhật Lịch Sử Đơn Hàng Nhanh
                                            Chóng Tránh Bị Bug</li>
                                        <li>Gửi Lịch Sử Mua Hàng & Nạp Tiền Về Telegram Của Bạn </li>
                                    </ul>
                                </div>

                                @if (Auth::user()->telegram_id !== null && Auth::user()->telegram_id !== '')
                                    <h6 class="text-muted fw-bold fs-6 mb-3">Trạng thái: <span
                                            class="badge bg-success badge-sm">Đã liên kết</span></h6>
                                    <form action="{{ route('account.update.status-telegram') }}" method="POST">
                                        @csrf
                                        <div class="mb-3 form-group">
                                            <label class="form-label">ID Telegram</label>
                                            <input type="text" class="form-control" id="telegram_id"
                                                value="{{ Auth::user()->telegram_id }}" disabled>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Thông báo về telegram</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="status" name="status" value="{{ Auth::user()->notification_telegram == 'yes' ? 'no' : 'yes' }}"
                                                    {{ Auth::user()->notification_telegram == 'yes' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status">
                                                    Gửi thông báo
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary shadow-2 btn-sm text-sm">Cập nhật</button>
                                        </div>
                                    </form>
                                @else
                                    <h6 class="text-muted fw-bold fs-6 mb-3">Trạng thái: <span
                                            class="badge bg-danger badge-sm">Chưa liên kết</span></h6>

                                    <button data-pc-animate="slide-in-right" type="button" class="btn btn-primary"
                                        data-bs-toggle="modal" data-bs-target="#telegram">
                                        Liên kết Telegram
                                    </button>

                                    <div class="modal fade modal-animate" id="telegram" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Liên Kết Telegram</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"> </button>
                                                </div>
                                                <form action="{{ route('account.update.status-telegram') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3 text-center">
                                                            <h4 class="text-gray">Thông tin Bot Telegram</h4>

                                                            <p class="text-muted">Để liên kết tài khoản của bạn với
                                                                Telegram,
                                                                bạn cần thực hiện các bước sau:</p>
                                                            <ol>
                                                                <li>Thêm Bot Telegram: <a
                                                                        href="https://t.me/{{ siteValue('telegram_bot_chat_username') }}"
                                                                        target="_blank">https://t.me/{{ siteValue('telegram_bot_chat_username') }}</a>
                                                                </li>
                                                                <li>Nhấn vào nút <strong>Start</strong> để bắt đầu</li>
                                                                <li>Chọn <strong>/active {api_token}</strong> để liên kết
                                                                    tài khoản trong đó {api_token} là phần token của bạn
                                                                </li>
                                                            </ol>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-primary shadow-2">Liên
                                                            Kết</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

    <script>
        $(document).ready(function() {
            $('#copy-secret').click(function() {
                var copyText = document.getElementById("secret");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);
                document.execCommand("copy");
                // alert("Đã sao chép mã bí mật: " + copyText.value);
                toastrNotify("Đã sao chép mã bí mật!", "success");
            });


            $('#btn-reload-token').click(function() {
                $.ajax({
                    url: "{{ route('account.reload-user-token') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#api_token').val(data.api_token);
                        toastrNotify("Đã thay đổi Api Token!", "success");
                    },
                    error: function() {
                        toastrNotify("Có lỗi xảy ra!", "error");
                    }
                });
            });
        });
    </script>
@endsection
