@extends('guard.layouts.master')

@section('title', 'Trang Chủ')

@section('content')
    <div class="row">
        @if (Auth::check())
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-primary me-1">
                                    <i class="ti ti-currency-dollar fs-2"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ number_format(Auth::user()->balance) }} đ</h4>
                                <h6 class="mb-0">Số dư hiện tại</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-warning me-1">
                                    <i class="ti ti-calendar-minus fs-2"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ number_format(Auth::user()->total_recharge) }} đ</h4>
                                <h6 class="mb-0">Tổng nạp tháng</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-success me-1">
                                    <i class="ti ti-layers-intersect fs-2"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ number_format(Auth::user()->total_recharge) }} đ</h4>
                                <h6 class="mb-0">Tổng nạp</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-info me-1">
                                    <i class="ti ti-diamond fs-2"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ levelUser(Auth::user()->level) }}</h4>
                                <h6 class="mb-0">Cấp bậc</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-8">
            <div class="scroll h-500px">
                @foreach (\App\Models\NoticeSystem::where('domain', request()->getHost())->orderBy('id', 'desc')->get() as $noticeSystem)
                    <div class="alert bg-{{ $noticeSystem->color }} text-white fw-bold" role="alert">
                        <div class="mb-0">
                            {!! $noticeSystem->content !!}
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- <div class="row">
                @foreach (\App\Models\ServicePlatform::where('domain', env('APP_MAIN_SITE'))->get() as $platform)
                    <h2 class="text-center fw-bold text-gray-700">{{ $platform->name }}</h2>
                   @foreach ($platform->services as $service)
                   <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('service',['platform' => $platform->slug, 'service' => $service->slug]) }}" class="d-flex justify-content-center flex-column align-items-center gap-2">
                                <img src="{{ $service->image }}" width="50" height="50" class="img-fluid" alt="">
                                <div class="text-center fw-bold text-gray-700">{{ $service->name }}</div>
                            </a>
                        </div>
                    </div>
                </div>
                   @endforeach
                @endforeach
            </div> --}}
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Hoạt động gần đây</h5>
                </div>
                <div class="card-body">
                    <div class="scroll h-350px">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item list-group-item-action py-2 px-3 cursor-pointer rounded">
                                @foreach (\App\Models\NoticeService::where('domain', request()->getHost())->orderBy('id', 'desc')->get() as $noticeService)
                                    <div class="media align-items-center gap-2">
                                        <div class="chat-avtar">
                                            <div class="avtar avtar-s bg-light-{{ $noticeService->color }}">
                                                <i class="ti ti-bell-ringing fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="media-body mx-2">
                                            <span class="f-18 text-muted fw-bold mb-1">
                                                {!! $noticeService->content !!}
                                            </span>
                                            <p class="f-12 text-muted"><i class="ti ti-clock"></i>
                                                {{ $noticeService->created_at }}</p>
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

    <div class="modal fade modal-animate" id="notiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h2>THÔNG BÁO</h2>
                    </div>
                    {!! siteValue('notice') !!}
                    <div class="mt-2 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary shadow-2" id="btn-close-notice">Tôi đã đọc</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            let isNoticeModal = localStorage.getItem('isNoticeModal');

            let time = 60 * 60 * 1000;

            if (new Date().getTime() - isNoticeModal > time) {
                $('#notiModal').modal('show');
            }

            $('#btn-close-notice').click(function() {
                localStorage.setItem('isNoticeModal', new Date().getTime());
                $('#notiModal').modal('hide');
            });

        });
    </script>
@endsection
