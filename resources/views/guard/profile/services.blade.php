@extends('guard.layouts.master')
@section('title', 'Cấp Bậc & Dịch Vụ')

@section('content')
    <div class="row">
        <div class="col-md-12 row">
            <div class="col-md-4">
                <div class="card price-card">
                    <div class="card-body">
                        <div class="price-head">
                            <h4 class="mb-0">Cộng Tác Viên</h4>
                            <div class="price-price mt-4">{{ number_format(site('collaborator')) }} <small>VND</small></div>
                            <div class="d-grid">
                                <a class="btn btn-outline-primary mt-4" href="{{ route('account.recharge') }}">Nâng Cấp
                                    Ngay</a>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush product-list">
                            <li class="list-group-item enable">Giảm giá dịch vụ. <i
                                    class="fas fa-check-circle text-primary"></i></li>
                            <li class="list-group-item enable">Có thể tạo website riêng. <i
                                    class="fas fa-check-circle text-primary"></i>
                            </li>
                            <li class="list-group-item enable">Giao diện trang website riêng. <i
                                    class="fas fa-times-circle"></i></li>
                            <li class="list-group-item enable">Có nhóm chat hỗ trợ 24/7. <i
                                    class="fas fa-check-circle text-primary"></i></li>
                            <li class="list-group-item enable">Có các ưu đãi quyền lợi riêng. <i
                                    class="fas fa-times-circle"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card price-card price-popular">
                    <div class="card-body">
                        <div class="price-head">
                            <h4 class="mb-0">Nhà Phân Phối</h4>
                            <div class="price-price mt-4">{{ number_format(site('distributor')) }} <small>VND</small></div>
                            <div class="d-grid">
                                <a class="btn btn-outline-primary mt-4" href="{{ route('account.recharge') }}">Nâng Cấp
                                    Ngay</a>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush product-list">
                            <li class="list-group-item enable">Giảm giá dịch vụ. <i
                                    class="fas fa-check-circle text-primary"></i></li>
                            <li class="list-group-item enable">Có thể tạo website riêng.<i
                                    class="fas fa-check-circle text-primary"></i>
                            </li>
                            <li class="list-group-item enable">Giao diện trang website riêng. <i
                                    class="fas fa-times-circle"></i></li>
                            <li class="list-group-item enable">Có nhóm chat hỗ trợ 24/7. <i
                                    class="fas fa-check-circle text-primary"></i></li>
                            <li class="list-group-item enable">Có các ưu đãi quyền lợi riêng. <i
                                    class="fas fa-check-circle text-primary"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card price-card">
                    <div class="card-body">
                        <div class="price-head">
                            <h4 class="mb-0">Đại Lý</h4>
                            <div class="price-price mt-4">{{ number_format(site('agency')) }} <small>VND</small></div>
                            <div class="d-grid">
                                <a class="btn btn-outline-primary mt-4" href="{{ route('account.recharge') }}">Nâng Cấp
                                    Ngay</a>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush product-list">
                            <li class="list-group-item enable">Giảm giá dịch vụ. <i
                                    class="fas fa-check-circle text-primary"></i></li>
                            <li class="list-group-item enable">Có thể tạo website riêng.<i
                                    class="fas fa-check-circle text-primary"></i>
                            </li>
                            <li class="list-group-item enable">Giao diện trang website riêng. <i
                                    class="fas fa-times-circle"></i></li>
                            <li class="list-group-item enable">Có nhóm chat hỗ trợ 24/7. <i
                                    class="fas fa-check-circle text-primary"></i></li>
                            <li class="list-group-item enable">Có các ưu đãi quyền lợi riêng. <i
                                    class="fas fa-check-circle text-primary"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Bảng Giá Dịch Vụ</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <ul class="nav nav-pills row" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                @foreach ($platforms as $platform)
                                    <li class="col-md-2 text-center">
                                        <a class="nav-link {{ $loop->first ? 'active' : '' }} d-flex align-items-center gap-1 justify-content-center"
                                            id="v-pills-{{ $platform->id }}-tab" data-bs-toggle="pill"
                                            href="#v-pills-{{ $platform->id }}" role="tab"
                                            aria-controls="v-pills-{{ $platform->id }}" aria-selected="true">
                                            <img src="{{ $platform->image }}" class="wid-25" alt="">
                                            <span>{{ $platform->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="col-md-12">
                            <div class="tab-content" id="v-pills-tabContent">

                                @foreach ($platforms as $platform)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="v-pills-{{ $platform->id }}" role="tabpanel"
                                        aria-labelledby="v-pills-{{ $platform->id }}-tab">


                                        {{-- accordition service --}}
                                        <div class="accordion" id="accordionExample">
                                            @foreach ($platform->services as $service)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading{{ $service->id }}">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse{{ $service->id }}"
                                                            aria-expanded="false"
                                                            aria-controls="collapse{{ $service->id }}">
                                                            {{ $service->name }}
                                                        </button>
                                                    </h2>
                                                    <div id="collapse{{ $service->id }}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="heading{{ $service->id }}"
                                                        data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">

                                                            <div class="table-responsive">
                                                                <table
                                                                    class="table table-bordered table-hover table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Thông tin</th>
                                                                            <th>Máy chủ</th>
                                                                            <th>Thành viên</th>
                                                                            <th>Cộng tác viên</th>
                                                                            <th>Đại lý</th>
                                                                            <th>Nhà phân phối</th>
                                                                            <th>Trạng thái</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        @foreach ($service->servers->where('domain', request()->getHost()) as $server)
                                                                            <tr></tr>
                                                                            <td>
                                                                                <ul class="mb-0">
                                                                                    <li class="fs-6 fw-bold">Thông tin:
                                                                                        {{ $server->name }}
                                                                                    </li>
                                                                                    <li class="fs-6 fw-bold">Min - Max:
                                                                                        {{ number_format($server->min) }} ~
                                                                                        {{ number_format($server->max) }}
                                                                                    </li>
                                                                                    <li class="fs-6 fw-bold">Giới hạn ngày:
                                                                                        <span class="text-danger">
                                                                                            @if ($server->limit_day == 0)
                                                                                                Không giới hạn
                                                                                            @else
                                                                                                {{ $server->limit_day }}
                                                                                            @endif
                                                                                        </span>
                                                                                    </li>
                                                                                </ul>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge bg-primary">{{ $server->package_id }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge bg-success">{{ $server->price_member }}đ</span>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge bg-info">{{ $server->price_collaborator }}đ</span>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="badge bg-warning">{{ $server->price_agency }}đ</span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge bg-danger">
                                                                                    Liên hệ Admin
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                {!! statusAction($server->status, true) !!}
                                                                            </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
