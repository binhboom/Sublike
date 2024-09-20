@extends('guard.layouts.master')

@section('title', 'Nạp Tiền')

@section('content')
    <div class="row">


        <div class="col-md-12 mb-3">
            <div class="row">
                <div class="col-6 d-grid gap-2">
                    <a href="{{ route('account.recharge') }}" class="btn btn-primary">
                        Ngân hàng</a>
                </div>
                <div class="col-6 d-grid gap-2">
                    <a href="{{ route('account.recharge.card') }}" class="btn btn-outline-primary">
                        Thẻ cào</a>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Nạp tiền</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger mb-0">
                        <ul class="mb-0">
                            <li class="fw-bold text-dark">Vui lòng nạp đúng tài khoản và nội dung</li>
                            <li class="fw-bold text-dark">Sai nội dung hoặc quên không có nội dung bị phạt 20% ( ví dụ nạp 100k còn 80k )</li>
                            <li class="fw-bold text-dark">Nạp dưới min của web yêu cầu (mất tiền)</li>
                            <li class="fw-bold text-dark">Không hỗ trợ nạp rồi rút ra với bất kì lý do gì</li>
                            <li class="fw-bold text-dark"> Sau 10p nếu chưa thấy tiền về tài khoản thì liên hệ trực tiếp
                                Admin.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                {{-- khuyến mãi nạp --}}
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Số tiền lớn hơn hoặc bằng</th>
                            <th>Khuyến mãi thêm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rechargePromotions as $rechargePromotion)
                            <tr></tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-primary">{{ number_format($rechargePromotion->min_balance) }}
                                    VNĐ</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $rechargePromotion->percentage }}%</span>
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row">
                @if ($momo->status === 'active')
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-img d-flex justify-content-center align-items-center py-3 w-100">
                                <img src="{{ asset($momo->logo) }}" alt="Logo Mbbank" width="65">
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center flex-column align-items-center">
                                    <div class="">
                                        Ngân Hàng: <span class="fw-bold">MOMO</span>
                                    </div>
                                    <div class="">
                                        Chủ tài khoản: <span class="fw-bold">{{ $momo->account_name }}
                                    </div>
                                    <div class="">
                                        Số tài khoản: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ $momo->account_number }}')">{{ $momo->account_number }} <i
                                                class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="">
                                        Nạp tối thiểu: <span class="fw-bold">{{ number_format($momo->min_recharge) }}
                                            VNĐ</span>
                                    </div>
                                    <div class="">
                                        Nội dung: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ siteValue('transfer_code') }}{{ Auth::user()->id }}')"
                                            class="cursor-pointer">{{ siteValue('transfer_code') }}{{ Auth::user()->id }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#momoModal">
                                            <i class="fas fa-qrcode"></i> QR CODE [Hoạt động]
                                        </button>
                                        <div class="modal fade text-dark" id="momoModal" tabindex="-1"
                                            aria-labelledby="momoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="momoModalLabel">Mã QR</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body" style="font-size: 1.5rem;">
                                                        <div class="d-flex jutify-content-center">
                                                            <img src="https://chart.googleapis.com/chart?chs=480x480&cht=qr&choe=UTF-8&chl=2|99|{{ $momo->account_number }}|MOMO|subgiasale.vn@gmail.com|0|0|{{ $momo->min_recharge }}|{{ siteValue('transfer_code') }}{{ Auth::user()->id }}|transfer_myqr"
                                                                alt="QR CODE" width="100%">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($mbbank->status === 'active')
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-img d-flex justify-content-center align-items-center py-3 w-100">
                                <img src="{{ asset($mbbank->logo) }}" alt="Logo Mbbank" width="150">
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center flex-column align-items-center">
                                    <div class="">
                                        Ngân Hàng: <span class="fw-bold">MB BANK</span>
                                    </div>
                                    <div class="">
                                        Chủ tài khoản: <span class="fw-bold">{{ $mbbank->account_name }}
                                    </div>
                                    <div class="">
                                        Số tài khoản: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ $mbbank->account_number }}')">{{ $mbbank->account_number }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="">
                                        Nạp tối thiểu: <span class="fw-bold">{{ number_format($mbbank->min_recharge) }}
                                            VNĐ</span>
                                    </div>
                                    <div class="">
                                        Nội dung: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ siteValue('transfer_code') }}{{ Auth::user()->id }}')"
                                            class="cursor-pointer">{{ siteValue('transfer_code') }}{{ Auth::user()->id }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-center gap-3">
                                        <button type="button" class="btn btn-primary btn-sm text-sm rounded"
                                            data-bs-toggle="modal" data-bs-target="#mbbankModal">
                                            <i class="fas fa-qrcode"></i> QR CODE[Hoạt động]
                                        </button>
                                        {{-- thanh toán hoá đơn --}}
                                        <button type="button" class="btn btn-success btn-sm text-sm rounded"
                                            data-bs-toggle="modal" data-bs-target="#billCreateModalMbbank">
                                            <i class="fas fa-file-invoice"></i> Tạo Hoá đơn [Bảo trì]
                                        </button>
                                        <div class="modal fade text-dark" id="mbbankModal" tabindex="-1"
                                            aria-labelledby="mbbankModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="mbbankModalLabel">Mã QR</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body" style="font-size: 1.5rem;">
                                                        <div class="d-flex jutify-content-center">
                                                            <img src="https://img.vietqr.io/image/mb-{{ $mbbank->account_number }}-qronly2.jpg?accountName={{ $mbbank->account_name }}&addInfo={{ siteValue('transfer_code') }}{{ Auth::user()->id }}&amount="
                                                                alt="QR CODE" width="100%">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Tạo hoá đơn --}}
                                        <div class="modal fade text-dark" id="billCreateModalMbbank" tabindex="-1"
                                            aria-labelledby="billCreateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="billCreateModalLabel">Nhập số tiền cần
                                                            nạp</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('create.bill') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="bank_code"
                                                            value="{{ base64_encode('MBBank') }}">
                                                        <div class="modal-body" id="t0">
                                                            <div class="form-group">
                                                                <label for="amount" class="form-label">Số tiền</label>
                                                                <input type="number" class="form-control" id="amount"
                                                                    oninput="changeAmount(this.value, 0)" name="amount">
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="">
                                                                    <div class="fw-bold">Số tiền cần thanh toán</div>
                                                                    <span id="amountPayMbbank"
                                                                        class="fw-bold text-primary fs-4">0</span> VNĐ
                                                                </div>
                                                                {{-- Số tiền nhận được --}}
                                                                <div class="">
                                                                    <div class="fw-bold">Số tiền nhận được</div>
                                                                    <span id="amountReceivesMbbank"
                                                                        class="fw-bold text-success fs-4">0</span> VNĐ
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Tạo hoá
                                                                đơn[Bảo trì]</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($techcombank->status === 'active')
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-img d-flex justify-content-center align-items-center py-3 w-100">
                                <img src="{{ asset($techcombank->logo) }}" alt="Logo Techcombank" width="160">
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center flex-column align-items-center">
                                    <div class="">
                                        Ngân Hàng: <span class="fw-bold">Techcombank</span>
                                    </div>
                                    <div class="">
                                        Chủ tài khoản: <span class="fw-bold">{{ $techcombank->account_name }}
                                    </div>
                                    <div class="">
                                        Số tài khoản: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ $techcombank->account_number }}')">{{ $techcombank->account_number }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="">
                                        Nạp tối thiểu: <span
                                            class="fw-bold">{{ number_format($techcombank->min_recharge) }}
                                            VNĐ</span>
                                    </div>
                                    <div class="">
                                        Nội dung: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ siteValue('transfer_code') }}{{ Auth::user()->id }}')"
                                            class="cursor-pointer">{{ siteValue('transfer_code') }}{{ Auth::user()->id }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-center gap-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#techcombankModal">
                                            <i class="fas fa-qrcode"></i> QR CODE [Hoạt động]
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm text-sm rounded"
                                            data-bs-toggle="modal" data-bs-target="#billCreateModalTechcombank">
                                            <i class="fas fa-file-invoice"></i> Tạo Hoá đơn[Bảo trì]
                                        </button>
                                        <div class="modal fade text-dark" id="techcombankModal" tabindex="-1"
                                            aria-labelledby="techcombankModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="techcombankModalLabel">Mã QR</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="d-flex jutify-content-center">
                                                            <img src="https://img.vietqr.io/image/techcombank-{{ $techcombank->account_number }}-qronly2.jpg?accountName={{ $techcombank->account_name }}&addInfo={{ siteValue('transfer_code') }}{{ Auth::user()->id }}&amount="
                                                                alt="QR CODE" width="100%">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade text-dark" id="billCreateModalTechcombank" tabindex="-1"
                                            aria-labelledby="billCreateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="billCreateModalLabel">Nhập số tiền cần
                                                            nạp</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('create.bill') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="bank_code"
                                                            value="{{ base64_encode('Techcombank') }}">
                                                        <div class="modal-body" id="t1">
                                                            <div class="form-group">
                                                                <label for="amount" class="form-label">Số tiền</label>
                                                                <input type="number" class="form-control" id="amount"
                                                                    oninput="changeAmount(this.value, 1)" name="amount">
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="">
                                                                    <div class="fw-bold">Số tiền cần thanh toán</div>
                                                                    <span id="amountPayTechcombank"
                                                                        class="fw-bold text-primary fs-4">0</span> VNĐ
                                                                </div>
                                                                {{-- Số tiền nhận được --}}
                                                                <div class="">
                                                                    <div class="fw-bold">Số tiền nhận được</div>
                                                                    <span id="amountReceivesTechcombank"
                                                                        class="fw-bold text-success fs-4">0</span> VNĐ
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Tạo hoá
                                                                đơn[Bảo trì]</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($acb->status === 'active')
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-img d-flex justify-content-center align-items-center py-3 w-100">
                                <img src="{{ asset($acb->logo) }}" alt="Logo ACB" width="160">
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center flex-column align-items-center">
                                    <div class="">
                                        Ngân Hàng: <span class="fw-bold">ACB</span>
                                    </div>
                                    <div class="">
                                        Chủ tài khoản: <span class="fw-bold">{{ $acb->account_name }}
                                    </div>
                                    <div class="">
                                        Số tài khoản: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ $acb->account_number }}')">{{ $acb->account_number }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="">
                                        Nạp tối thiểu: <span class="fw-bold">{{ number_format($acb->min_recharge) }}
                                            VNĐ</span>
                                    </div>
                                    <div class="">
                                        Nội dung: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ siteValue('transfer_code') }}{{ Auth::user()->id }}')"
                                            class="cursor-pointer">{{ siteValue('transfer_code') }}{{ Auth::user()->id }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-center gap-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#ACB">
                                            <i class="fas fa-qrcode"></i> QR CODE [Hoạt động]
                                        </button>

                                        <button type="button" class="btn btn-success btn-sm text-sm rounded"
                                            data-bs-toggle="modal" data-bs-target="#billCreateModalACB">
                                            <i class="fas fa-file-invoice"></i> Tạo Hoá đơn[Bảo trì]
                                        </button>

                                        <div class="modal fade text-dark" id="ACB" tabindex="-1"
                                            aria-labelledby="ACBLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="ACBLabel">Mã QR</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="d-flex jutify-content-center">
                                                            <img src="https://img.vietqr.io/image/ACB-{{ $acb->account_number }}-qronly2.jpg?accountName={{ $acb->account_name }}&addInfo={{ siteValue('transfer_code') }}{{ Auth::user()->id }}&amount="
                                                                alt="QR CODE" width="100%">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade text-dark" id="billCreateModalACB" tabindex="-1"
                                            aria-labelledby="billCreateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="billCreateModalLabel">Nhập số tiền cần
                                                            nạp</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('create.bill') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="bank_code"
                                                            value="{{ base64_encode('ACB') }}">
                                                        <div class="modal-body" id="t2">
                                                            <div class="form-group">
                                                                <label for="amount" class="form-label">Số tiền</label>
                                                                <input type="number" class="form-control" id="amount"
                                                                    oninput="changeAmount(this.value, 2)" name="amount">
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="">
                                                                    <div class="fw-bold">Số tiền cần thanh toán</div>
                                                                    <span id="amountPayACB"
                                                                        class="fw-bold text-primary fs-4">0</span> VNĐ
                                                                </div>
                                                                {{-- Số tiền nhận được --}}
                                                                <div class="">
                                                                    <div class="fw-bold">Số tiền nhận được</div>
                                                                    <span id="amountReceivesACB"
                                                                        class="fw-bold text-success fs-4">0</span> VNĐ
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Tạo hoá
                                                                đơn[Bảo trì]</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($viettinbank->status === 'active')
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-img d-flex justify-content-center align-items-center py-3 w-100">
                                <img src="{{ asset($viettinbank->logo) }}" alt="Logo ACB" width="160">
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center flex-column align-items-center">
                                    <div class="">
                                        Ngân Hàng: <span class="fw-bold">Viettinbank</span>
                                    </div>
                                    <div class="">
                                        Chủ tài khoản: <span class="fw-bold">{{ $viettinbank->account_name }}
                                    </div>
                                    <div class="">
                                        Số tài khoản: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ $viettinbank->account_number }}')">{{ $viettinbank->account_number }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="">
                                        Nạp tối thiểu: <span
                                            class="fw-bold">{{ number_format($viettinbank->min_recharge) }}
                                            VNĐ</span>
                                    </div>
                                    <div class="">
                                        Nội dung: <span class="fw-bold cursor-pointer text-primary"
                                            onclick="coppy('{{ siteValue('transfer_code') }}{{ Auth::user()->id }}')"
                                            class="cursor-pointer">{{ siteValue('transfer_code') }}{{ Auth::user()->id }}
                                            <i class="fas fa-copy"></i></span>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-center gap-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#ACB">
                                            <i class="fas fa-qrcode"></i> QR CODE [Hoạt động]
                                        </button>

                                        <button type="button" class="btn btn-success btn-sm text-sm rounded"
                                            data-bs-toggle="modal" data-bs-target="#billCreateModalViettin">
                                            <i class="fas fa-file-invoice"></i> Tạo Hoá đơn[Bảo trì]
                                        </button>

                                        <div class="modal fade text-dark" id="ACB" tabindex="-1"
                                            aria-labelledby="ACBLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="ACBLabel">Mã QR</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="d-flex jutify-content-center">
                                                            <img src="https://img.vietqr.io/image/ICB-{{ $viettinbank->account_number }}-qronly2.jpg?accountName={{ $viettinbank->account_name }}&addInfo={{ siteValue('transfer_code') }}{{ Auth::user()->id }}&amount="
                                                                alt="QR CODE" width="100%">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade text-dark" id="billCreateModalViettin" tabindex="-1"
                                            aria-labelledby="billCreateModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="billCreateModalLabel">Nhập số tiền cần
                                                            nạp</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('create.bill') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="bank_code"
                                                            value="{{ base64_encode('Viettinbank') }}">
                                                        <div class="modal-body" id="t2">
                                                            <div class="form-group">
                                                                <label for="amount" class="form-label">Số tiền</label>
                                                                <input type="number" class="form-control" id="amount"
                                                                    oninput="changeAmount(this.value, 3)" name="amount">
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="">
                                                                    <div class="fw-bold">Số tiền cần thanh toán</div>
                                                                    <span id="amountPayViettin"
                                                                        class="fw-bold text-primary fs-4">0</span> VNĐ
                                                                </div>
                                                                {{-- Số tiền nhận được --}}
                                                                <div class="">
                                                                    <div class="fw-bold">Số tiền nhận được</div>
                                                                    <span id="amountReceivesViettin"
                                                                        class="fw-bold text-success fs-4">0</span> VNĐ
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Tạo hoá
                                                                đơn[Bảo trì]</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
        <div class="col-md-12">
            <div class="border-top border-start border-end py-2 border-success bg-primary rounded-top-2 mb-0">
                <h5 class="text-white text-center mb-0">NỘI DUNG: (BẮT BUỘC GHI ĐÚNG NỘI DUNG DƯỚI ĐÂY)</h5>
            </div>
            <div class="alert alert-primary border-primary text-primary rounded-top-0 fw-bold fs-3 text-center">
                <span onclick="coppy('{{ siteValue('transfer_code') }}{{ Auth::user()->id }}')"
                    class="cursor-pointer">{{ siteValue('transfer_code') }}{{ Auth::user()->id }} <i
                        class="fas fa-copy"></i></span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Lịch sử nạp tiền</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mã giao dịch</th>
                                    <th>Thời gian</th>
                                    <th>Loại giao dịch</th>
                                    <th>Cổng thanh toán</th>
                                    <th>Người chuyển</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Nội dung</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recharges as $recharge)
                                    <tr>
                                        <td>{{ $recharge->id }}</td>
                                        <td>
                                            @if ($recharge->type === 'bill')
                                                <span class="badge bg-success">{{ $recharge->order_code }}</span>
                                            @else
                                                <span class="badge bg-primary">
                                                    {{ $recharge->bankcode() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $recharge->created_at }}</td>
                                        <td>
                                            @if ($recharge->type === 'bill')
                                                <span class="badge bg-warning">Thanh toán hóa đơn</span>
                                            @elseif ($recharge->type == 'recharge')
                                                <span class="badge bg-primary">Nạp tiền</span>
                                            @endif
                                        </td>
                                        <td>{{ $recharge->bank_name }}</td>
                                        <td>Không xác định</td>
                                        <td>{{ number_format($recharge->real_amount) }} VNĐ</td>
                                        <td>
                                            @if ($recharge->status === 'Pending')
                                                <span class="badge bg-warning">Đang chờ thanh toán</span>
                                            @elseif ($recharge->status === 'Success')
                                                <span class="badge bg-success">Thành công</span>
                                            @else
                                                <span class="badge bg-danger">Thất bại</span>
                                            @endif
                                        </td>
                                        <td>
                                            <textarea class="form-control" rows="1" readonly>{{ $recharge->note }}</textarea>
                                        </td>
                                        <td>
                                            @if ($recharge->status === 'Pending' && $recharge->type === 'bill')
                                                {{-- xemm bil --}}
                                                <a href="{{ route('payment.bill', $recharge->order_code) }}"
                                                    class="btn btn-primary btn-sm btn-icon">
                                                    <i class="fas fa-eye text-white"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        const promotion = {{ site('percent_promotion') ?? 0 }};

        function changeAmount(amount, type) {
            // const amountPay = document.getElementById('amountPay');
            // const amountReceives = document.getElementById('amountReceives');
            // amountPay.innerText = Intl.NumberFormat().format(amount);
            // const receives = amount - (amount * promotion / 100);
            // amountReceives.innerText = Intl.NumberFormat().format(receives);

            const amountPayMbbank = document.getElementById('amountPayMbbank');
            const amountReceivesMbbank = document.getElementById('amountReceivesMbbank');
            const amountPayTechcombank = document.getElementById('amountPayTechcombank');
            const amountReceivesTechcombank = document.getElementById('amountReceivesTechcombank');
            const amountPayACB = document.getElementById('amountPayACB');
            const amountReceivesACB = document.getElementById('amountReceivesACB');
            const amountPayViettin = document.getElementById('amountPayViettin');
            const amountReceivesViettin = document.getElementById('amountReceivesViettin');


            if (type === 0) {
                amountPayMbbank.innerText = Intl.NumberFormat().format(amount);
                const receives = amount - (amount * promotion / 100);
                amountReceivesMbbank.innerText = Intl.NumberFormat().format(receives);
            } else if (type === 1) {
                amountPayTechcombank.innerText = Intl.NumberFormat().format(amount);
                const receives = amount - (amount * promotion / 100);
                amountReceivesTechcombank.innerText = Intl.NumberFormat().format(receives);
            } else if (type === 2) {
                amountPayACB.innerText = Intl.NumberFormat().format(amount);
                const receives = amount - (amount * promotion / 100);
                amountReceivesACB.innerText = Intl.NumberFormat().format(receives);
            } else if (type === 3) {
                amountPayViettin.innerText = Intl.NumberFormat().format(amount);
                const receives = amount - (amount * promotion / 100);
                amountReceivesViettin.innerText = Intl.NumberFormat().format(receives);
            }
        }
    </script>
@endsection
