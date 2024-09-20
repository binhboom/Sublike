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
                                    <th>Số dư</th>
                                    <th>Số hiện tại</th>
                                    <th>Số dư bây giờ</th>
                                    <th>Ghi chú</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ $transaction->user->username }}</td>
                                        <td>{{ $transaction->tran_code }}</td>
                                        <td>
                                            @if ($transaction->type == 'recharge')
                                                <span class="badge bg-success">Nạp tiền</span>
                                            @elseif ($transaction->type == 'order')
                                                <span class="badge bg-primary">Tạo đơn</span>
                                            @elseif ($transaction->type == 'refund')
                                                <span class="badge bg-danger">Hoàn tiền</span>
                                            @elseif ($transaction->type == 'balance')
                                                <span class="badge bg-warning">Điều chỉnh số dư</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($transaction->action == 'add')
                                                <span
                                                    class="text-success">+{{ number_format($transaction->first_balance) }}</span>
                                            @elseif ($transaction->action == 'sub')
                                                <span
                                                    class="text-danger">-{{ number_format($transaction->first_balance) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-primary">{{ number_format($transaction->before_balance) }}</td>
                                        <td class="text-danger">{{ number_format($transaction->after_balance) }}</td>
                                        <td>
                                            <textarea class="form-control" rows="1" readonly>{{ $transaction->note }}</textarea>
                                        </td>
                                        <td>{{ $transaction->ip }}</td>
                                    </tr>
                                @empty
                                    @include('admin.components.table-search-not-found', ['colspan' => 10])
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center align-items-center mt-4">
                            {{ $transactions->appends(request()->all())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
