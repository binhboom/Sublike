@extends('admin.layouts.master')
@section('title',
    'Cấu hình hệ thống
    ')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="py-2 px-3">
                    <ul class="nav nav-tabs profile-tabs justify-content-center" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="config-tab" data-bs-toggle="tab" href="#config" role="tab"
                                aria-selected="true">
                                <i class="ti ti-info-circle me-2"></i>Cấu hình chung
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="seo-tab" data-bs-toggle="tab" href="#seo" role="tab"
                                aria-selected="true">
                                <i class="ti ti-brand-airbnb me-2"></i>Seo Website
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="notify-tab" data-bs-toggle="tab" href="#notify" role="tab"
                                aria-selected="false" tabindex="-1">
                                <i class="ti ti-bell-ringing me-2"></i>Thông Báo
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="display-tab" data-bs-toggle="tab" href="#display" role="tab"
                                aria-selected="false" tabindex="-1">
                                <i class="ti ti-device-tv me-2"></i>Giao Diện
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane active show" id="config" role="tabpanel" aria-labelledby="config-tab">
                            <form action="{{ route('admin.website.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="facebook" name="facebook"
                                                placeholder="Nhập dữ liệu" value="{{ siteValue('facebook') }}">
                                            <label for="facebook">Link Facebook</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="Zalo" name="Zalo"
                                                placeholder="Nhập dữ liệu" value="{{ siteValue('zalo') }}">
                                            <label for="Zalo">Link Zalo</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="telegram" name="telegram"
                                                placeholder="Nhập dữ liệu" value="{{ siteValue('telegram') }}">
                                            <label for="telegram">Link Telegram</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="collaborator" name="collaborator"
                                                placeholder="Nhập dữ liệu" value="{{ siteValue('collaborator') }}">
                                            <label for="collaborator">Mức nạp Cộng Tác Viên</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="agency" name="agency"
                                                placeholder="Nhập dữ liệu" value="{{ siteValue('agency') }}">
                                            <label for="agency">Mức nạp Đại Lý</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="distributor"
                                                name="distributor" placeholder="Nhập dữ liệu"
                                                value="{{ siteValue('distributor') }}">
                                            <label for="distributor">Mức nạp Nhà Phân Phối</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="nameserver_1"
                                                name="nameserver_1" placeholder="Nhập dữ liệu"
                                                value="{{ siteValue('nameserver_1') }}">
                                            <label for="nameserver_1">NameServer 1 (cloudflare)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="nameserver_2"
                                                name="nameserver_2" placeholder="Nhập dữ liệu"
                                                value="{{ siteValue('nameserver_2') }}">
                                            <label for="nameserver_2">NameServer 2 (cloudflare)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" id="script_head" name="script_head" style="height: 150px;"
                                                placeholder="Nhập dữ liệu">{{ siteValue('script_head') }}</textarea>
                                            <label for="script_head">Script head</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" id="script_body" name="script_body" style="height: 150px;"
                                                placeholder="Nhập dữ liệu">{{ siteValue('script_body') }}</textarea>
                                            <label for="script_body">Script Body</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" id="script_footer" name="script_footer" style="height: 150px;"
                                                placeholder="Nhập dữ liệu">{{ siteValue('script_footer') }}</textarea>
                                            <label for="script_footer">Script Footer</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary col-12">Lưu dữ liệu</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                            <form action="{{ route('admin.website.update') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name_site" name="name_site"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('name_site') }}">
                                    <label for="name_site">Tên website</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('title') }}">
                                    <label for="title">Tiêu đề</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="description" name="description"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('description') }}">
                                    <label for="description">Mô tả</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="keywords" name="keywords"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('keywords') }}">
                                    <label for="keywords">Từ khoá</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="author" name="author"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('author') }}">
                                    <label for="author">Author</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="thumbnail" name="thumbnail"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('thumbnail') }}">
                                    <label for="thumbnail">Thumbnail</label>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary col-12">Lưu dữ liệu</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="notify" role="tabpanel" aria-labelledby="notify-tab">
                            <form action="{{ route('admin.website.update') }}" method="POST">
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="notice">Thông báo hệ Nổi</label>
                                    <input type="text" class="form-control" id="notice" name="notice"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('notice') }}">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary col-12">Lưu dữ liệu</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="display" role="tabpanel" aria-labelledby="display-tab">
                            <form action="{{ route('admin.website.update') }}" method="POST">
                                @csrf
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="logo" name="logo"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('logo') }}">
                                    <label for="logo">Logo</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="favicon" name="favicon"
                                        placeholder="Nhập dữ liệu" value="{{ siteValue('favicon') }}">
                                    <label for="favicon">Favicon</label>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary col-12">Lưu dữ liệu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

    <script src="/assets/js/plugins/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            height: '400',
            selector: '#notice',
            content_style: 'body { font-family: "Inter", sans-serif; }',
            menubar: false,
            toolbar: [
                'styleselect fontselect fontsizeselect',
                'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify',
                'bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code'
            ],
            plugins: 'advlist autolink link image lists charmap print preview code'
        });
    </script>
@endsection
