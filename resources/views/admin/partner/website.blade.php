@extends('admin.layouts.master')
@section('title', 'Danh sách Website Con')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Danh sách các Website Con</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thao tác</th>
                                    <th>Tên Website</th>
                                    <th>Trạng thái</th>
                                    <th>Trạng thái Cloudflare</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($partnerWebsites as $partnerWebsite)
                                    <tr>
                                        <td>{{ $partnerWebsite->id }}</td>
                                        <td>
                                            {{--  <a href="{{ route('admin.partner.website.edit', $partnerWebsite->id) }}"
                                                class="btn btn-primary btn-sm">Sửa</a>
                                             --}}
                                            <a href="{{ route('admin.website.partner.delete', $partnerWebsite->id) }}"
                                                class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top"
                                                title="Xóa">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            {{-- kiểm tra trạng thái cloudflare --}}
                                            @if ($partnerWebsite->zone_status !== 'active')
                                                <a href="{{ route('admin.website.partner.active', $partnerWebsite->id) }}"
                                                    class="btn btn-success btn-sm" data-toggle="tooltip"
                                                    data-placement="top" title="Kích hoạt">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            @else
                                                {{-- <a href="{{ route('admin.website.partner.deactive', $partnerWebsite->id) }}"
                                                    class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top"
                                                    title="Ngưng kích hoạt">
                                                    <i class="fa fa-times"></i>
                                                </a> --}}
                                            @endif
                                        </td>
                                        <td>{{ $partnerWebsite->name }} ({{ $partnerWebsite->domain }})</td>
                                        <td>
                                            @if ($partnerWebsite->status == 'active')
                                                <span class="badge bg-success">Hoạt động</span>
                                            @elseif($partnerWebsite->status == 'inactive')
                                                <span class="badge bg-danger">Không hoạt động</span>
                                            @else
                                                <span class="badge bg-warning">Chờ kích hoạt</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($partnerWebsite->zone_status == 'active')
                                                <span class="badge bg-success">Hoạt động</span>
                                            @elseif($partnerWebsite->zone_status == 'inactive')
                                                <span class="badge bg-danger">Không hoạt động</span>
                                            @else
                                                <span class="badge bg-warning">Chờ kích hoạt</span>
                                            @endif
                                        </td>
                                        <td>{{ $partnerWebsite->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end align-items-center">
                            {{ $partnerWebsites->appends(request()->all())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
