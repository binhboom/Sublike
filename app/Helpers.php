<?php

use App\Models\ConfigSite;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Carbon;

if (!function_exists('siteValue')) {
    function siteValue($column, $domain = null)
    {
        $domain = $domain ? $domain : request()->getHost();

        $configSite = ConfigSite::where('domain', $domain)->where('status', 'active')->first();
        if ($configSite) {
            return $configSite->$column;
        } else {
            return null;
        }
    }
}

if (!function_exists('site')) {
    function site($column, $domain = null)
    {
        $domain = $domain ? $domain : request()->getHost();
        $configSite = ConfigSite::where('domain', $domain)->where('status', 'active')->first();
        if ($configSite) {
            return $configSite->$column;
        } else {
            return null;
            
        }
        function site($key) {
            $settings = [
                'name_site' => 'Your Site Name',
                'logo' => 'path-to-your-logo.png',
                'description' => 'Your site description',
            ];
    
            return $settings[$key] ?? null;
     }
    }
}

if (!function_exists('statusAction')) {
    function statusAction($status, $html = false)
    {
        if ($html) {
            switch ($status) {
                case 'active':
                    return '<span class="badge bg-success">Hoạt động</span>';
                case 'inactive':
                    return '<span class="badge bg-danger">Không hoạt động</span>';
                default:
                    return '<span class="badge bg-success">Hoạt động</span>';
            }
        }

        switch ($status) {
            case 'active':
                return 'Hoạt động';
            case 'inactive':
                return 'Không hoạt động';
            default:
                return 'Hoạt động';
        }
    }
}

if (!function_exists('levelUser')) {
    function levelUser($level, $html = false)
    {
        if ($html) {
            switch ($level) {
                case 'member':
                    return '<span class="badge bg-primary">Thành viên</span>';
                case 'collaborator':
                    return '<span class="badge bg-warning">Cộng tác viên</span>';
                case 'agency':
                    return '<span class="badge bg-info">Đại lý</span>';
                case 'distributor':
                    return '<span class="badge bg-success">Nhà phân phối</span>';
                default:
                    return '<span class="badge bg-primary">Thành viên</span>';
            }
        }

        switch ($level) {
            case 'member':
                return 'Thành viên';
            case 'collaborator':
                return 'Cộng tác viên';
            case 'agency':
                return 'Đại lý';
            case 'distributor':
                return 'Nhà phân phối';
            default:
                return 'Thành viên';
        }
    }
}

if (!function_exists('getDomain')) {
    function getDomain()
    {
        return request()->getHost() ?? env('APP_MAIN_SITE');
    }
}

if (!function_exists('statusOrder')) {
    function statusOrder($status, $html = false, $isBreak = false)
    {
        //Processing, Completed, Cancelled, Refunded, Failed, Pending, Partially Refunded, Partially Completed, WaitingForRefund,
        if ($html) {
            switch ($status) {
                case 'Running':
                    return '<span class="badge bg-primary">Đang chạy</span>';
                case 'Processing':
                    return '<span class="badge bg-primary">Đang xử lý</span>';
                case 'Completed':
                    return '<span class="badge bg-success">Hoàn thành</span>';
                case 'Cancelled':
                    return '<span class="badge bg-danger">Đã hủy</span>';
                case 'Refunded':
                    return '<span class="badge bg-danger">Đã hoàn tiền</span>';
                case 'Failed':
                    return '<span class="badge bg-danger">Thất bại</span>';
                case 'Pending':
                    return '<span class="badge bg-warning">Chờ xử lý</span>';
                case 'Partially Refunded':
                    return '<span class="badge bg-danger">Hoàn tiền một phần</span>';
                case 'Partially Completed':
                    return '<span class="badge bg-warning">Hoàn thành một phần</span>';
                case 'WaitingForRefund':
                    return '<span class="badge bg-warning">Chờ hoàn tiền</span>';
                case 'Expired':
                    return '<span class="badge bg-danger">Đã Hết hạn</span>';
                case 'Success':
                    return '<span class="badge bg-success">Thành công</span>';
                case 'Active':
                    return '<span class="badge bg-success">Đang hoạt động</span>';
                default:
                    return '<span class="badge bg-primary">Đang xử lý</span>';
            }
        } else {
            switch ($status) {
                case 'Running':
                    return 'Đang chạy';
                case 'Processing':
                    return 'Đang xử lý';
                case 'Completed':
                    return 'Hoàn thành';
                case 'Cancelled':
                    return 'Đã hủy';
                case 'Refunded':
                    return 'Đã hoàn tiền';
                case 'Failed':
                    return 'Thất bại';
                case 'Pending':
                    return 'Chờ xử lý';
                case 'Partially Refunded':
                    return 'Hoàn tiền một phần';
                case 'Partially Completed':
                    return 'Hoàn thành một phần';
                case 'WaitingForRefund':
                    return 'Chờ hoàn tiền';
                case 'Expired':
                    return 'Hết hạn';
                default:
                    return 'Đang xử lý';
            }
        }
    }
}

// đếm số ngày còn lại trong remaining
if (!function_exists('remainingDays')) {
    function remainingDays($start, int $duration = 0, $m = false)
    {
        $startDate = Carbon::parse($start);

        // echo($duration . 'fkdsjlfkj');
        $endDate = $startDate->copy()->addDays($duration);

        $currentDate = Carbon::now();

        if ($currentDate->gte($endDate)) {
            return "Hết hạn";
        } else {
            $daysLeft = $currentDate->diffInDays($endDate);
            $daysLeft = number_format($daysLeft, 0, ',', '.');
            return $m === true ? $daysLeft . "Ngày" : $daysLeft;
        }
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice($price)
    {
        return number_format($price, 0, ',', '.');
    }
}


if (!function_exists('thecao')) {
    function thecao($telco, $code, $serial, $amount, $request_id, $partner_id, $sign, $command = 'charging')
    {

        $client = new Client();
        $options = [
            'multipart' => [
                [
                    'name' => 'telco',
                    'contents' => $telco
                ],
                [
                    'name' => 'code',
                    'contents' => $code
                ],
                [
                    'name' => 'serial',
                    'contents' => $serial
                ],
                [
                    'name' => 'amount',
                    'contents' => $amount
                ],
                [
                    'name' => 'request_id',
                    'contents' => $request_id
                ],
                [
                    'name' => 'partner_id',
                    'contents' => $partner_id
                ],
                [
                    'name' => 'sign',
                    'contents' => $sign
                ],
                [
                    'name' => 'command',
                    'contents' => $command
                ]
            ]
        ];
        $request = new Request('POST', 'https://gachthenhanh.net/chargingws/v2');
        $res = $client->sendAsync($request, $options)->wait();
        // echo $res->getBody();
        return json_decode($res->getBody());
    }
}
