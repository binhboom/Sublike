@extends('admin.layouts.master')
@section('title', 'Cấu hình SMM')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thêm API Smm mới</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.service.smm.create') }}" method="POST">  
    @csrf  
    <div class="form-group">  
        <label for="name" class="form-label">Tên API</label>  
        <input type="text" class="form-control" id="name" name="name"  
            placeholder="Tên API vd(smmcoder)">  
    </div>  
    <div class="form-group">  
        <label for="api_key" class="form-label">API Key</label>  
        <input type="text" class="form-control" id="api_key" name="api_key" placeholder="API Key">  
    </div>  
    <div class="form-group">  
        <label for="url_api" class="form-label">Url API</label>  
        <input type="url" class="form-control" id="url_api" name="url_api"   
            placeholder="https://hacklikesub.net/api/v2"  
            pattern="https://hacklikesub\.net/api/v2"   
            required>  
    </div>  
    <div class="form-group">  
        <label for="status" class="form-label">Trạng thái</label>  
        <select class="form-control" id="status" name="status">  
            <option value="on">Hoạt động</option>  
            <option value="off">Không hoạt động</option>  
        </select>  
    </div>  
    <div class="form-group">  
        <label for="update_price" class="form-label">Tự động cập nhật giá</label>  
        <select class="form-control" id="update_price" name="update_price">  
            <option value="on">Bật</option>  
            <option value="off">Tắt</option>  
        </select>  
    </div>  
    <div class="form-group">  
        <label for="price_update" class="form-label">Tăng giá tự động theo %</label>  
        <input type="number" class="form-control" id="price_update" name="price_update"  
            placeholder="Tăng giá tự động theo %">  
    </div>  
    <button type="submit" class="btn btn-primary">Thêm API</button>  
</form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách API SMM</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter text-center mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Thao tác</th>
                                    <th>Tên SMM</th>
                                    <th>Đường dẫn (API)</th>
                                    <th>Link Cron</th>
                                    <th>Link Update Giá</th>
                                    <th>Trạng thái</th>
                                    <th>Cập nhật giá</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody class="fw-bold">
                                @if ($smmlist->isEmpty())
                                    @include('admin.components.table-search-not-found', ['colspan' => 7])
                                @endif
                                @foreach ($smmlist as $key => $smm)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <a href="{{ route('admin.service.smm.edit', $smm->id) }}"
                                                class="btn btn-sm btn-primary text-sm">Sửa</a>
                                            <a href="{{ route('admin.service.smm.delete', $smm->id) }}"
                                                class="btn btn-sm btn-danger text-sm">Xóa</a>
                                        </td>
                                        <td>{{ $smm->name }}</td>
                                        <td>{{ $smm->url_api }}</td>
                                        <td>
                                            <a href="{{ route('cron-job.status.service.smm', ['name' => $smm->name]) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-primary text-sm">{{ route('cron-job.status.service.smm', ['name' => $smm->name]) }}</a>
                                        </td>
                                        <td>
                                            <a href="{{ route('api.price-service', ['service' => $smm->name]) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-primary text-sm">{{ route('api.price-service', ['service' => $smm->name]) }}</a>
                                        </td>
                                        <td>
                                            @if ($smm->status == 'on')
                                                <span class="badge bg-success">Hoạt động</span>
                                            @else
                                                <span class="badge bg-danger">Không hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($smm->update_price == 'on')
                                                <span class="badge bg-success">Bật</span>
                                            @else
                                                <span class="badge bg-danger">Tắt</span>
                                            @endif
                                        </td>
                                        <td>{{ $smm->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
