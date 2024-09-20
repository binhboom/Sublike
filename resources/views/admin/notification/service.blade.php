@extends('admin.layouts.master')
@section('title', 'Thông báo hoạt động')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-titlle">Thêm thông báo mới</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notify.service.create') }}" method="POST">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" name="title" id="title" placeholder="Tiêu đề">
                            <label for="title">Tiêu đề</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select name="color" id="color" class="form-select">
                                <option value="primary">Tím</option>
                                <option value="secondary">Đen</option>
                                <option value="success">Xanh Lục</option>
                                <option value="danger">Đỏ</option>
                                <option value="warning">Vàng</option>
                                <option value="info">Xanh Dương</option>
                            </select>
                            <label for="color">Màu sắc</label>
                        </div>
                        <div class="form-group mb-3">
                            <label for="content">Nội dung</label>
                            <textarea class="form-control" id="content" name="content" placeholder="Nội dung" style="height: 100px"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100">Thêm thông báo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách thông báo</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Thao tác</th>
                                <th>Người đăng</th>
                                <th>Tiêu đề</th>
                                <th>Nội dung</th>
                                <th>Màu sắc</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($noticeServices as $notification)
                                <tr>
                                    <td>{{ $notification->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.notify.service.delete', ['id' => $notification->id]) }}"
                                            class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                    <td>{{ $notification->user->username }}</td>
                                    <td>{{ $notification->title }}</td>
                                    <td>{!! $notification->content !!}</td>
                                    <td><span class="badge bg-{{ $notification->color }}">{{ $notification->color }}</span>
                                    </td>
                                    <td>{{ $notification->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center align-items-center">
                        {{ $noticeServices->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/js/plugins/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            height: '400',
            selector: '#content',
            content_style: 'body { font-family: "Inter", sans-serif; }',
            menubar: false,
            toolbar: [
                'styleselect fontselect fontsizeselect',
                'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify',
                'bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code'
            ],
            plugins: 'advlist autolink link image lists charmap print preview code'
        });
    </script>
@endsection
