console.log('%cĐược phát triển bởi: %cLương Bình Dương %c- https://fb.com/luongbinhduong.dev', 'color: #007bff; font-weight: bold; font-size: 20px;', 'color: #007bff; font-weight: bold; font-size: 20px;', 'color: #007bff; font-weight: bold; font-size: 20px;');

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


const coppy = (element) => {
    const textArea = document.createElement("textarea");
    textArea.value = element;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("copy");
    document.body.removeChild(textArea);
    toastrNotify(`Sao chép thành công {${element}}`, "success");
}

const toastrNotify = (text, icon) => {
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "10000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    toastr[icon](text, 'Thông báo!');
}

const copyToClipboard = (element) => {
    const $temp = $("<input>");
    $("body").append($temp);
    $temp.val(element).select();
    document.execCommand("copy");
    $temp.remove();
    toastrNotify(`Sao chép thành công {${element}}`, "success");
}


const handleCheckLevel = (levelUser) => {
    $.ajax({
        url: '/level/check',
        type: 'GET',
        data: {
            level: levelUser
        },
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                Swal.fire("Thành công", response.message, "success")
            }
        }
    });
}
