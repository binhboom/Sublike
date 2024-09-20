@extends('admin.layouts.master')
@section('title', 'Danh sách dịch vụ & nền tảng')

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
                            <h5 class="card-title">Danh sách nền tảng</h5>
                        </div>
                        <div class="card-body">
                            <form action="">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Tìm kiếm dữ liệu"
                                                name="search" value="{{ request()->search }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group d-flex justify-content-between align-items-center gap-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="ti ti-search"></i> Tìm kiếm
                                            </button>
                                            <a href="{{ route('admin.service.platform') }}" class="btn btn-secondary w-100">
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
                                            <th>Thứ tự</th>
                                            <th>Tên nền tảng</th>
                                            <th>Đường dẫn</th>
                                            <th>Biểu tượng</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold">
                                        @if ($platforms->isEmpty())
                                            @include('admin.components.table-search-not-found', [
                                                'colspan' => 8,
                                            ])
                                        @else
                                            @foreach ($platforms as $platform)
                                                <tr>
                                                    <td>{{ $platform->id }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.service.platform.edit', ['id' => $platform->id]) }}"
                                                            class="btn btn-sm btn-success"
                                                            data-bs-toggle="tooltip" title="Xem chi tiết">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.service.platform.delete', ['id' => $platform->id]) }}"
                                                            class="btn btn-sm btn-danger"
                                                            data-bs-toggle="tooltip" title="Xóa">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge fs-6 fw-bold bg-success">{{ $platform->order }}</span>
                                                    </td>
                                                    <td>{{ $platform->name }}</td>
                                                    <td>{{ $platform->slug }}</td>
                                                    <td>
                                                        <img src="{{ $platform->image }}" alt="{{ $platform->name }}"
                                                            class="img-fluid" style="max-width: 50px">
                                                    </td>
                                                    <td>
                                                        @if ($platform->status == 'active')
                                                            <span class="badge bg-success">Hoạt động</span>
                                                        @else
                                                            <span class="badge bg-danger">Không hoạt động</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $platform->created_at }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center align-items-center">
                                    {{ $platforms->appends(request()->all())->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="new" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Tạo mới nền tảng</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.service.platform.create') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Tên nền tảng của dịch vụ" value="{{ old('name') }}">
                                    <label for="name">Tên nền tảng</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="slug"
                                        placeholder="Đường dẫn của nền tảng" value="{{ old('slug') }}">
                                    <label for="slug">Đường dẫn</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="image" placeholder="Nhập hình ảnh"
                                        value="{{ old('image') }}">
                                    <label for="image">Hình ảnh</label>
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
