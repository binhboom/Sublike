@extends('admin.layouts.master')
@section('title', 'Danh sách máy chủ')

@section('content')

    @foreach ($servers as $upd)
        @if ($upd->price_update !== $upd->price)
            <div class="alert alert-danger bg-danger text-white mb-5">
                <p class="text-white">Nguồn:
                    <span class="text-white fw-bold">{{ $upd->providerName }}</span>
                    Máy chủ:
                    <span class="text-white fw-bold">{{ $upd->package_id }}</span>
                    dịch vụ: <span class="text-white fw-bold">{{ $upd->name }}</span> vừa thay đổi giá
                </p>
                <ul class="list-none mb-2">
                    <li>
                        <strong>Giá gốc hiện tại:</strong> {{ $upd->price_update }}đ
                    </li>
                </ul>
                <div class="p mb-0">Vui lòng <a
                        href="{{ route('admin.server.edit', ['id' => $upd->id, 'price_update' => true]) }}" class="fw-bold"
                        data-bs-toggle="tooltip" title="Cập nhật giá">
                        Cập nhật giá tại đây
                    </a></div>
            </div>
        @endif
    @endforeach

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body py-2">
                    <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="list-tab" data-bs-toggle="tab" href="#list" role="tab"
                                aria-selected="true">
                                <i class="ti ti-layers-linked me-2"></i>Danh Sách
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="new-tab" data-bs-toggle="tab" href="#new" role="tab"
                                aria-selected="false" tabindex="-1">
                                <i class="ti ti-receipt me-2"></i>Tạo Mới
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="list" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Danh sách máy chủ</h5>
                        </div>
                        <div class="card-body">
                            <form action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Tìm kiếm dữ liệu"
                                                name="search" value="{{ request()->search }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <select name="service" id="service" class="form-select">
                                                <option value="">-- Dịch vụ --</option>
                                                @foreach (\App\Models\ServicePlatform::where('domain', env('APP_MAIN_SITE'))->orderBy('order', 'asc')->get() as $key => $platform)
                                                    <option value="">-- {{ $platform->name }} --</option>
                                                    @foreach ($platform->services as $service)
                                                        <option value="{{ $service->id }}"
                                                            {{ request()->service == $service->id ? 'selected' : '' }}>
                                                            --- {{ $service->name }} ---
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <select name="visibility" id="visibility" class="form-select">
                                                <option value="">-- Hiển thị --</option>
                                                <option value="public"
                                                    {{ request()->visibility == 'public' ? 'selected' : '' }}>
                                                    Công khai
                                                </option>
                                                <option value="private"
                                                    {{ request()->visibility == 'private' ? 'selected' : '' }}>
                                                    Riêng tư
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <select name="status" id="status" class="form-select">
                                                <option value="">-- Trạng thái --</option>
                                                <option value="active"
                                                    {{ request()->status == 'active' ? 'selected' : '' }}>
                                                    Hoạt động
                                                </option>
                                                <option value="inactive"
                                                    {{ request()->status == 'inactive' ? 'selected' : '' }}>
                                                    Không hoạt động
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group d-flex justify-content-between align-items-center gap-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="ti ti-search"></i> Tìm kiếm
                                            </button>
                                            <a href="{{ route('admin.server') }}" class="btn btn-secondary w-100">
                                                <i class="ti ti-rotate-clockwise"></i> Làm mới
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-vcenter mb-0">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Thao tác</th>
                                            <th>Thông tin</th>
                                            <th>Bảng giá</th>
                                            <th>Thao tác Máy chủ</th>
                                            <th>Nguồn dịch vụ</th>
                                            <th>Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold">
                                        @if ($servers->isEmpty())
                                            @include('admin.components.table-search-not-found', [
                                                'colspan' => 8,
                                            ])
                                        @else
                                            @foreach ($servers as $server)
                                                <tr>
                                                    <td>{{ $server->id }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.server.edit', ['id' => $server->id]) }}"
                                                            class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                                                            title="Xem chi tiết">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.server.delete', ['id' => $server->id]) }}"
                                                            class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                                            title="Xóa">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            <li>
                                                                <strong>Dịch vụ:</strong> {{ $server->name }}
                                                            </li>
                                                            <li>
                                                                <strong>Máy chủ:</strong> {{ $server->package_id }}
                                                            </li>
                                                            <li>
                                                                <strong>Trạng thái:</strong> {!! $server->getStatusLabel($server->status, true) !!}
                                                            </li>
                                                            <li>
                                                                <strong>Min - Max:</strong>
                                                                {{ number_format($server->min) }} -
                                                                {{ number_format($server->max) }}
                                                            </li>
                                                            <li>
                                                                <strong>Hiển thị:</strong> {!! $server->getVisibilityLabel($server->visibility, true) !!}
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            <li>
                                                                <strong>Giá gốc:</strong>
                                                                {{ number_format($server->price ?? 0) }}đ
                                                            </li>
                                                            <li>
                                                                <strong class="text-success">Giá thành viên:</strong>
                                                                {{ $server->price_member }}đ
                                                            </li>
                                                            <li>
                                                                <strong class="text-primary">Giá cộng tác viên:</strong>
                                                                {{ $server->price_collaborator }}đ
                                                            </li>
                                                            <li>
                                                                <strong class="text-info">Giá đại lý:</strong>
                                                                {{ $server->price_agency }}đ
                                                            </li>
                                                            <li>
                                                                <strong class="text-danger">Giá nhà phân phối:</strong>
                                                                {{ $server->price_distributor }}đ
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            <li>
                                                                <strong>Số lượng:</strong> {!! $server->getActionStatusLabel($server->actions->first()->quantity_status, true) !!}
                                                            </li>
                                                            <li>
                                                                <strong>Cảm xúc:</strong> {!! $server->getActionStatusLabel($server->actions->first()->reaction_status, true) !!}
                                                            </li>
                                                            <li>
                                                                <strong>Bình luận:</strong> {!! $server->getActionStatusLabel($server->actions->first()->comments_status, true) !!}
                                                            </li>
                                                            <li>
                                                                <strong>Số phút:</strong> {!! $server->getActionStatusLabel($server->actions->first()->minutes_status, true) !!}
                                                            </li>
                                                            <li>
                                                                <strong>Thời gian:</strong> {!! $server->getActionStatusLabel($server->actions->first()->time_status, true) !!}
                                                            </li>
                                                            <li>
                                                                <strong>Bài viết:</strong> {!! $server->getActionStatusLabel($server->actions->first()->posts_status, true) !!}
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            <li>
                                                                <strong>Nguồn:</strong> {{ $server->providerName }}
                                                            </li>
                                                            <li>
                                                                <strong>Đường dẫn:</strong> {{ $server->providerLink }}
                                                            </li>
                                                            <li>
                                                                <strong>Máy chủ:</strong> {{ $server->providerServer }}
                                                            </li>
                                                            <li>
                                                                <strong>Mã dịch vụ:</strong> {{ $server->providerKey }}
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        {{ $server->created_at->diffForHumans() }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center align-items-center">
                                    {{ $servers->appends(request()->all())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="new" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Tạo mới dịch vụ</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.server.create') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-floating mb-3">
                                            <select name="service" id="" class="form-select">
                                                @foreach (\App\Models\ServicePlatform::where('domain', env('APP_MAIN_SITE'))->orderBy('order', 'asc')->get() as $key => $platform)
                                                    <option value="">-- {{ $platform->name }} --</option>
                                                    @foreach ($platform->services as $service)
                                                        <option value="{{ $service->id }}"
                                                            {{ old('service') == $service->id ? 'selected' : '' }}>
                                                            --- {{ $service->name }} ---
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                            <label for="service">Dịch vụ</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Tiêu đề máy chủ" value="{{ old('name') }}">
                                            <label for="name">Tiêu đề máy chủ</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <textarea name="details" id="details" class="form-control" placeholder="Mô tả máy chủ" rows="5"
                                                style="height: 200px;">{{ old('details') }}</textarea>
                                            <label for="details">Mô tả máy chủ</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <select name="package_id" id="package_id" class="form-select">
                                                        <option value="">-- Chọn máy chủ --</option>
                                                        @for ($i = 1; $i <= 20; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ old('package_id') == $i ? 'selected' : '' }}>
                                                                Máy chủ {{ $i }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                    <label for="package_id">Máy chủ</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <select name="get_uid" id="get_uid" class="form-select">
                                                        <option value="">-- Chọn trạng thái --</option>
                                                        <option value="on"
                                                            {{ old('get_uid') == 'on' ? 'selected' : '' }}>Bật</option>
                                                        <option value="off"
                                                            {{ old('get_uid') == 'off' ? 'selected' : '' }}>Tắt</option>
                                                    </select>
                                                    <label for="get_uid">Tự động lấy UID</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" name="limit_day"
                                                        id="limit_day" placeholder="Giới hạn ngày" min="0"
                                                        value="{{ old('limit_day') }}">
                                                    <label for="limit_day">Giới hạn ngày (Nhập 0
                                                        thì sẽ không giới hạn)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" class="form-control" id="min"
                                                        name="min" value="{{ old('min') }}">
                                                    <label for="min">Mua tôi thiểu</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" class="form-control" id="max"
                                                        name="max" value="{{ old('max') }}">
                                                    <label for="max">Mua tôi đa</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="price_member"
                                                        name="price_member" value="{{ old('price_member') }}">
                                                    <label for="price_member">Giá thành viên</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="price_collaborator"
                                                        name="price_collaborator"
                                                        value="{{ old('price_collaborator') }}">
                                                    <label for="price_collaborator">Giá cộng tác viên</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="price_agency"
                                                        name="price_agency" value="{{ old('price_agency') }}">
                                                    <label for="price_agency">Giá đại lý</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="price_distributor"
                                                        name="price_distributor" value="{{ old('price_distributor') }}">
                                                    <label for="price_distributor">Giá nhà phân bón</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="providerName" id="providerName" class="form-select">
                                                        <option value="">-- Chọn nguồn dịch vụ --</option>
                                                        

                                                        @foreach (\App\Models\SmmPanelPartner::where('domain', env('APP_MAIN_SITE'))->get() as $smm)
                                                            <option value="{{ $smm->name }}"
                                                                {{ old('providerName') == $smm->name ? 'selected' : '' }}>
                                                                {{ $smm->name }}
                                                            </option>
                                                        @endforeach

                                                        <option value="codedynamic"
                                                            {{ old('providerName') == 'codedynamic' ? 'selected' : '' }}>
                                                            mmo7me.com</option>
                                                    </select>
                                                    <label for="providerName">Nguồn dịch vụ</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="providerLink"
                                                        name="providerLink" value="{{ old('providerLink') }}">
                                                    <label for="providerLink">Đường dẫn dịch vụ</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="providerServer"
                                                        name="providerServer" value="{{ old('providerServer') }}">
                                                    <label for="providerServer">Máy chủ gốc</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="providerKey"
                                                        name="providerKey" value="{{ old('providerKey') }}">
                                                    <label for="providerKey">Mã dịch vụ</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <select name="refund_status" id="refund_status" class="form-select">
                                                        <option value="on"
                                                            {{ old('refund_status') == 'on' ? 'selected' : '' }}>
                                                            Có hoàn tiền
                                                        </option>
                                                        <option value="off"
                                                            {{ old('refund_status') == 'off' ? 'selected' : '' }}>
                                                            Không hoàn tiền
                                                        </option>
                                                    </select>
                                                    <label for="refund_status">Hoàn tiền</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <select name="warranty_status" id="warranty_status"
                                                        class="form-select">
                                                        <option value="on"
                                                            {{ old('warranty_status') == 'on' ? 'selected' : '' }}>
                                                            Có Bảo hành
                                                        </option>
                                                        <option value="off"
                                                            {{ old('warranty_status') == 'off' ? 'selected' : '' }}>
                                                            Không Bảo hành
                                                        </option>
                                                    </select>
                                                    <label for="warranty_status">Bảo hành</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <select name="renews_status" id="renews_status" class="form-select">
                                                        <option value="on"
                                                            {{ old('renews_status') == 'on' ? 'selected' : '' }}>
                                                            Có Gia hạn
                                                        </option>
                                                        <option value="off"
                                                            {{ old('renews_status') == 'off' ? 'selected' : '' }}>
                                                            Không Gia hạn
                                                        </option>
                                                    </select>
                                                    <label for="renews_status">Gia hạn</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="status" id="status" class="form-select">
                                                        <option value="active"
                                                            {{ old('status') == 'active' ? 'selected' : '' }}>
                                                            Hoạt động
                                                        </option>
                                                        <option value="inactive"
                                                            {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                            Không hoạt động
                                                        </option>
                                                    </select>
                                                    <label for="status">Trạng thái</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="visibility" id="visibility" class="form-select">
                                                        <option value="public"
                                                            {{ old('visibility') == 'public' ? 'selected' : '' }}>
                                                            Công khai
                                                        </option>
                                                        <option value="private"
                                                            {{ old('visibility') == 'private' ? 'selected' : '' }}>
                                                            Riêng tư
                                                        </option>
                                                    </select>
                                                    <label for="visibility">Hiển thị</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="reaction_status" id="reaction_status"
                                                        class="form-select">
                                                        <option value="off"
                                                            {{ old('reaction_status') == 'off' ? 'selected' : '' }}>
                                                            Tắt
                                                        </option>
                                                        <option value="on"
                                                            {{ old('reaction_status') == 'on' ? 'selected' : '' }}>
                                                            Bật
                                                        </option>
                                                    </select>
                                                    <label for="reaction_status">Cảm xúc</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="quantity_status" id="quantity_status"
                                                        class="form-select">
                                                        <option value="off"
                                                            {{ old('quantity_status') == 'off' ? 'selected' : '' }}>
                                                            Tắt
                                                        </option>
                                                        <option value="on"
                                                            {{ old('quantity_status') == 'on' ? 'selected' : '' }}>
                                                            Bật
                                                        </option>
                                                    </select>
                                                    <label for="quantity_status">Số lượng</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="comments_status" id="comments_status"
                                                        class="form-select">
                                                        <option value="off"
                                                            {{ old('comments_status') == 'off' ? 'selected' : '' }}>
                                                            Tắt
                                                        </option>
                                                        <option value="on"
                                                            {{ old('comments_status') == 'on' ? 'selected' : '' }}>
                                                            Bật
                                                        </option>
                                                    </select>
                                                    <label for="comments_status">Bình luận</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="minutes_status" id="minutes_status"
                                                        class="form-select">
                                                        <option value="off"
                                                            {{ old('minutes_status') == 'off' ? 'selected' : '' }}>
                                                            Tắt
                                                        </option>
                                                        <option value="on"
                                                            {{ old('minutes_status') == 'on' ? 'selected' : '' }}>
                                                            Bật
                                                        </option>
                                                    </select>
                                                    <label for="minutes_status">Số phút</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="time_status" id="time_status" class="form-select">
                                                        <option value="off"
                                                            {{ old('time_status') == 'off' ? 'selected' : '' }}>
                                                            Tắt
                                                        </option>
                                                        <option value="on"
                                                            {{ old('time_status') == 'on' ? 'selected' : '' }}>
                                                            Bật
                                                        </option>
                                                    </select>
                                                    <label for="time_status">Thời gian</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select name="posts_status" id="posts_status" class="form-select">
                                                        <option value="off"
                                                            {{ old('posts_status') == 'off' ? 'selected' : '' }}>
                                                            Tắt
                                                        </option>
                                                        <option value="on"
                                                            {{ old('posts_status') == 'on' ? 'selected' : '' }}>
                                                            Bật
                                                        </option>
                                                    </select>
                                                    <label for="posts_status">Bài viết</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> <strong>Lưu ý:</strong> Bạn
                                                    cần nhập dữ liệu cảm xúc theo ví dụ như sau:
                                                    <code>LIKE,HAHA,COMMENT</code> hoặc tất cả là <code>ALL</code>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="reaction_data"
                                                        name="reaction_data" value="{{ old('reaction_data') }}">
                                                    <label for="reaction_data">Dữ liệu cảm xúc</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> <strong>Lưu ý:</strong>
                                                    Thời gian tính theo giây và phải là số nguyên dương
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="minutes_data"
                                                        name="minutes_data" value="{{ old('minutes_data') }}">
                                                    <label for="minutes_data">Dữ liệu thời gian</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-save"></i> Thêm máy chủ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
