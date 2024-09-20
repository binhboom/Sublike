@extends('guard.layouts.master')
@section('title', 'Hỗ trợ Ticket')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/pack-lbd/images/ticket-1.png') }}" alt="ticket" class="img-fluid"
                            style="width: 40px; height: 40px; object-fit: cover; object-position: center; border-radius: 50%;">
                        <h5 class="card-title">Hỗ trợ Ticket</h5>
                    </div>
                </div>
                <div class="card-body pc-component">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#pills-home"
                                role="tab" aria-controls="pills-home" aria-selected="true">Danh sách
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" href="#pills-profile"
                                role="tab" aria-controls="pills-profile" aria-selected="false" tabindex="-1">Tạo mới
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade active show" id="pills-home" role="tabpanel"
                            aria-labelledby="pills-home-tab">
                            <form action="" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="search" id="search"
                                                placeholder="Tìm kiếm">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select class="form-select" name="replied_status" id="replied_status">
                                                <option value="">Trạng thái</option>
                                                <option value="">Tất cả</option>
                                                <option value="0">Chưa xử lý</option>
                                                <option value="1">Đang xử lý</option>
                                                <option value="2">Đã xử lý</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap dataTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Người tạo</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Ngày cập nhật</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tickets as $ticket)
                                            <tr>
                                                <td>{{ $ticket->id }}</td>
                                                <td>{{ $ticket->user->name }}</td>
                                                <td>
                                                    @if ($ticket->replied_status == 1)
                                                        <span class="badge bg-warning">Chưa phản hồi</span>
                                                    @elseif ($ticket->replied_status == 2)
                                                        <span class="badge bg-info">Đã phản hồi</span>
                                                    @elseif ($ticket->replied_status == 3)
                                                        <span class="badge bg-success">Đã xử lý</span>
                                                    @endif
                                                </td>
                                                <td>{{ $ticket->created_at }}</td>
                                                <td>{{ $ticket->updated_at }}</td>
                                                <td>
                                                    <a href="{{ route('ticket.detail', ['id' => $ticket->id]) }}"
                                                        class="btn btn-primary btn-sm">Xem</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <form action="{{ route('ticket.create') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="type" id="type">
                                        <option value="1">Hỗ trợ</option>
                                        <option value="2">Yêu cầu dịch vụ</option>
                                        <option value="3">Báo lỗi</option>
                                        <option value="4">Góp ý</option>
                                        <option value="5">Khác</option>
                                    </select>
                                    <label for="type">Loại ticket</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="title" id="title" placeholder=" ">
                                    <label for="title">Tiêu đề</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" name="description" id="description" placeholder=" "
                                        style="height: 100px;"></textarea>
                                    <label for="description">Nội dung</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <select name="service_id" id="" class="form-select">
                                        @foreach ($service_servers as $server)
                                            <option value="{{ $server->id }}">| {{ $server->service->platform->name }} | Dịch vụ: {{ $server->service->name }} | Máy chủ: {{ $server->package_id }} |</option>
                                        @endforeach
                                    </select>
                                    <label for="" class="form-label">Dịch vụ</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="priority" id="priority">
                                        <option value="low">Thấp</option>
                                        <option value="medium">Trung bình</option>
                                        <option value="high">Cao</option>
                                    </select>
                                    <label for="priority">Mức ưu tiên</label>
                                </div>
                                <button type="submit" class="btn btn-primary">Tạo mới</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
