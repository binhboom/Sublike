@extends('admin.layouts.master')
@section('title', 'Cấu hình nạp tiền')

@section('content')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="py-2 px-3">
                    <ul class="nav nav-tabs profile-tabs justify-content-center" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="config-tab" data-bs-toggle="tab" href="#config" role="tab"
                                aria-selected="true">
                                <i class="ti ti-info-circle me-2"></i>Cấu hình nạp tiền
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="promotion-tab" data-bs-toggle="tab" href="#promotion" role="tab"
                                aria-selected="true">
                                <i class="ti ti-brand-airbnb me-2"></i>
                                Khuyến mãi nạp
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane active show" id="config" role="tabpanel" aria-labelledby="config-tab">
                    <div class="row">
                        <div class="card">
                            <div class="card-header">
                                <h5>Cấu hình nạp tiền [GACHTHENHANH.NET]</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.payment.config.update') }}" method="POST">
                                    @csrf
                                    <div class="form-floating mb-3">
                                        <input type="date" name="start_promotion" id="start_promotion"
                                            value="{{ siteValue('start_promotion') }}" class="form-control">
                                        <label for="start_promotion">Ngày bắt đầu khuyến mãi</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="date" name="end_promotion" id="end_promotion"
                                            value="{{ siteValue('end_promotion') }}" class="form-control">
                                        <label for="end_promotion">Ngày kết thúc khuyến mãi</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="number" name="percent_promotion" id="percent_promotion"
                                            value="{{ siteValue('percent_promotion') }}" class="form-control">
                                        <label for="percent_promotion">Phần trăm khuyến mãi</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" name="transfer_code" id="transfer_code"
                                            value="{{ siteValue('transfer_code') }}" class="form-control">
                                        <label for="transfer_code">Mã nạp tiền</label>
                                    </div>
                                    <b class="mb-3">Cấu hình thẻ cào</b>
                                    <div class="form-floating mb-3">
                                        <input type="text" name="partner_id" id="partner_id"
                                            value="{{ siteValue('partner_id') }}" class="form-control">
                                        <label for="partner_id">Partner ID</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" name="partner_key" id="partner_key"
                                            value="{{ siteValue('partner_key') }}" class="form-control">
                                        <label for="partner_key">Partner Key</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" name="percent_card" id="percent_card"
                                            value="{{ siteValue('percent_card') }}" class="form-control">
                                        <label for="percent_card">Chiết khấu thẻ</label>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save"></i>
                                            Lưu cấu hình
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Cấu hình auto MOMO ( <a href="http://api.vpnfast.vn/"
                                            class="text-primary" target="_blank" rel="noopener noreferrer">Thuê Tại Đây</a>
                                        )</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.payment.update', ['bank_name' => $momo->bank_name]) }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <select name="status" id="status" class="form-select">
                                                <option value="active" {{ $momo->status == 'active' ? 'selected' : '' }}>Bật
                                                </option>
                                                <option value="inactive"
                                                    {{ $momo->status == 'inactive' ? 'selected' : '' }}>Tắt
                                                </option>
                                            </select>
                                            <label for="status">Trạng thái</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_name" id="account_name"
                                                value="{{ $momo->account_name }}" placeholder="Nhập dữ liệu">
                                            <label for="account_name">Chủ tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_number"
                                                id="account_number" value="{{ $momo->account_number }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_number">Số điện thoại</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="min_recharge"
                                                id="min_recharge" value="{{ $momo->min_recharge }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="min_recharge">Nạp tối thiểu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="api_key" id="api_key"
                                                value="{{ $momo->token }}" placeholder="Nhập dữ liệu">
                                            <label for="api_key">Api Token</label>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-save"></i>
                                                Lưu cấu hình
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Cấu hình auto MB Bank ( <a href="https://api.vpnfast.vn/"
                                            class="text-primary" target="_blank" rel="noopener noreferrer">Thuê Tại
                                            api.vpnfast.vn</a> )</h5>
                                </div>
                                <div class="card-body">
                                    <form
                                        action="{{ route('admin.payment.update', ['bank_name' => $mbbank->bank_name]) }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <select name="status" id="status" class="form-select">
                                                <option value="active"
                                                    {{ $mbbank->status == 'active' ? 'selected' : '' }}>Bật
                                                </option>
                                                <option value="inactive"
                                                    {{ $mbbank->status == 'inactive' ? 'selected' : '' }}>Tắt
                                                </option>
                                            </select>
                                            <label for="status">Trạng thái</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_name"
                                                id="account_name" value="{{ $mbbank->account_name }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_name">Chủ tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_number"
                                                value="{{ $mbbank->account_number }}" id="account_number"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_number">Số tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="min_recharge"
                                                id="min_recharge" value="{{ $mbbank->min_recharge }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="min_recharge">Nạp tối thiểu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_username"
                                                id="account_username" value="{{ $mbbank->bank_account }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_username">Tài khoản</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="account_password"
                                                id="account_password" value="{{ $mbbank->bank_password }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_password">Mật khẩu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="api_key" id="api_key"
                                                value="{{ $mbbank->token }}" placeholder="Nhập dữ liệu">
                                            <label for="api_key">Api Token</label>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-save"></i>
                                                Lưu cấu hình
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Cấu hình auto Techcombank ( <a
                                            href="http://api.vpnfast.vn/" class="text-primary" target="_blank"
                                            rel="noopener noreferrer">Thuê Tại Đây</a> )</h5>
                                </div>
                                <div class="card-body">
                                    <form
                                        action="{{ route('admin.payment.update', ['bank_name' => $techcombank->bank_name]) }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <select name="status" id="status" class="form-select">
                                                <option value="active"
                                                    {{ $techcombank->status == 'active' ? 'selected' : '' }}>Bật
                                                </option>
                                                <option value="inactive"
                                                    {{ $techcombank->status == 'inactive' ? 'selected' : '' }}>
                                                    Tắt
                                                </option>
                                            </select>
                                            <label for="status">Trạng thái</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_name"
                                                id="account_name" value="{{ $techcombank->account_name }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_name">Chủ tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_number"
                                                value="{{ $techcombank->account_number }}" id="account_number"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_number">Số tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="min_recharge"
                                                id="min_recharge" value="{{ $techcombank->min_recharge }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="min_recharge">Nạp tối thiểu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_username"
                                                id="account_username" value="{{ $techcombank->bank_account }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_username">Tài khoản</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="account_password"
                                                id="account_password" value="{{ $techcombank->bank_password }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_password">Mật khẩu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="api_key" id="api_key"
                                                value="{{ $techcombank->token }}" placeholder="Nhập dữ liệu">
                                            <label for="api_key">Api Token</label>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-save"></i>
                                                Lưu cấu hình
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Cấu hình auto ACB ( <a href="http://api.vpnfast.vn/"
                                            class="text-primary" target="_blank" rel="noopener noreferrer">Thuê Tại
                                             api.vpnfast.vn</a> )</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.payment.update', ['bank_name' => $acb->bank_name]) }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <select name="status" id="status" class="form-select">
                                                <option value="active" {{ $acb->status == 'active' ? 'selected' : '' }}>
                                                    Bật</option>
                                                <option value="inactive"
                                                    {{ $acb->status == 'inactive' ? 'selected' : '' }}>Tắt
                                                </option>
                                            </select>
                                            <label for="status">Trạng thái</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_name"
                                                id="account_name" value="{{ $acb->account_name }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_name">Chủ tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_number"
                                                value="{{ $acb->account_number }}" id="account_number"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_number">Số tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="min_recharge"
                                                id="min_recharge" value="{{ $acb->min_recharge }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="min_recharge">Nạp tối thiểu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_username"
                                                id="account_username" value="{{ $acb->bank_account }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_username">Tài khoản</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="account_password"
                                                id="account_password" value="{{ $acb->bank_password }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_password">Mật khẩu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="api_key" id="api_key"
                                                value="{{ $acb->token }}" placeholder="Nhập dữ liệu">
                                            <label for="api_key">Api Token</label>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-save"></i>
                                                Lưu cấu hình
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Cấu hình auto Viettinbank ( <a
                                            href="http://api.vpnfast.vn/" class="text-primary" target="_blank"
                                            rel="noopener noreferrer">Thuê Tại Đây</a> )</h5>
                                </div>
                                <div class="card-body">
                                    <form
                                        action="{{ route('admin.payment.update', ['bank_name' => $viettinbank->bank_name]) }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <select name="status" id="status" class="form-select">
                                                <option value="active"
                                                    {{ $viettinbank->status == 'active' ? 'selected' : '' }}>Bật
                                                </option>
                                                <option value="inactive"
                                                    {{ $viettinbank->status == 'inactive' ? 'selected' : '' }}>
                                                    Tắt
                                                </option>
                                            </select>
                                            <label for="status">Trạng thái</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_name"
                                                id="account_name" value="{{ $viettinbank->account_name }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_name">Chủ tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_number"
                                                value="{{ $viettinbank->account_number }}" id="account_number"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_number">Số tài khoản</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="min_recharge"
                                                id="min_recharge" value="{{ $viettinbank->min_recharge }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="min_recharge">Nạp tối thiểu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="account_username"
                                                id="account_username" value="{{ $viettinbank->bank_account }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_username">Tài khoản</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="account_password"
                                                id="account_password" value="{{ $viettinbank->bank_password }}"
                                                placeholder="Nhập dữ liệu">
                                            <label for="account_password">Mật khẩu</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" name="api_key" id="api_key"
                                                value="{{ $viettinbank->token }}" placeholder="Nhập dữ liệu">
                                            <label for="api_key">Api Token</label>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-save"></i>
                                                Lưu cấu hình
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="promotion" role="tabpanel" aria-labelledby="promotion-tab">
                    <div class="row">
                        <div class="card mb-5">
                            <div class="card-header">
                                <h5>Khuyến mãi nạp</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.payment.promotion.create') }}" method="POST">
                                    @csrf
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="min_balance" value="{{ old('min_balance') }}"
                                            placeholder="Số tiền tối thiểu">
                                        <label for="min_balance">Số tiền tối thiểu</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="percentage" value="{{ old('percentage') }}"
                                            placeholder="Phần trăm">
                                        <label for="percentage">Chiết khấu khuyến mãi</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <select name="status" id="status" class="form-select">
                                            <option value="active" selected>Bật</option>
                                            <option value="inactive">Tắt</option>
                                        </select>
                                        <label for="status">Trạng thái</label>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save"></i>
                                            Lưu cấu hình
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5>Danh sách Khuyến mãi nạp</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Số tiền tối thiểu</th>
                                                <th>Phần trăm</th>
                                                <th>Trạng thái</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($listRechargePromotion->isEmpty())
                                                @include('admin.components.table-search-not-found', [
                                                    'colspan' => 5,
                                                ])
                                            @endif
                                            @foreach ($listRechargePromotion as $key => $promotion)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ number_format($promotion->min_balance) }} VNĐ</td>
                                                    <td>{{ $promotion->percentage }}%</td>
                                                    <td>
                                                        @if ($promotion->status == 'active')
                                                            <span class="badge bg-success">Bật</span>
                                                        @else
                                                            <span class="badge bg-danger">Tắt</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.payment.promotion.edit', ['id' => $promotion->id]) }}"
                                                            class="btn btn-primary btn-sm text-xs">
                                                            <i class="fas fa-edit"></i>
                                                            Sửa
                                                        </a>
                                                        <a href="{{ route('admin.payment.promotion.delete', ['id' => $promotion->id]) }}"
                                                            class="btn btn-danger btn-sm text-xs">
                                                            <i class="fas fa-trash"></i>
                                                            Xóa
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center align-items-center">
                                        {{ $listRechargePromotion->appends(request()->all())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
