@extends('guard.layouts.master')

@section('title', 'Biến động số dư')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Biến động số dư</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Giao dịch</th>
                                    <th>Thời gian</th>
                                    <th>Mã đơn</th>
                                    <th>Số tiền</th>
                                    <th>Nội dung</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
