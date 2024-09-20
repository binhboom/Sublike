@extends('admin.layouts.master')
@section('title', 'Thông tin người dùng')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <h5 class="card-header">Thông tin người dùng</h5>
                <div class="card-body">
                    <form action="{{ route('admin.user.update', ['username' => $user->username]) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" value="{{ $user->name }}"
                                        name="name">
                                    <label for="name">Họ và tên</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="email" value="{{ $user->email }}"
                                        disabled>
                                    <label for="email">Địa chỉ Email</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" value="{{ $user->username }}"
                                        disabled>
                                    <label for="username">Tài khoản</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="balance"
                                        value="{{ number_format($user->balance) }} VNĐ" disabled>
                                    <label for="balance">Số dư</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="level" name="level">
                                        <option value="member" {{ $user->level == 'member' ? 'selected' : '' }}>Thành viên
                                        </option>
                                        <option value="collaborator" {{ $user->level == 'collaborator' ? 'selected' : '' }}>
                                            Cộng tác viên</option>
                                        <option value="agency" {{ $user->level == 'agency' ? 'selected' : '' }}>Đại lý
                                        </option>
                                        <option value="distributor" {{ $user->level == 'distributor' ? 'selected' : '' }}>
                                            Nhà phân phối</option>
                                    </select>
                                    <label for="level">Cấp bậc</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="role" name="role">
                                        <option value="member" {{ $user->role == 'member' ? 'selected' : '' }}>Khách hàng
                                        </option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Quản trị viên
                                        </option>
                                    </select>
                                    <label for="role">Chức vụ</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Hoạt động
                                        </option>
                                        <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Không
                                            hoạt động</option>
                                        <option value="banned" {{ $user->status == 'banned' ? 'selected' : '' }}>Bị khoá
                                        </option>
                                    </select>
                                    <label for="status">Trạng thái</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" disabled=""
                                        value="{{ $user->last_login }}">
                                    <label for="last_login">Lần đăng nhập cuối</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" dirname="" value="{{ $user->last_ip }}" disabled>
                                    <label for="last_ip">Ip Location</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="{{ route('admin.user') }}" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.user.update-password', ['username' => $user->username]) }}"
                        method="POST">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password">
                            <label for="password">Mật khẩu mới</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                            <label for="password_confirmation">Nhập lại mật khẩu</label>
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
