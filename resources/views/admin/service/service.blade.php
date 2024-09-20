@extends('admin.layouts.master')
@section('title', 'Danh sách dịch vụ ')

@section('content')
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
                            <h5 class="card-title">Danh sách dịch vụ</h5>
                        </div>
                        <div class="card-body">
                            <form action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Tìm kiếm dữ liệu"
                                                name="search" value="{{ request()->search }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select name="platform" id="platform" class="form-select">
                                                <option value="">-- Nền tảng --</option>
                                                @foreach (\App\Models\ServicePlatform::where('domain', env('APP_MAIN_SITE'))->orderBy('order', 'asc')->get() as $platform)
                                                    <option value="{{ $platform->code }}">{{ $platform->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group d-flex justify-content-between align-items-center gap-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="ti ti-search"></i> Tìm kiếm
                                            </button>
                                            <a href="{{ route('admin.service') }}" class="btn btn-secondary w-100">
                                                <i class="ti ti-rotate-clockwise"></i> Làm mới
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover table-vcenter text-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Thao tác</th>
                                            <th>Tên nền tảng</th>
                                            <th>Tên dịch vụ</th>
                                            <th>Đường đẫn</th>
                                            <th>Thao tác</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold">
                                        @if ($services->isEmpty())
                                            @include('admin.components.table-search-not-found', ['colspan' => 8])
                                        @else
                                            @foreach ($services as $service)
                                                <tr>
                                                    <td>{{ $service->id }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.service.edit', ['id' => $service->id]) }}"
                                                            class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                        <a href="{{ route('admin.service.delete', ['id' => $service->id]) }}"
                                                            class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $service->platform->name }}</td>
                                                    <td>{{ $service->name }}</td>
                                                    <td>{{ $service->slug }}</td>
                                                    <td>
                                                        <ul class="text-sm mb-0">
                                                            <li>Cột cảm xúc: <span class="badge bg-{{ $service->reaction_status == 'on' ? 'success' : 'danger' }}">{{ $service->reaction_status == 'on' ? 'Bật' : 'Tắt' }}</span></li>
                                                            <li>Cột số lượng: <span class="badge bg-{{ $service->quantity_status == 'on' ? 'success' : 'danger' }}">{{ $service->quantity_status == 'on' ? 'Bật' : 'Tắt' }}</span></li>
                                                            <li>Cột bình luận: <span class="badge bg-{{ $service->comments_status == 'on' ? 'success' : 'danger' }}">{{ $service->comments_status == 'on' ? 'Bật' : 'Tắt' }}</span></li>
                                                            <li>Cột số phút: <span class="badge bg-{{ $service->minutes_status == 'on' ? 'success' : 'danger' }}">{{ $service->minutes_status == 'on' ? 'Bật' : 'Tắt' }}</span></li>
                                                            <li>Cột thời gian: <span class="badge bg-{{ $service->time_status == 'on' ? 'success' : 'danger' }}">{{ $service->time_status == 'on' ? 'Bật' : 'Tắt' }}</span></li>
                                                            <li>Cột bài viết: <span class="badge bg-{{ $service->posts_status == 'on' ? 'success' : 'danger' }}">{{ $service->posts_status == 'on' ? 'Bật' : 'Tắt' }}</span></li>
                                                        </ul>
                                                    </td>
                                                    <td>
                                                        @if ($service->status == 'active')
                                                            <span class="badge bg-success">Hoạt động</span>
                                                        @else
                                                            <span class="badge bg-danger">Không hoạt động</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $service->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center align-items-center">
                                    {{ $services->appends(request()->all())->links('pagination::bootstrap-4') }}
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
                            <form action="{{ route('admin.service.create') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-3">
                                    <select name="platform_id" id="platform" class="form-select">
                                        <option value="">-- Chọn nền tảng --</option>
                                        @foreach (\App\Models\ServicePlatform::where('domain', env('APP_MAIN_SITE'))->orderBy('order', 'asc')->get() as $platform)
                                            <option value="{{ $platform->id }}" @if (old('platform_id') == $platform->id) selected @endif>{{ $platform->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="platform">Chọn nền tảng</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Tên dịch vụ"
                                        value="{{ old('name') }}">
                                    <label for="name">Tên dịch vụ</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="slug" name="slug" placeholder="Đường dẫn"
                                        value="{{ old('slug') }}">
                                    <label for="slug">Đường dẫn</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea name="note" id="note" class="form-control" cols="30" rows="8" style="height: 120px;">{{ old('note') }}</textarea>
                                    <label for="note">Ghi chú</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea name="details" id="details" class="form-control" cols="30" rows="8" style="height: 120px;">{{ old('details') }}</textarea>
                                    <label for="note">Lưu ý</label>
                                </div>
                                <h5 class="mb-3">META SEO: </h5>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Tên dịch vụ"
                                        value="{{ old('title') }}">
                                    <label for="title">Title</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Tên dịch vụ"
                                        value="{{ old('description') }}">
                                    <label for="description">Description</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="image" name="image" placeholder="Tên dịch vụ"
                                        value="{{ old('image') }}">
                                    <label for="image">Image</label>
                                </div>
                                <div class="row">
                                    <h5 class="mb-3">Hiển thị dữ liệu trong bảng </h5>
                                    <div class="col-sm-12 col-md-6 col-lg-2">
                                        <div class="form-floating mb-3">
                                            <select name="reaction_status" id="" class="form-select">
                                                <option value="off">Tắt</option>
                                                <option value="on">Bật</option>
                                            </select>
                                            <label for="" class="form-label">Cột cảm xúc</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-2">
                                        <div class="form-floating mb-3">
                                            <select name="quantity_status" id="" class="form-select">
                                                <option value="off">Tắt</option>
                                                <option value="on">Bật</option>
                                            </select>
                                            <label for="" class="form-label">Cột số lượng</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-2">
                                        <div class="form-floating mb-3">
                                            <select name="comments_status" id="" class="form-select">
                                                <option value="off">Tắt</option>
                                                <option value="on">Bật</option>
                                            </select>
                                            <label for="" class="form-label">Cột bình luận</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-2">
                                        <div class="form-floating mb-3">
                                            <select name="minutes_status" id="" class="form-select">
                                                <option value="off">Tắt</option>
                                                <option value="on">Bật</option>
                                            </select>
                                            <label for="" class="form-label">Cột số phút</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-2">
                                        <div class="form-floating mb-3">
                                            <select name="time_status" id="" class="form-select">
                                                <option value="off">Tắt</option>
                                                <option value="on">Bật</option>
                                            </select>
                                            <label for="" class="form-label">Cột thời gian</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-2">
                                        <div class="form-floating mb-3">
                                            <select name="posts_status" id="" class="form-select">
                                                <option value="off">Tắt</option>
                                                <option value="on">Bật</option>
                                            </select>
                                            <label for="" class="form-label">Cột bài viết</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-floating mb-3">
                                    <select name="status" id="status" class="form-select">
                                        <option value="active" @if (old('status') == 'active') selected @endif>Hoạt động</option>
                                        <option value="inactive" @if (old('status') == 'inactive') selected @endif>Không hoạt động</option>
                                    </select>
                                    <label for="status">Trạng thái</label>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary col-12">
                                        <i class="fas fa-save"></i> Thêm mới
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
