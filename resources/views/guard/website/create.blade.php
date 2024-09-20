@extends('guard.layouts.master')
@section('title', 'Tạo Website riêng')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-globe"></i> Tạo Website riêng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row col-reverse">
                        <div class="col-md-6">
                            <form action="{{ route('create.website.post') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="domain">Tên miền</label>
                                    <input type="text" class="form-control" id="domain" placeholder="Tên miền"
                                        autocomplete="off" name="domain" value="{{ $website->name ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary">
                                        Lưu ngay
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="alert bg-warning mb-0">
                                <ul class="mb-0 text-white">
                                    <li>Bạn phải đạt cấp bậc cộng tác viên hoặc đại lý mới có thể tạo web con!</li>
                                    <li>Nghiêm cấm các tiên miền có chữ : Facebook, Instagram để tránh bị vi phạm bản quyền.
                                    </li>
                                    <li>Khách hàng tạo tài khoản và sử dụng dịch vụ ở site con. Tiền sẽ trừ vào tài khoản
                                        của đại lý
                                        ở site chính. Vì thế để khách hàng mua được tài khoản đại lý phải còn số dư.</li>
                                    <li>
                                        Bạn cần thêm nameserver của chúng tôi vào tên miền của bạn để website hoạt động.
                                        <br>
                                        <span class="fw-bold">Nameserver 1:</span> <span class="text-primary fw-bold">{{ site('nameserver_1') }}</span>
                                        <br>
                                        <span class="fw-bold">Nameserver 2:</span> <span class="text-primary fw-bold">{{ site('nameserver_2') }}</span>
                                    </li>
                                    <li>Chúng tôi hỗ trợ mục đích kinh doanh của tất cả cộng tác viên và đại lý!</li>
                                    <li class="fw-bold">
                                        Nếu bạn đã thêm website rồi, hãy chờ liên hệ cho chúng tôi để kích hoạt website của
                                        bạn nhanh nhất
                                        <br>
                                        <a href="{{ site('zalo') }}" target="_blank" class="fw-bold">
                                            <i class="fab fa-messages"></i> Zalo</a>
                                        <br>
                                        <a href="{{ site('facebook') }}" target="_blank" class="fw-bold">
                                            <i class="fab fa-facebook"></i> Facebook</a>
                                        <br>
                                        <a href="{{ site('telegram') }}" target="_blank" class="fw-bold">
                                            <i class="fab fa-telegram"></i> Telegram</a>

                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
