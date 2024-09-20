@extends('guard.layouts.master')
@section('title', $service->name . ' | ' . $platform->name)

@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center gap-2 mb-5">
                        <svg class="pc-icon text-primary">
                            <use xlink:href="#custom-bag"></use>
                        </svg>
                        <span class="text-primary">Tạo đơn hàng mới: {{ $service->package }}</span>
                    </h5>

                    <form action="{{ route('api.create.order') }}" method="POST" lbd-request="order">
                        <input type="hidden" name="provider_package" value="{{ $service->package }}">
                        <div class="form-group mb-3">
                            <label for="object_id" class="form-label"><strong>Link Hoặc UID:</strong></label>
                            <input type="text" class="form-control" id="object_id" name="object_id"
                                placeholder="Nhập link hoặc ID tuỳ các máy chủ">
                        </div>
                        <div class="form-group mb-3">
                            <label for="" class="form-label"><strong>Máy chủ:</strong></label>
                            @foreach ($service->servers->where('visibility', 'public')->where('domain', request()->getHost()) as $server)
                                <div class="form-check mb-2 d-flex align-items-center gap-2">
                                    <input type="radio"
                                        class="form-check-input {{ $server->status === 'active' ? 'input-light-primary' : 'input-light-danger' }}"
                                        name="provider_server" value="sv-{{ $server->package_id }}"
                                        id="provider-server-{{ $server->package_id }}" data-details="{{ $server->details }}"
                                        data-min="{{ $server->min }}" data-max="{{ $server->max }}"
                                        data-quantity="{{ $server->action->quantity_status }}"
                                        data-reaction="{{ $server->action->reaction_status }}"
                                        data-comment="{{ $server->action->comments_status }}"
                                        data-getuid="{{ $server->action->get_uid }}"
                                        data-minute="{{ $server->action->minutes_status }}"
                                        data-reaction_type="{{ $server->action->reaction_data ?? 'all' }}"
                                        data-comment_type="{{ $server->action->comments_data }}"
                                        data-minute_type="{{ $server->action->minutes_data }}"
                                        data-posts="{{ $server->action->posts_status }}"
                                        data-posts_type="{{ $server->action->posts_data }}"
                                        data-time="{{ $server->action->time_status }}"
                                        data-time_type="{{ $server->action->time_data }}"
                                        data-price="{{ $server->levelPrice(Auth::check() ? Auth::user()->level : 'member') }}" onclick="checkPrice()">
                                    <label class="form-check-label" for="provider-server-{{ $server->package_id }}">
                                        <span class="badge bg-success">Sv{{ $server->package_id }}</span>
                                        <sp>{{ $server->name }}</sp an>
                                        <span
                                            class="badge bg-primary">{{ $server->levelPrice(Auth::check() ? Auth::user()->level : 'member') }}đ</span>
                                        @if ($server->status === 'inactive')
                                            <span class="badge bg-danger">Bảo trì</span>
                                        @else
                                            <span class="badge bg-success">Hoạt động</span>
                                        @endif

                                        {{-- @if ($server->limit_day > 0)
                                            <span class="badge bg-danger">Giới hạn: {{ number_format($server->limit_day) }}
                                                lần/ngày</span>
                                        @else
                                            <span class="badge bg-warning">Không giới hạn</span>
                                        @endif --}}

                                    </label>
                                </div>
                            @endforeach
                            <div id="informationServer"></div>
                        </div>

                        <div class="form-group mb-3 reactions" id="reactions_type" style="display: none;">
                            <label><strong>Cảm xúc:</strong></label>
                            <div class="mt-3">
                                <div class=" form-check form-check-inline">
                                    <label class="form-check-label " for="reaction0">
                                        <input class="form-check-input checkbox d-none" type="radio" data-prices="101"
                                            id="reaction0" name="reaction" value="like" checked="">
                                        <img src="{{ asset('assets/pack-lbd/reaction/like.png') }}" alt="image"
                                            class="d-block ml-2 rounded-circle" width="35">
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label " for="reaction1">
                                        <input class="form-check-input checkbox d-none" type="radio" data-prices="100"
                                            id="reaction1" name="reaction" value="love">
                                        <img src="{{ asset('assets/pack-lbd/reaction/love.png') }}" alt="image"
                                            class="d-block ml-2 rounded-circle" width="35">
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label " for="reaction2">
                                        <input class="form-check-input checkbox d-none" type="radio" data-prices="100"
                                            id="reaction2" name="reaction" value="care">
                                        <img src="{{ asset('assets/pack-lbd/reaction/care.png') }}" alt="image"
                                            class="d-block ml-2 rounded-circle" width="35">
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label " for="reaction3">
                                        <input class="form-check-input checkbox d-none" type="radio" data-prices="100"
                                            id="reaction3" name="reaction" value="haha">
                                        <img src="{{ asset('assets/pack-lbd/reaction/haha.png') }}" alt="image"
                                            class="d-block ml-2 rounded-circle" width="35">
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label " for="reaction4">
                                        <input class="form-check-input checkbox d-none" type="radio" data-prices="100"
                                            id="reaction4" name="reaction" value="wow">
                                        <img src="{{ asset('assets/pack-lbd/reaction/wow.png') }}" alt="image"
                                            class="d-block ml-2 rounded-circle" width="35">
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label " for="reaction6">
                                        <input class="form-check-input checkbox d-none" type="radio" data-prices="100"
                                            id="reaction6" name="reaction" value="sad">
                                        <img src="{{ asset('assets/pack-lbd/reaction/sad.png') }}" alt="image"
                                            class="d-block ml-2 rounded-circle" width="35">
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label " for="reaction7">
                                        <input class="form-check-input checkbox d-none" type="radio" data-prices="100"
                                            id="reaction7" name="reaction" value="angry">
                                        <img src="{{ asset('assets/pack-lbd/reaction/angry.png') }}" alt="image"
                                            class="d-block ml-2 rounded-circle" width="35">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3 quantity" id="quantity_type">
                            <label for="quantity" class="form-label"><strong>Số lượng: <span id="quantity_limit">(0 ~
                                        0)</span></strong></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value=""
                                onkeyup="checkPrice()" placeholder="Nhập số lượng">
                        </div>

                        <div class="form-group mb-3 comments" id="comments_type" style="display: none;">
                            <label for="comments" class="form-label">
                                <strong>Nội dung bình luận: </strong><span id="counter_comment"
                                    class="badge bg-success py-1">0</span>
                                <span id="quantity_limit">(0 ~ 0)</span>
                                <div class="alert alert-danger text-danger mt-1 mb-0" id="comment-alert">
                                    <strong>Lưu ý:</strong> Nếu bạn nhập nhiều bình luận, hệ thống sẽ chọn ngẫu nhiên 1 bình
                                    luận trong số đó để tăng.
                                </div>
                            </label>
                            <textarea class="form-control" name="comments" id="comments" rows="3" placeholder="Nhập nội dung bình luận"
                                onkeyup="checkPrice()"></textarea>
                        </div>

                        <div class="form-group mb-3 minute" id="minute_type" style="display: none;">
                            <label for="minute" class="form-label"><strong>Số phút</strong></label>
                            <select name="minutes" class="form-select" onchange="checkPrice()">
                                <option value="15">15 phút</option>
                                <option value="30">30 phút</option>
                                <option value="60">60 phút</option>
                                <option value="90">90 phút</option>
                                <option value="120">120 phút</option>
                                <option value="150">150 phút</option>
                                <option value="180">180 phút</option>
                                <option value="210">210 phút</option>
                                <option value="240">240 phút</option>
                                <option value="270">270 phút</option>
                                <option value="300">300 phút</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-12" id="time_type" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="duration" class="form-label">Thời gian:</label>
                                    <select name="duration" id="duration" class="form-select" onchange="checkPrice()">
                                        <option value="7">7 Ngày</option>
                                        <option value="15">15 Ngày</option>
                                        <option value="30">30 Ngày</option>
                                        <option value="60">60 Ngày</option>
                                        <option value="90">90 Ngày</option>
                                        <option value="120">120 Ngày</option>
                                        <option value="150">150 Ngày</option>
                                        <option value="180">180 Ngày</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3" id="posts_type" style="display: none;">
                                    <label for="posts" class="form-label">Số bài viết:</label>
                                    {{-- <input type="number" class="form-control" id="posts" name="posts"
                                        onkeyup="checkPrice()" value="5" placeholder="Nhập thời gian"> --}}
                                    <select name="posts" id="posts" class="form-select">
                                        {{-- <option value="unlimited">Không giới hạn</option> --}}
                                        <option value="5">5 Bài viết</option>
                                            <option value="10">10 Bài viết</option>
                                            <option value="20">20 Bài viết</option>
                                            <option value="30">30 Bài viết</option>
                                            <option value="40">40 Bài viết</option>
                                            <option value="50">50 Bài viết</option>
                                            <option value="60">60 Bài viết</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="note" class="form-label"><strong>Ghi chú:</strong></label>
                            <textarea class="form-control" name="note" id="note" rows="3" placeholder="Nhập ghi chú nếu cần"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <div class="alert bg-primary text-center text-white">
                                <h3 class="alert-heading">Tổng thanh toán: <span class="text-danger"
                                        id="total_pay">0</span>
                                    VNĐ</h3>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit"
                                class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Tạo đơn hàng</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-4">
            <div class="alert alert-danger bg-danger text-white mb-3">
                <h5 class="alert-heading">Lưu ý</h5>
                {!! $service->note !!}
            </div>
            <div class="alert alert-primary bg-primary text-white">
                <h5 class="alert-heading">Các trường hợp huỷ đơn hoặc không chạy</h5>
                {!! $service->details !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Lịch sử tạo đơn</h5>
                </div>
                <div class="card-body">
                    <form action="">

                        <div class="row">
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">Từ ngày</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ request()->start_date }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">Đến ngày</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ request()->end_date }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">Tất cả</option>
                                        <option value="Processing"
                                            {{ request()->status === 'Processing' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="Completed"
                                            {{ request()->status === 'Completed' ? 'selected' : '' }}>Đã hoàn thành
                                        </option>
                                        <option value="Cancelled"
                                            {{ request()->status === 'Cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        <option value="Refunded" {{ request()->status === 'Refunded' ? 'selected' : '' }}>
                                            Đã hoàn tiền</option>
                                        <option value="Failed" {{ request()->status === 'Failed' ? 'selected' : '' }}>Thất
                                            bại</option>
                                        <option value="Pending" {{ request()->status === 'Pending' ? 'selected' : '' }}>
                                            Chờ xử lý</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label for="order_code" class="form-label">Mã đơn hàng</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="order_code" name="order_code"
                                            value="{{ request()->order_code }}" placeholder="Nhập mã đơn hàng">
                                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped table-vcenter fw-bold">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Thao tác</th>
                                    <th>Thời gian</th>
                                    <th>Mã gói</th>
                                    <th>Mã đơn</th>
                                    <th>Link/UID</th>
                                    <th>Server</th>
                                    @if ($service->quantity_status === 'on')
                                        <th>Số lượng</th>
                                    @endif
                                    @if ($service->reaction_status === 'on')
                                        <th>Cảm xúc</th>
                                    @endif
                                    @if ($service->comments_status === 'on')
                                        <th>Bình luận</th>
                                    @endif
                                    @if ($service->minute_status === 'on')
                                        <th>Số phút</th>
                                    @endif
                                    @if ($service->time_status === 'on')
                                        <th>Số ngày</th>
                                        <th>Còn lại</th>
                                    @endif
                                    {{-- @if ($service->posts_status === 'on')
                                        <th>Bài viết/Ngày</th>
                                    @endif --}}
                                    @if ($service->time_status !== 'on')
                                        <th>Ban đầu</th>
                                        <th>Đã tăng</th>
                                    @endif
                                    <th>Trạng thái</th>
                                    <th>Giá tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody class="fw-bold">
                                @if ($orders->isEmpty())
                                    @include('admin.components.table-search-not-found', ['colspan' => 20])
                                @else
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td class="">
                                                @if (
                                                    $order->status !== 'Completed' &&
                                                        $order->status !== 'Refunded' &&
                                                        $order->status !== 'Cancelled' &&
                                                        $order->status !== 'Failed' &&
                                                        $order->status !== 'WaitingForRefund' &&
                                                        $order->status !== 'WaitingForRefund' &&
                                                        $order->status !== 'Failed' &&
                                                        $order->status !== 'Partially Refunded' &&
                                                        $order->status !== 'Partially Completed')
                                                    {{-- Hoàn tiền --}}
                                                    <div class="d-flex align-items-center gap-1">

                                                        @if ($order->server->action->refund_status === 'on')
                                                            <a href="javascript:;" class="btn btn-sm btn-warning"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Hoàn tiền"
                                                                onclick="refundOrder('{{ $order->order_code }}')">
                                                                <i class="fas fa-undo"></i>
                                                        @endif
                                                        {{-- Bảo hành --}}
                                                        @if ($order->server->action->warranty_status === 'on')
                                                            <a href="javascript:;" class="btn btn-sm btn-info"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Bảo hành"
                                                                onclick="warrantyOrder('{{ $order->order_code }}')">
                                                                <i class="fas fa-sync"></i>
                                                            </a>
                                                        @endif

                                                        {{-- Cập nhật --}}

                                                        <a href="javascript:;" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Cập nhật"
                                                            onclick="updateOrder('{{ $order->order_code }}')">
                                                            <i class="fas fa-cube"></i>
                                                        </a>

                                                    </div>
                                                @endif
                                                {{-- bảo hành --}}
                                                @if ($order->server->action->renews_status === 'on')
                                                    <a href="javascript:;" class="btn btn-sm btn-info"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Gia hạn"
                                                        onclick="renewsOrder('{{ $order->order_code }}')">
                                                        <i class="fas fa-sync"></i>
                                                @endif
                                            </td>
                                            <td>{{ $order->updated_at }}</td>
                                            <td>{{ $order->service->package }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>
                                                @if (strpos($order->object_id, 'https') !== false)
                                                    <a href="{{ $order->object_id }}"
                                                        target="_blank">{{ $order->object_id }}</a>
                                                @else
                                                    {{ $order->object_id }}
                                                @endif
                                            </td>
                                            <td>{{ $order->object_server }}</td>
                                            @if ($service->quantity_status === 'on')
                                                <td>{{ number_format($order->orderdata()['quantity']) }}</td>
                                            @endif
                                            @if ($service->reaction_status === 'on')
                                                <td>{{ $order->orderdata()['reaction'] }}</td>
                                            @endif
                                            @if ($service->comments_status === 'on')
                                                <td>
                                                    <textarea class="form-control" rows="1" readonly>{{ $order->orderdata()['comments'] }}</textarea>
                                                </td>
                                            @endif
                                            @if ($service->minute_status === 'on')
                                                <td>{{ $order->orderdata()['minute'] }} phút</td>
                                            @endif
                                            @if ($service->time_status === 'on')
                                                <td>{{ $order->orderdata()['duration'] ?? 0 }} ngày</td>
                                                <td>{{ remainingDays($order->time, $order->remaining, true) }}
                                                </td>
                                            @endif
                                            {{-- @if ($service->posts_status === 'on')
                                                <td>{{ $order->posts ?? 0 }}/{{ $order->orderdata()['posts'] }}</td>
                                            @endif --}}
                                            @if ($service->time_status !== 'on')
                                                <td>{{ number_format($order->start) }}</td>
                                                <td>{{ number_format($order->buff) }}</td>
                                            @endif
                                            <td>
                                                {!! statusOrder($order->status, true) !!}
                                            </td>
                                            <td>{{ $order->price }}</td>
                                            <td>{{ number_format($order->payment) }}</td>
                                            <td>
                                                <textarea class="form-control" rows="1" readonly>{{ $order->note }}</textarea>
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
@section('script')
    <script src="{{ asset('assets/pack-lbd/js/service.js?time=') }}{{ time() }}"></script>
@endsection
