@extends('admin.layouts.master')
@section('title', 'Chỉnh sửa máy chủ')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tạo mới dịch vụ</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.server.update', ['id' => $server->id]) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <select name="service" id="" class="form-select" disabled>
                                        @foreach (\App\Models\ServicePlatform::where('domain', env('APP_MAIN_SITE'))->orderBy('order', 'asc')->get() as $key => $platform)
                                            <option value="">-- {{ $platform->name }} --</option>
                                            @foreach ($platform->services as $service)
                                                <option value="{{ $service->id }}"
                                                    {{ $server->service_id == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    <label for="service">Dịch vụ</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Tiêu đề máy chủ" value="{{ $server->name }}">
                                    <label for="name">Tiêu đề máy chủ</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea name="details" id="details" class="form-control" placeholder="Mô tả máy chủ" rows="5"
                                        style="height: 200px;">{{ $server->details }}</textarea>
                                    <label for="details">Mô tả máy chủ</label>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <select name="package_id" id="package_id" class="form-select" disabled>
                                                <option value="">-- Chọn máy chủ --</option>
                                                @for ($i = 1; $i <= 50; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ $server->package_id == $i ? 'selected' : '' }}>
                                                        Máy chủ {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <label for="package_id">Máy chủ</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="price_member" name="price_member"
                                                value="{{ $server->price_member }}">
                                            <label for="price_member">Giá thành viên</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="price_collaborator"
                                                name="price_collaborator" value="{{ $server->price_collaborator }}">
                                            <label for="price_collaborator">Giá cộng tác viên</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="price_agency"
                                                name="price_agency" value="{{ $server->price_agency }}">
                                            <label for="price_agency">Giá đại lý</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="price_distributor"
                                                name="price_distributor" value="{{ $server->price_distributor }}">
                                            <label for="price_distributor">Giá nhà phân bón</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="status" id="status" class="form-select">
                                                <option value="active"
                                                    {{ $server->status == 'active' ? 'selected' : '' }}>
                                                    Hoạt động
                                                </option>
                                                <option value="inactive"
                                                    {{ $server->status == 'inactive' ? 'selected' : '' }}>
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
                                                    {{ $server->visibility == 'public' ? 'selected' : '' }}>
                                                    Công khai
                                                </option>
                                                <option value="private"
                                                    {{ $server->visibility == 'private' ? 'selected' : '' }}>
                                                    Riêng tư
                                                </option>
                                            </select>
                                            <label for="visibility">Hiển thị</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Sửa máy chủ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
