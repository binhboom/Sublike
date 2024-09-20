@extends('admin.layouts.master')

@section('title', 'Lịch sử nạp tiền')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lịch sử nạp tiền</h4>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="search" name="search"
                                        value="{{ request('search') }}" placeholder="Nhập tài khoản hoặc mã giao dịch">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped fw-bold mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tài khoản</th>
                                    <th>Mã giao dịch</th>
                                    <th>Loại</th>
                                    <th>Người chuyển</th>
                                    <th>Số tiền</th>
                                    <th>Thực nhận</th>
                                    <th>Nội dung</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $key => $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ $payment->user->username }}</td>
                                        <td>{{ $payment->bank_code }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>{{ $payment->bank_name }}</td>
                                        <td>{{ number_format($payment->amount) }}đ</td>
                                        <td>{{ number_format($payment->real_amount) }}đ</td>
                                        <td>{{ $payment->note }}</td>
                                        <td>{{ $payment->created_at }}</td>
                                    </tr>
                                @empty
                                    @include('admin.components.table-search-not-found', ['colspan' => 9])
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center align-items-center">
                            {{ $payments->appends(request()->all())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
