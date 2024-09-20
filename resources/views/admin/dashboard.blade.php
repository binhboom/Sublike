@extends('admin.layouts.master')

@section('title', 'Trang Quản Trị')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avtar bg-light-success me-3">
                            <i class="ti ti-users fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($totalUser) }}</h4>
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
                            <h4 class="mb-0">{{ number_format($totalBalance) }}</h4>
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
                            <i class="ti ti-coin fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($totalRecharge) }}</h4>
                            <p class="mb-0 text-opacity-75 capitalize">Tổng đã nạp</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avtar bg-light-info me-3">
                            <i class="ti ti-users fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($totalUserToday) }}</h4>
                            <p class="mb-0 text-opacity-75 capitalize">thành viên hôm nay</p>
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
                            <h4 class="mb-0">{{ number_format($totalRevenue) }}</h4>
                            <p class="mb-0 text-opacity-75 capitalize">Tổng doanh thu</p>
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
                            <i class="ti ti-coin fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($totalRefund) }}</h4>
                            <p class="mb-0 text-opacity-75 capitalize">Tổng hoàn tiền</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="avtar bg-light-info me-3">
                            <i class="ti ti-coin fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($totalCanceled) }}</h4>
                            <p class="mb-0 text-opacity-75 capitalize">Tổng hủy đơn</p>
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
                            <i class="ti ti-coin fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ number_format($totalRechargeToday) }}</h4>
                            <p class="mb-0 text-opacity-75 capitalize">Nạp tiền hôm nay</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thống kê doanh thu</h5>
                </div>
                <div class="card-body">
                    <div id="sdk-dlsk-fk"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="/assets/js/plugins/apexcharts.min.js"></script>
    <script>
        var options = {
            series: [{
                    name: 'Nạp tiền',
                    data: @json($data['recharge'])
                }, {
                    name: 'Đơn hàng',
                    data: @json($data['order'])
                },
                {
                    name: "Người dùng",
                    data: @json($data['user'])
                }
            ],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#FF4560', '#775DD0', '#008FFB'],
            stroke: {
                curve: 'smooth'
            },
            fill: {
                type: "solid",
                opacity: 1
            },
            title: {
                text: '',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: @json($labels)
            }

        };

        var chart = new ApexCharts(document.querySelector("#sdk-dlsk-fk"), options);
        chart.render();
    </script>
@endsection
