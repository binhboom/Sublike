@extends('admin.layouts.master')
@section('title', 'Thay đổi số dư')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Chỉnh sửa số dư</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.user.update-balance') }}" method="POST">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" value="{{ request()->get('username') }}" name="username">
                            <label for="" class="form-label">
                                Tài khoản
                            </label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="balance" value="{{ request()->get('balance') }}" name="balance">
                            <label for="" class="form-label">
                                Số dư
                            </label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="note" name="note" style="height: 150px;">{{ request()->get('note') }}</textarea>
                            <label for="" class="form-label">
                                Ghi chú
                            </label>
                        </div>
                        <div class="form-floating mb-3">'
                            <select class="form-control" id="type" name="type">
                                <option value="add" {{ request()->get('type') == 'add' ? 'selected' : '' }}>Cộng tiền</option>
                                <option value="sub" {{ request()->get('type') == 'sub' ? 'selected' : '' }}>Trừ tiền</option>
                            </select>
                            <label for="" class="form-label">
                                Thao tác
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
