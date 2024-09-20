@extends('admin.layouts.master')
@section('title', 'Chỉnh sửa máy chủ')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tạo mới dịch vụ</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.server.update', ['id' => $server->id]) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-floating mb-3">
                                    <select name="service" id="" class="form-select">
                                        @foreach (\App\Models\ServicePlatform::where('domain', env('APP_MAIN_SITE'))->orderBy('order', 'asc')->get() as $key => $platform)
                                            <option value="">-- {{ $platform->name }} --</option>
                                            @foreach ($platform->services as $service)
                                                <option value="{{ $service->id }}"
                                                    {{ $server->service_id == $service->id ? 'selected' : '' }}>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    <label for="service">Dịch vụ</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Tiêu đề máy chủ" value="{{ $server->name }}">
                                    <label for="name">Tiêu đề máy chủ</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea name="details" id="details" class="form-control" placeholder="Mô tả máy chủ" rows="5"
                                        style="height: 200px;">{{ $server->details }}</textarea>
                                    <label for="details">Mô tả máy chủ</label>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <select name="package_id" id="package_id" class="form-select">
                                                <option value="">-- Chọn máy chủ --</option>
                                                @for ($i = 1; $i <= 50; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ $server->package_id == $i ? 'selected' : '' }}>
                                                        Máy chủ {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <label for="package_id">Máy chủ</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <select name="get_uid" id="get_uid" class="form-select">
                                                <option value="">-- Chọn trạng thái --</option>
                                                <option value="on"
                                                    {{ $server->actions->first()->get_uid == 'on' ? 'selected' : '' }}>Bật
                                                </option>
                                                <option value="off"
                                                    {{ $server->actions->first()->get_uid == 'off' ? 'selected' : '' }}>Tắt
                                                </option>
                                            </select>
                                            <label for="get_uid">Tự động lấy UID</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" name="limit_day" id="limit_day"
                                                placeholder="Giới hạn ngày" min="0"
                                                value="{{ $server->limit_day }}">
                                            <label for="limit_day">Giới hạn ngày (Nhập 0
                                                thì sẽ không giới hạn)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="min" name="min"
                                                value="{{ $server->min }}">
                                            <label for="min">Mua tôi thiểu</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="max" name="max"
                                                value="{{ $server->max }}">
                                            <label for="max">Mua tôi đa</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="price_member" name="price_member"
                                                value="{{ $server->price_member }}">
                                            <label for="price_member">Giá thành viên</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="price_collaborator"
                                                name="price_collaborator" value="{{ $server->price_collaborator }}">
                                            <label for="price_collaborator">Giá cộng tác viên</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="price_agency"
                                                name="price_agency" value="{{ $server->price_agency }}">
                                            <label for="price_agency">Giá đại lý</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="price_distributor"
                                                name="price_distributor" value="{{ $server->price_distributor }}">
                                            <label for="price_distributor">Giá nhà phân bón</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="providerName" id="providerName" class="form-select">
                                                <option value="">-- Chọn nguồn dịch vụ --</option>
                                                <option value="subgiare"
                                                    {{ $server->providerName == 'subgiare' ? 'selected' : '' }}>
                                                    Subgiare.vn</option>
                                                <option value="2mxh"
                                                    {{ $server->providerName == '2mxh' ? 'selected' : '' }}>
                                                    App.2mxh.com</option>
                                                <option value="baostar"
                                                    {{ $server->providerName == 'baostar' ? 'selected' : '' }}>
                                                    Dichvu.baostar.pro</option>
                                                <option value="boosterviews"
                                                    {{ $server->providerName == 'boosterviews' ? 'selected' : '' }}>
                                                    Boosterviews.com</option>
                                                <option value="hacklike17"
                                                    {{ $server->providerName == 'hacklike17' ? 'selected' : '' }}>
                                                    Hacklike17.com</option>
                                                <option value="conglike"
                                                    {{ $server->providerName == 'conglike' ? 'selected' : '' }}>
                                                    Conglike.com</option>
                                                <option value="cheotuongtac"
                                                    {{ $server->providerName == 'cheotuongtac' ? 'selected' : '' }}>
                                                    Cheotuongtac.com</option>
                                                <option value="tuongtacsale"
                                                    {{ $server->providerName == 'tuongtacsale' ? 'selected' : '' }}>
                                                    Smm.tuongtacsale.com</option>
                                                <option value="trumsubre"
                                                    {{ $server->providerName == 'trumsubre' ? 'selected' : '' }}>
                                                    Trumsubre.com</option>
                                                <option value="traodoisub"
                                                    {{ $server->providerName == 'traodoisub' ? 'selected' : '' }}>
                                                    Traodoisub.com</option>
                                                <option value="smmgen"
                                                    {{ $server->providerName == 'smmgen' ? 'selected' : '' }}>
                                                    Smmgen.com</option>
                                                <option value="smmcoder"
                                                    {{ $server->providerName == 'smmcoder' ? 'selected' : '' }}>
                                                    Smmcoder.com</option>
                                                <option value="smmfollows"
                                                    {{ $server->providerName == 'smmfollows' ? 'selected' : '' }}>
                                                    Smmfollows.com</option>
                                                <option value="autolikez"
                                                    {{ $server->providerName == 'autolikez' ? 'selected' : '' }}>
                                                    Autolikez.com</option>
                                                @foreach (\App\Models\SmmPanelPartner::where('domain', env('APP_MAIN_SITE'))->get() as $smm)
                                                    <option value="{{ $smm->name }}"
                                                        {{ $server->providerName == $smm->name ? 'selected' : '' }}>
                                                        {{ $smm->name }}
                                                    </option>
                                                @endforeach

                                                <option value="codedynamic"
                                                    {{ $server->providerName == 'codedynamic' ? 'selected' : '' }}>
                                                    Codedynamic.net</option>
                                            </select>
                                            <label for="providerName">Nguồn dịch vụ</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="providerLink"
                                                name="providerLink" value="{{ $server->providerLink }}">
                                            <label for="providerLink">Đường dẫn dịch vụ</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="providerServer"
                                                name="providerServer" value="{{ $server->providerServer }}">
                                            <label for="providerServer">Máy chủ gốc</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="providerKey"
                                                name="providerKey" value="{{ $server->providerKey }}">
                                            <label for="providerKey">Mã dịch vụ</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="refund_status" id="refund_status" class="form-select">
                                                <option value="on"
                                                    {{ $server->action->refund_status == 'on' ? 'selected' : '' }}>
                                                    Có hoàn tiền
                                                </option>
                                                <option value="off"
                                                    {{ $server->action->refund_status == 'off' ? 'selected' : '' }}>
                                                    Không hoàn tiền
                                                </option>
                                            </select>
                                            <label for="refund_status">Hoàn tiền</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="warranty_status" id="warranty_status" class="form-select">
                                                <option value="on"
                                                    {{ $server->action->warranty_status == 'on' ? 'selected' : '' }}>
                                                    Có Bảo hành
                                                </option>
                                                <option value="off"
                                                    {{ $server->action->warranty_status == 'off' ? 'selected' : '' }}>
                                                    Không Bảo hành
                                                </option>
                                            </select>
                                            <label for="warranty_status">Bảo hành</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="renews_status" id="renews_status" class="form-select">
                                                <option value="on"
                                                    {{ $server->action->renews_status == 'on' ? 'selected' : '' }}>
                                                    Có Gia hạn
                                                </option>
                                                <option value="off"
                                                    {{ $server->action->renews_status == 'off' ? 'selected' : '' }}>
                                                    Không Gia hạn
                                                </option>
                                            </select>
                                            <label for="renews_status">Gia hạn</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="status" id="status" class="form-select">
                                                <option value="active"
                                                    {{ $server->status == 'active' ? 'selected' : '' }}>
                                                    Hoạt động
                                                </option>
                                                <option value="inactive"
                                                    {{ $server->status == 'inactive' ? 'selected' : '' }}>
                                                    Không hoạt động
                                                </option>
                                            </select>
                                            <label for="status">Trạng thái</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="visibility" id="visibility" class="form-select">
                                                <option value="public"
                                                    {{ $server->visibility == 'public' ? 'selected' : '' }}>
                                                    Công khai
                                                </option>
                                                <option value="private"
                                                    {{ $server->visibility == 'private' ? 'selected' : '' }}>
                                                    Riêng tư
                                                </option>
                                            </select>
                                            <label for="visibility">Hiển thị</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="reaction_status" id="reaction_status" class="form-select">
                                                <option value="">-- Chọn trạng thái --</option>
                                                <option value="on"
                                                    {{ $server->actions->first()->reaction_status == 'on' ? 'selected' : '' }}>
                                                    Bật
                                                </option>
                                                <option value="off"
                                                    {{ $server->actions->first()->reaction_status == 'off' ? 'selected' : '' }}>
                                                    Tắt
                                                </option>
                                            </select>
                                            <label for="reaction_status">Cảm xúc</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="quantity_status" id="quantity_status" class="form-select">
                                                <option value="">-- Chọn trạng thái --</option>
                                                <option value="on"
                                                    {{ $server->actions->first()->quantity_status == 'on' ? 'selected' : '' }}>
                                                    Bật
                                                </option>
                                                <option value="off"
                                                    {{ $server->actions->first()->quantity_status == 'off' ? 'selected' : '' }}>
                                                    Tắt
                                                </option>
                                            </select>
                                            <label for="quantity_status">Số lượng</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="comments_status" id="comments_status" class="form-select">
                                                <option value="">-- Chọn trạng thái --</option>
                                                <option value="on"
                                                    {{ $server->actions->first()->comments_status == 'on' ? 'selected' : '' }}>
                                                    Bật
                                                </option>
                                                <option value="off"
                                                    {{ $server->actions->first()->comments_status == 'off' ? 'selected' : '' }}>
                                                    Tắt
                                                </option>
                                            </select>
                                            <label for="comments_status">Bình luận</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="minutes_status" id="minutes_status" class="form-select">
                                                <option value="">-- Chọn trạng thái --</option>
                                                <option value="on"
                                                    {{ $server->actions->first()->minutes_status == 'on' ? 'selected' : '' }}>
                                                    Bật
                                                </option>
                                                <option value="off"
                                                    {{ $server->actions->first()->minutes_status == 'off' ? 'selected' : '' }}>
                                                    Tắt
                                                </option>
                                            </select>
                                            <label for="minutes_status">Số phút</label>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="time_status" id="time_status" class="form-select">
                                                <option value="off"
                                                    {{ $server->actions->first()->time_status == 'off' ? 'selected' : '' }}>
                                                    Tắt
                                                </option>
                                                <option value="on"
                                                    {{ $server->actions->first()->time_status == 'on' ? 'selected' : '' }}>
                                                    Bật
                                                </option>
                                            </select>
                                            <label for="time_status">Thời gian</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="posts_status" id="posts_status" class="form-select">
                                                <option value="off"
                                                    {{ $server->actions->first()->posts_status == 'off' ? 'selected' : '' }}>
                                                    Tắt
                                                </option>
                                                <option value="on"
                                                    {{ $server->actions->first()->posts_status == 'on' ? 'selected' : '' }}>
                                                    Bật
                                                </option>
                                            </select>
                                            <label for="posts_status">Bài viết</label>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> <strong>Lưu ý:</strong> Bạn
                                            cần nhập dữ liệu cảm xúc theo ví dụ như sau:
                                            <code>LIKE,HAHA,COMMENT</code> hoặc tất cả là <code>ALL</code>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="reaction_data"
                                                name="reaction_data"
                                                value="{{ $server->actions->first()->reaction_data }}">
                                            <label for="reaction_data">Dữ liệu cảm xúc</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> <strong>Lưu ý:</strong>
                                            Thời gian tính theo giây và phải là số nguyên dương
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="minutes_data"
                                                name="minutes_data"
                                                value="{{ $server->actions->first()->minutes_data }}">
                                            <label for="minutes_data">Dữ liệu thời gian</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Sửa máy chủ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
