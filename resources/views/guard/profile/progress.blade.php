@extends('guard.layouts.master')
@section('title', 'Tiến Trình')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tất cả tiến trình</h5>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Tìm kiếm mã đơn" name="search" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped fw-bold">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Trạng thái</th>
                                    <th>Mã đơn</th>
                                    <th>Đường dẫn</th>
                                    <th>Dịch vụ</th>
                                    <th>Máy chủ</th>
                                    <th>Số lượng</th>
                                    <th>Bắt đầu</th>
                                    <th>Đã tăng</th>
                                    <th>Giá tiền</th>
                                    <th>Ghi chú</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($orders->isEmpty())
                                    @include('admin.components.table-search-not-found', ['colspan' => 12])
                                @else
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{!! statusOrder($order->status, true) !!}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>
                                                <a
                                                    href="{{ route('service', ['platform' => $order->service->platform->slug, 'service' => $order->service->slug]) }}">{{ $order->service->name }}</a>
                                            </td>
                                            <td>{{ $order->service->platform->name }}</td>
                                            <td>{{ $order->object_server }}</td>
                                            <td>{{ number_format($order->orderdata()['quantity']) }}</td>
                                            <td>{{ number_format($order->start) }}</td>
                                            <td>{{ number_format($order->buff) }}</td>
                                            <td>{{ $order->price }}</td>
                                            <td>
                                                <textarea class="form-control" rows="1" readonly>{{ $order->note }}</textarea>
                                            </td>
                                            <td>{{ $order->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="d-flex jsutify-content-center align-items-center">
                            {{ $orders->appends(request()->all())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
