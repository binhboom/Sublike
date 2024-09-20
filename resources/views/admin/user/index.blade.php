@extends('admin.layouts.master')
@section('title', 'Danh sách thành viên')

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avtar bg-light-success me-3">
                                <i class="ti ti-users fs-2"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">
                                    {{ \App\Models\User::where('domain', request()->getHost())->count() }}
                                </h4>
                                <p class="mb-0 text-opacity-75 capitalize">Tổng thành viên</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avtar bg-light-warning me-3">
                                <i class="ti ti-coin fs-2"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">
                                    {{ number_format(\App\Models\User::where('domain', request()->getHost())->sum('balance')) }}
                                    VNĐ
                                </h4>
                                <p class="mb-0 text-opacity-75 capitalize">Tổng số dư</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avtar bg-light-primary me-3">
                                <i class="ti ti-access-point fs-2"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">
                                    {{ \App\Models\User::where('domain', request()->getHost())->whereDate('updated_at', today())->count() }}
                                </h4>
                                <p class="mb-0 text-opacity-75 capitalize">Hoạt động hôm nay</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avtar bg-light-danger me-3">
                                <i class="ti ti-lock fs-2"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">
                                    {{ \App\Models\User::where('domain', request()->getHost())->where('two_factor_auth', 'yes')->count() }}
                                </h4>
                                <p class="mb-0 text-opacity-75 capitalize">Tài khoản xác thực</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Danh sách thành viên</h5>
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Tìm kiếm dữ liệu" name="search"
                                        value="{{ request()->search }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-select" name="level">
                                        <option value="">-- Loại Tài Khoản --</option>
                                        <option value="member" @if (request()->level == 'member') selected @endif>Thành
                                            viên</option>
                                        <option value="collaborator" @if (request()->level == 'collaborator') selected @endif>
                                            Cộng tác viên</option>
                                        <option value="agency" @if (request()->level == 'agency') selected @endif>Đại lý
                                        </option>
                                        <option value="distributor" @if (request()->level == 'distributor') selected @endif>
                                            Nhà phân phối</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-select" name="role">
                                        <option value="">-- Chức vụ --</option>
                                        <option value="member" @if (request()->role == 'member') selected @endif>Thành
                                            viên</option>
                                        <option value="admin" @if (request()->role == 'admin') selected @endif>Quản
                                            trị viên</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-select" name="status">
                                        <option value="">-- Trạng thái --</option>
                                        <option value="active" @if (request()->status == 'active') selected @endif>Hoạt
                                            động</option>
                                        <option value="inactive" @if (request()->status == 'inactive') selected @endif>Không
                                            hoạt động</option>
                                        <option value="banned" @if (request()->status == 'banned') selected @endif>Bị cấm
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group d-flex justify-content-between align-items-center gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti ti-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.user') }}" class="btn btn-secondary w-100">
                                        <i class="ti ti-rotate-clockwise"></i> Làm mới
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped table-vcenter fs-5">
                            <thead>
                                <tr>
                                    <th>
                                        ID
                                    </th>
                                    <th>Thao tác</th>
                                    <th>Người dùng</th>
                                    <th>tài khoản</th>
                                    <th>Số dư</th>
                                    <th>Tổng nạp</th>
                                    <th>Cấp bậc</th>
                                    <th>Chức vụ</th>
                                    <th>Trạng thái</th>
                                    <th>Bảo mật</th>
                                    <th>Địa chỉ</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody class="fw-bold">
                                @if ($users->isEmpty())
                                    @include('admin.components.table-search-not-found', [
                                        'colspan' => 8,
                                    ])
                                @else
                                    @foreach ($users as $user)
                                        <tr class="fs-6">
                                            <td>#{{ $user->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.user.detail', ['id' => $user->id]) }}"
                                                    class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                                                    title="Xem chi tiết">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.user.balance', ['username' => $user->username]) }}"
                                                    class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                                    title="Sửa số dư">
                                                    <i class="ti ti-pencil"></i>
                                                </a>

                                                <a href="{{ route('admin.user.transactions', ['username' => $user->username]) }}"
                                                    class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                    title="Lịch sử giao dịch">
                                                    <i class="ti ti-shopping-cart"></i>
                                                </a>
                                                <a href="{{ route('admin.user.delete', ['id' => $user->id]) }}"
                                                    class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                                    title="Xóa">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avtar me-3">
                                                        <img src="{{ $user->avatar }}" alt="avatar"
                                                            class="img-fluid rounded-circle" width="50">
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                                        <span class="text-opacity">{{ $user->email }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->username }}</td>
                                            <td class="text-sm fw-bold">{{ number_format($user->balance) }} VNĐ</td>
                                            <td class="text-sm fw-bold">{{ number_format($user->total_recharge) }} VNĐ
                                            </td>
                                            <td>{!! levelUser($user->level, true) !!}</td>
                                            <td>
                                                @if ($user->role == 'admin')
                                                    <span class="badge bg-danger">Quản trị viên</span>
                                                @else
                                                    <span class="badge bg-success">Thành viên</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->status == 'active')
                                                    <span class="badge bg-success">Hoạt động</span>
                                                @elseif ($user->status == 'inactive')
                                                    <span class="badge bg-warning">Không hoạt động</span>
                                                @else
                                                    <span class="badge bg-danger">Bị cấm</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->two_factor_auth === 'yes')
                                                    <span class="badge bg-success">Bật</span>
                                                @else
                                                    <span class="badge bg-danger">Tắt</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->last_ip }}</td>
                                            <td>{{ $user->created_at }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center align-items-center">
                            {{ $users->appends(request()->all())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

@endsection
