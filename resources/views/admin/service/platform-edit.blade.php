@extends('admin.layouts.master')
@section('title', 'Chỉnh sửa nền tảng')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Chỉnh sửa nền tảng</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.service.platform.update', ['id' => $platform->id]) }}" method="POST">
                        @csrf
                        {{-- thứ tự hiển thị --}}
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" name="order" placeholder="Thứ tự hiển thị"
                                value="{{ $platform->order }}">
                            <label for="order">Thứ tự hiển thị</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Tên nền tảng của dịch vụ"
                                value="{{ $platform->name }}">
                            <label for="name">Tên nền tảng</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="slug" placeholder="Đường dẫn của nền tảng"
                                value="{{ $platform->slug }}">
                            <label for="slug">Đường dẫn</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="image" placeholder="Nhập hình ảnh"
                                value="{{ $platform->image }}">
                            <label for="image">Hình ảnh</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select name="status" class="form-select">
                                <option value="active" @if ($platform->status == 'active') selected @endif>Hoạt động
                                </option>
                                <option value="inactive" @if ($platform->status == 'inactive') selected @endif>Không hoạt
                                    động</option>
                            </select>
                            <label for="status">Trạng thái</label>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary col-12">
                                <i class="fas fa-save"></i> Thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
