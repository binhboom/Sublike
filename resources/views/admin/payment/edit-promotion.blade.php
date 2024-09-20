@extends('admin.layouts.master')
@section('title', 'Chinh sửa khuyến mãi')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.payment.promotion.update', ['id' => $promotion->id]) }}" method="POST">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="min_balance" value="{{ $promotion->min_balance }}"
                        placeholder="Số tiền tối thiểu">
                    <label for="min_balance">Số tiền tối thiểu</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="percentage" value="{{ $promotion->percentage }}"
                        placeholder="Phần trăm">
                    <label for="percentage">Chiết khấu khuyến mãi</label>
                </div>
                <div class="form-floating mb-3">
                    <select name="status" id="status" class="form-select">
                        <option value="active" {{ $promotion->status === 'active' ? 'selected' : '' }}>Bật</option>
                        <option value="inactive" {{ $promotion->status === 'inactive' ? 'selected' : '' }}>Tắt</option>
                    </select>
                    <label for="status">Trạng thái</label>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i>
                        Lưu cấu hình
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
