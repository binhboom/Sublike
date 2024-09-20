@extends('guard.layouts.master')
@section('title', 'Thanh toán hoá đơn')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-3">Thanh toán hoá đơn</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="py-3 text-center bg-light-primary rounded-2 fw-bold mb-4">
                        Nạp tiền qua chuyển khoản
                    </div>
                    <table class="table table-row-dashed table-row-gray-300 gy-7">
                        <tbody>
                            <tr>
                                <td>Ngân Hàng</td>
                                <td>
                                    <p class="text-info fw-bolder ng-binding bank-name"
                                        style="cursor: pointer; color: red !important; display: inline-block;">
                                        {{ $banking->bank_name }}
                                    </p>
                                    <button type="button" class="btn btn-primary text-sm btn-sm ml-3 btn-copy"
                                        style="float: right;" onclick="coppy('{{ $banking->bank_name }}')">
                                        Sao chép
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Tên chủ tài khoản</td>
                                <td>
                                    <p class="text-info fw-bolder ng-binding account-owner"
                                        style="cursor: pointer; color: red !important; display: inline-block;">
                                        {{ $banking->bank_account }}</p>
                                    <button type="button" class="btn btn-primary text-sm btn-sm ml-3 btn-copy"
                                        style="float: right;" onclick="coppy('{{ $banking->bank_account }}')">
                                        Sao chép
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Số tài khoản</td>
                                <td>
                                    <p class="text-info fw-bolder ng-binding account-number"
                                        style="cursor: pointer; color: red !important; display: inline-block;">
                                        {{ $banking->account_number }}
                                    </p>
                                    <button type="button" class="btn btn-primary text-sm btn-sm ml-3 btn-copy"
                                        style="float: right;" onclick="coppy('{{ $banking->account_number }}')">
                                        Sao chép
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Nội dung chuyển khoản</td>
                                <td>
                                    <p class="text-info fw-bolder ng-binding content-tranfer"
                                        style="cursor: pointer; color: red !important; display: inline-block;">
                                        {{ site('transfer_code') }}{{ $bill->order_code }}</p>
                                    <button type="button" class="btn btn-primary text-sm btn-sm ml-3 btn-copy"
                                        style="float: right;"
                                        onclick="coppy('{{ site('transfer_code') }}{{ $bill->order_code }}')">
                                        Sao chép
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Số tiền</td>
                                <td>
                                    <p class="text-info fw-bolder ng-binding amount-money"
                                        style="cursor: pointer; color: red !important; display: inline-block;">
                                        {{ number_format($bill->amount) }} VNĐ
                                    </p>
                                    <button type="button" class="btn btn-primary text-sm btn-sm ml-3 btn-copy"
                                        style="float: right;" onclick="coppy('{{ $bill->amount }}')">
                                        Sao chép
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="py-3 text-center bg-light-primary rounded-2 fw-bold mb-4">
                        Nạp tiền qua quét mã QR
                    </div>
                    <div class="text-center mb-3">
                        <img src="https://img.vietqr.io/image/{{ $banking->bank_name === 'Viettinbank' ? 'ICB' : $banking->bank_name }}-{{ $banking->account_number }}-qronly2.jpg?accountName={{ $banking->account_name }}&addInfo={{ siteValue('transfer_code') }}{{ $bill->order_code }}&amount={{ $bill->amount }}"
                            alt="QR CODE" width="300">
                    </div>
                    <div class="">
                        <h4 class="card-title">Hướng dẫn nạp tiền qua quét mã QR</h4>
                        <ul class="fw-bold">
                            <li>1. Đăng nhập ứng dụng Mobile Banking, chọn chức năng Scan QR và quét mã QR trên đây.</li>
                            <li>2. Nhập số tiền muốn nạp, kiểm tra thông tin đơn hàng (NH, chủ TK, số TK, Nội dung CK) trùng
                                khớp với thông tin CK bên trái.</li>
                            <li>3. Xác nhận giao dịch và chờ nhận thông báo giao dịch thành công.</li>
                        </ul>
                        <small>
                            <p class="text-danger">*Chú ý: mỗi mã QR chỉ dùng cho 1 giao dịch nạp tiền, không sử dụng lại
                            </p>
                        </small>
                    </div>
                </div>
                <div class="col-md-12">
                    {{-- chờ giao dịch loading --}}
                    <div class="text-center mt-5">
                        <h4 class="card-title mb-3">Chờ giao dịch xác nhận</h4>
                        <p class="text-danger">Vui lòng không tắt trình duyệt hoặc thoát khỏi trang này cho đến khi giao
                            dịch được xác nhận</p>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        async function checkPayment() {
            let url = "{{ route('payment.check', $bill->order_code) }}";
            let response = await fetch(url);
            let result = await response.json();
            if (result.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Giao dịch thành công',
                    text: 'Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('account.recharge') }}";
                    }
                });
            }
        }

        setInterval(() => {
            checkPayment();
        }, 7000);
    </script>
@endsection

