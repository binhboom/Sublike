@extends('admin.layouts.master')
@section('title', 'Chỉnh sửa cấu hình SMM')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sửa API Smm</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.service.smm.update', $smm->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="form-label">Tên API</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $smm->name }}"
                                placeholder="Tên API vd(smmcoder)">
                        </div>
                        <div class="form-group">
                            <label for="api_key" class="form-label">API Key</label>
                            <input type="text" class="form-control" id="api_key" name="api_key" placeholder="API Key" value="{{ $smm->api_token }}">
                        </div>
                        <div class="form-group">
                            <label for="url_api" class="form-label">Url API</label>
                            <input type="text" class="form-control" id="url_api" name="url_api" placeholder="Url API" value="{{ $smm->url_api }}">
                        </div>
                        <div class="form-group">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="on" {{ $smm->status == 'on' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="off" {{ $smm->status == 'off' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="update_price" class="form-label">Tự động cập nhật giá</label>
                            <select class="form-control" id="update_price" name="update_price">
                                <option value="on" {{ $smm->update_price == 'on' ? 'selected' : '' }}>Bật</option>
                                <option value="off" {{ $smm->update_price == 'off' ? 'selected' : '' }}>Tắt</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price_update" class="form-label">Tăng giá tự động theo %</label>
                            <input type="number" class="form-control" id="price_update" name="price_update" value="{{ $smm->price_update }}"
                                placeholder="Tăng giá tự động theo %">
                        </div>
                        <button type="submit" class="btn btn-primary">Sửa API SMM</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
