@extends('guard.layouts.master')

@section('title', 'Nạp Tiền')

@section('content')
    <div class="row">

        <div class="col-md-12 mb-3">
            <div class="row">
                <div class="col-6 d-grid gap-2">
                    <a href="{{ route('account.recharge') }}" class="btn btn-outline-primary">
                        Ngân hàng</a>
                </div>
                <div class="col-6 d-grid gap-2">
                    <a href="{{ route('account.recharge.card') }}" class="btn btn-primary">
                        Thẻ cào</a>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Nạp thẻ cào</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('account.recharge.card.post') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="" class="form-label">Loại thẻ</label>
                                    <select name="card_type" id="card_type" class="form-control">
                                        <option value="VIETTEL">Viettel (Chiết khấu: {{ site('percent_card') ?? 25 }}%)
                                        </option>
                                        <option value="MOBIFONE">Mobifone (Chiết khấu: {{ site('percent_card') ?? 25 }}%)
                                        </option>
                                        <option value="VINAPHONE">Vinaphone (Chiết khấu: {{ site('percent_card') ?? 25 }}%)
                                        </option>
                                        <option value="VIETNAMMOBILE">Vietnamobile (Chiết khấu:
                                            {{ site('percent_card') ?? 25 }}%)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="" class="form-label">Mệnh giá</label>
                                    <select name="card_value" id="card_value" class="form-control">
                                        <option value="10000">10,000 VNĐ</option>
                                        <option value="20000">20,000 VNĐ</option>
                                        <option value="30000">30,000 VNĐ</option>
                                        <option value="50000">50,000 VNĐ</option>
                                        <option value="100000">100,000 VNĐ</option>
                                        <option value="200000">200,000 VNĐ</option>
                                        <option value="300000">300,000 VNĐ</option>
                                        <option value="500000">500,000 VNĐ</option>
                                        <option value="1000000">1,000,000 VNĐ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="" class="form-label">Seri</label>
                                    <input type="text" name="card_seri" id="card_seri" class="form-control"
                                        placeholder="Nhập dữ liệu....">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="" class="form-label">Mã thẻ</label>
                                    <input type="text" name="card_code" id="card_code" class="form-control"
                                        placeholder="Nhập dữ liệu....">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary col-12" id="btnRechargeCard">Nạp thẻ cào</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Lịch sử nạp thẻ cào</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Thời gian</th>
                                    <th>Loại thẻ</th>
                                    <th>Mệnh giá</th>
                                    <th>Seri</th>
                                    <th>Mã thẻ</th>
                                    <th>Thực nhận</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($rechargeCards->isEmpty())
                                    @include('admin.components.table-search-not-found', ['colspan' => 8])
                                @endif

                                @foreach ($rechargeCards as $rechargeCard)
                                    <tr>
                                        <td>{{ $rechargeCard->id }}</td>
                                        <td>{{ $rechargeCard->created_at->diffForHumans() }}</td>
                                        <td>{{ $rechargeCard->type }}</td>
                                        <td>{{ number_format($rechargeCard->amount) }} VNĐ</td>
                                        <td>{{ $rechargeCard->serial }}</td>
                                        <td>{{ $rechargeCard->pin }}</td>
                                        <td>{{ number_format($rechargeCard->real_amount) }} VNĐ</td>
                                        <td>
                                            @if ($rechargeCard->status == 'pending')
                                                <span class="badge bg-warning">Chờ xử lý</span>
                                            @elseif ($rechargeCard->status == 'success')
                                                <span class="badge bg-success">Thành công</span>
                                            @else
                                                <span class="badge bg-danger">Thất bại</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center align-items-center">
                            {{ $rechargeCards->appends(request()->all())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
