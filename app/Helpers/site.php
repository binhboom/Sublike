<?php

function site($key = null) {
    if ($key === null) {
        return 'My Website'; // Hoặc để trống: return '';
    } else {
        // Logic để trả về giá trị dựa trên key
        return 'Giá trị tương ứng với key';
    }
}

?>