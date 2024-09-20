@extends('admin.layouts.master')
@section('title', 'Chỉnh sửa đơn hàng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Chỉnh sửa đơn hàng</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.order.action', ['id' => $order->id]) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="" class="fomr-label">Trạng thái</label>
                                {{-- <input type="text" class="form-control" placeholder="Status" name="status"
                                    value="{{ $order->status }}" readonly> --}}
                                <select class="form-select" name="status">
                                    <option value="Processing" {{ $order->status == 'Processing' ? 'selected' : '' }}>Đang
                                        xử
                                        lý</option>
                                    <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>
                                        Hoàn thành</option>
                                    <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>
                                        Đã hủy</option>
                                    <option value="Refunded" {{ $order->status == 'Refunded' ? 'selected' : '' }}>Đã
                                        hoàn tiền</option>
                                    <option value="Failed" {{ $order->status == 'Failed' ? 'selected' : '' }}>Thất
                                        bại</option>
                                    <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Chờ
                                        xử lý</option>
                                    <option value="Partially Refunded"
                                        {{ $order->status == 'Partially Refunded' ? 'selected' : '' }}>Hoàn tiền một
                                        phần</option>
                                    <option value="Partially Completed"
                                        {{ $order->status == 'Partially Completed' ? 'selected' : '' }}>Hoàn thành
                                        một phần</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="" class="fomr-label">Bắt đầu</label>
                                <input type="text" class="form-control" placeholder="Bắt đầu" name="start"
                                    value="{{ $order->start }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="" class="fomr-label">Đã tăng</label>
                                <input type="text" class="form-control" placeholder="Đã tăng" name="buff"
                                    value="{{ $order->buff }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary col-12">Chỉnh sửa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
