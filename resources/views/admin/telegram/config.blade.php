@extends('admin.layouts.master')
@section('title', 'Cấu hình Telegram')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Cấu hình Telegram</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.website.update') }}" method="POST">
                        @csrf
                        {{--  <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="logo" name="logo"
                                    placeholder="Nhập dữ liệu" value="{{ siteValue('logo') }}">
                                <label for="logo">Logo</label>
                            </div> --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Cấu hình Bot Thông báo</h5>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="telegram_bot_token"
                                        name="telegram_bot_token" placeholder="Nhập dữ liệu"
                                        value="{{ siteValue('telegram_bot_token') }}">
                                    <label for="telegram_bot_token">Token Bot</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="telegram_chat_id" name="telegram_chat_id"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('telegram_chat_id') }}">
                                    <label for="telegram_chat_id">Chat ID</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="telegram_bot_username"
                                        name="telegram_bot_username" placeholder="Nhập dữ liệu"
                                        value="{{ siteValue('telegram_bot_username') }}">
                                    <label for="telegram_bot_username">Username Bot</label>
                                </div>
                                <div class="">
                                    <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Cấu hình Bot Chat</h5>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="telegram_bot_chat_token"
                                        name="telegram_bot_chat_token" placeholder="Nhập dữ liệu"
                                        value="{{ siteValue('telegram_bot_chat_token') }}">
                                    <label for="telegram_bot_chat_token">Token Bot</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="telegram_bot_chat_id"
                                        name="telegram_bot_chat_id" placeholder="Nhập dữ liệu"
                                        value="{{ siteValue('telegram_bot_chat_id') }}">
                                    <label for="telegram_bot_chat_id">Chat ID</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="telegram_bot_chat_username"
                                        name="telegram_bot_chat_username" placeholder="Nhập dữ liệu"
                                        value="{{ siteValue('telegram_bot_chat_username') }}">
                                    <label for="telegram_bot_chat_username">Username Bot</label>
                                </div>
                                <div class="">
                                    <button type="button" class="btn btn-primary" id="btn-setWebhook">Set Webhook</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#btn-setWebhook').click(function() {
                $.ajax({
                    url: "{{ route('admin.telegram.set-webhook') }}",
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btn-setWebhook').html('Đang xử lý...');
                    },
                    complete: function() {
                        $('#btn-setWebhook').html('Set Webhook');
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: response.message,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Thất bại',
                                text: response.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Thất bại',
                            text: 'Có lỗi xảy ra, vui lòng thử lại sau',
                        });
                    }
                });
            });
        });
    </script>
@endsection
