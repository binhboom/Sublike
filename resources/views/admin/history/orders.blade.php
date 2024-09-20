@extends('admin.layouts.master')
@section('title', 'Lịch sử đơn hàng')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lịch sử tạo đơn</h4>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" id="search" name="search"
                                        value="{{ request('search') }}" placeholder="Nhập tài khoản hoặc mã đơn hàng">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <select name="status" id="status" class="form-select">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="Processing"
                                            {{ request('status') == 'Processing' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>
                                            Hoàn thành</option>
                                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>
                                            Đã hủy</option>
                                        <option value="Refunded" {{ request('status') == 'Refunded' ? 'selected' : '' }}>Đã
                                            hoàn tiền</option>
                                        <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>Thất
                                            bại</option>
                                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Chờ
                                            xử lý</option>
                                        <option value="Partially Refunded"
                                            {{ request('status') == 'Partially Refunded' ? 'selected' : '' }}>Hoàn tiền một
                                            phần</option>
                                        <option value="Partially Completed"
                                            {{ request('status') == 'Partially Completed' ? 'selected' : '' }}>Hoàn thành
                                            một phần</option>
                                    </select>
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
                                    <th>Thao tác</th>
                                    <th>Tài khoản</th>
                                    <th>Mã đơn hàng</th>
                                    <th>Trạng thái</th>
                                    <th>Dữ liệu</th>
                                    <th>Bắt đầu</th>
                                    <th>Đã tăng</th>
                                    <th>Bình luận</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($orders->isEmpty())
                                    @include('admin.components.table-search-not-found', ['colspan' => 8])
                                @else
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>
                                                @if ($order->server->providerName === 'codedynamic')
                                                    {{-- @if ($order->status === 'Processing')
                                                        <a href="{{ route('admin.order.action', ['id' => $order->id, 'action' => 'Running']) }}"
                                                            class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Duyệt đơn">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="{{ route('admin.order.action', ['id' => $order->id, 'action' => 'Cancelled']) }}"
                                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Huỷ đơn">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @elseif ($order->status === 'Running')
                                                        <a href="{{ route('admin.order.action', ['id' => $order->id, 'action' => 'Completed']) }}"
                                                            class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Hoàn thành">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="{{ route('admin.order.action', ['id' => $order->id, 'action' => 'Cancelled']) }}"
                                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Huỷ đơn">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif --}}
                                                    <form action="{{ route('admin.order.action', ['id' => $order->id]) }}"
                                                        method="POST" class="d-flex flex-column align-items-center">
                                                        @csrf
                                                        <div class="form-group">
                                                            <select name="status" id="" class="form-select">
                                                                <option value="Processing"
                                                                    {{ $order->status === 'Processing' ? 'selected' : '' }}>
                                                                    Đang xử lý</option>
                                                                <option value="Completed"
                                                                    {{ $order->status === 'Completed' ? 'selected' : '' }}>
                                                                    Hoàn thành</option>
                                                                <option value="Cancelled"
                                                                    {{ $order->status === 'Cancelled' ? 'selected' : '' }}>
                                                                    Đã hủy</option>
                                                                <option value="Refunded"
                                                                    {{ $order->status === 'Refunded' ? 'selected' : '' }}>
                                                                    Đã hoàn tiền</option>
                                                                <option value="Failed"
                                                                    {{ $order->status === 'Failed' ? 'selected' : '' }}>
                                                                    Thất bại</option>
                                                                <option value="Pending"
                                                                    {{ $order->status === 'Pending' ? 'selected' : '' }}>
                                                                    Chờ xử lý</option>
                                                                <option value="WaitingForRefund"
                                                                    {{ $order->status === 'WaitingForRefund' ? 'selected' : '' }}>
                                                                    Chờ hoàn tiền</option>
                                                                <option value="Expired"
                                                                    {{ $order->status === 'Expired' ? 'selected' : '' }}>
                                                                    Hết hạn</option>
                                                                <option value="Success"
                                                                    {{ $order->status === 'Success' ? 'selected' : '' }}>
                                                                    Thành công</option>
                                                                <option value="Active"
                                                                    {{ $order->status === 'Active' ? 'selected' : '' }}>
                                                                    Đang hoạt động</option>
                                                            </select>
                                                        </div>
                                                        <div class="">
                                                            <button type="submit" class="btn btn-sm btn-primary"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Cập nhật trạng thái">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endif
                                                {{-- sửa --}}
                                                <div class=" d-flex justify-content-center mt-1">
                                                    <a href="{{ route('admin.order.edit', ['id' => $order->id]) }}"
                                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                                {{-- xoá  --}}
                                                <div class=" d-flex justify-content-center mt-1">
                                                    <a href="{{ route('admin.order.delete', ['id' => $order->id]) }}"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xoá đơn hàng này không?')"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Xoá">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $order->user->username }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>
                                                {!! statusOrder($order->status, true) !!}
                                            </td>
                                            <td>
                                                <ul>
                                                    @if (env('APP_MAIN_SITE') === request()->getHost())
                                                        <li>Mã đối tác: {{ $order->order_id }}</li>
                                                    @else
                                                        <li>Mã đơn hàng: {{ $order->order_code }}</li>
                                                    @endif
                                                    <li>Đường dẫn: {{ $order->object_id }}</li>
                                                    <li>Máy chủ: {{ $order->object_server }}</li>
                                                    <li>Dịch vụ: {{ $order->service->name }}</li>
                                                    <li>Số lượng:
                                                        {{ number_format(json_decode($order->order_data, true)['quantity']) }}
                                                    </li>
                                                    <li>Giá: {{ $order->price }} đ</li>
                                                    <li>Thành tiền: {{ number_format($order->payment) }} đ</li>
                                                    <li>Thời gian tạo: {{ $order->created_at }}</li>
                                                </ul>
                                            </td>
                                            <td>{{ $order->start }}</td>
                                            <td>{{ $order->buff }}</td>
                                            <td>
                                                <textarea class="form-control" rows="3" readonly>{{ html_entity_decode(json_decode($order->order_data, true)['comments'], ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</textarea>
                                            </td>
                                            <td>
                                                <textarea class="form-control" rows="3" readonly>{{ $order->note }}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center align-items-center">
                            {{ $orders->appends(request()->all())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
