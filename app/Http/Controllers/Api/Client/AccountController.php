<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BankResource;
use App\Http\Resources\Api\OrderApiResource;
use App\Http\Resources\Api\UserApiResource;
use App\Library\CloudflareController;
use App\Library\CpanelController;
use App\Models\Banking;
use App\Models\Order;
use App\Models\PartnerWebsite;
use App\Models\Recharge;
use App\Models\Service;
use App\Models\ServicePlatform;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function getMe(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Lấy thông tin thành công",
            'user' => new UserApiResource($request->user)
        ]);
    }

    public function changePassword(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'password_new' => 'required|string|min:6',
            'password_confirm' => 'required|string|min:6|same:password_new'
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => $valid->errors()->first()
            ]);
        } else {
            $user = User::where('username', $request->user->username)->where('domain', $request->getHost())->where('status', 'active')->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {

                    $user->password = Hash::make($request->password_new);
                    $user->save();

                    UserActivity::create([
                        'user_id' => $user->id,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'activity' => 'auth',
                        'note' => 'Thay đổi mật khẩu thành công, địa chỉ IP: ' . $request->ip(),
                        'domain' => request()->getHost(),
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'code' => 201,
                        'message' => "Thay đổi mật khẩu thành công!"
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'code' => 400,
                        'message' => "Mật khẩu cũ không đúng vui lòng thử lại!"
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Tài khoản không tồn tại"
                ]);
            }
        }
    }

    public function getActivities(Request $request)
    {
        $search = $request->search;
        $order = $request->order ? 'asc' : 'desc';
        $sort_by = $request->sort_by ? $request->sort_by : 'id';

        $activities = UserActivity::where('user_id', $request->user->id)
            ->where('domain', $request->getHost())
            ->where(function ($query) use ($search) {
                if ($search) {
                    $query->where('note', 'like', '%' . $search . '%');
                }
            })
            ->orderBy($sort_by, $order)
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Lấy thông tin thành công",
            'data' => $activities
        ]);
    }

    public function getRecharges(Request $request)
    {
        $search = $request->search;
        $order = $request->order == 'asc' ? 'asc' : 'desc';
        $sort_by = $request->sort_by ? $request->sort_by : 'id';

        $recharges = Recharge::where('user_id', $request->user->id)
            ->where('domain', $request->getHost())
            ->where(function ($query) use ($search) {
                if ($search) {
                    return $query->where('order_code', 'like', '%' . $search . '%')->orWhere('bank_name', 'like', '%' . $search . '%')->orWhere('amount', 'like', '%' . $search . '%');
                }
            })
            ->orderBy($sort_by, $order)
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Lấy thông tin thành công",
            'data' => $recharges
        ]);
    }

    public function getTransaction(Request $request)
    {
        $search = $request->search;
        $order = $request->order ? 'asc' : 'desc';
        $sort_by = $request->sort_by ? $request->sort_by : 'id';

        $transactions = Transaction::where('user_id', $request->user->id)
            ->where('domain', $request->getHost())
            ->where(function ($query) use ($search) {
                if ($search) {
                    return $query->where('tran_code', 'like', '%' . $search . '%')->orWhere('first_balance', 'like', '%' . $search . '%')->orWhere('before_balance', 'like', '%' . $search . '%')
                        ->orWhere('after_balance', 'like', '%' . $search . '%')->orWhere('note', 'like', '%' . $search . '%');
                }
            })
            ->orderBy($sort_by, $order)
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Lấy thông tin thành công",
            'data' => $transactions
        ]);
    }

    public function getOrders(Request $recharges)
    {
        $platform = $recharges->platform;
        $service = $recharges->service;
        $search = $recharges->search;
        $order = $recharges->order == 'asc' ? 'asc' : 'desc';
        $sort_by = $recharges->sort_by ? $recharges->sort_by : 'id';

        if (!$platform || !$service) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => "Thiếu thông số"
            ]);
        }

        $platformS = ServicePlatform::where('slug', $platform)->where('domain', env('APP_MAIN_SITE'))->first();
        $serviceS = $platformS->services()->where('slug', $service)->first();

        $orders = Order::where('user_id', $recharges->user->id)
            ->where('domain', $recharges->getHost())
            ->where('service_id', $serviceS->id)
            ->when(function ($query) use ($search) {
                if ($search) {
                    return $query->where('order_code', 'like', '%' . $search . '%')->orWhere('note', 'like', '%' . $search . '%');
                }
            })
            ->orderBy($sort_by, $order)
            ->paginate(10);

        $orders->map(function ($order) {
            $order->service = $order->service;
            // $order->server = $order->server;
            $order->action = $order->server->actions->first();
            $order->data = json_decode($order->order_data);
            return $order;
        });

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => "Lấy thông tin thành công",
            'data' => $orders
        ]);
    }

    public function refreshApiToken(Request $request)
    {
        $user = User::where('username', $request->user->username)->where('domain', $request->getHost())->where('status', 'active')->first();
        if ($user) {
            $user->api_token = encrypt($user->username . time());
            $user->save();

            UserActivity::create([
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'activity' => 'auth',
                'note' => 'Cập nhật API Token mới, địa chỉ IP: ' . $request->ip(),
                'domain' => request()->getHost(),
            ]);

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => "Cập nhật API Token thành công!",
                'api_token' => $user->api_token
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => "Tài khoản không tồn tại"
            ]);
        }
    }

    public function createBill(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'bank_name' => 'required',
            'amount' => 'required|min:10000|numeric',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => $valid->errors()->first()
            ], 400);
        } else {

            $bank = Banking::where('bank_name', $request->bank_name)->where('domain', $request->getHost())->first();
            if ($bank) {
                $code = mt_rand(100000, 999999);
                $bill = new Recharge();
                $bill->user_id = $request->user->id;
                $bill->order_code = $code;
                $bill->type = 'bill';
                $bill->payment_method = $request->bank_name;
                $bill->bank_name = $request->bank_name;
                $bill->bank_code = null;
                $bill->amount = $request->amount;
                $bill->real_amount = 0;
                $bill->status = 'Pending';
                $bill->note = null;
                $bill->domain = $request->getHost();
                $bill->expired_at = now()->addMinutes(30);
                $bill->save();

                return response()->json([
                    'status' => 'success',
                    'code' => 201,
                    'message' => "Tạo hóa đơn thành công",
                    'data' => $bill
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Ngân hàng không tồn tại"
                ], 400);
            }
        }
    }

    public function paymentBill(Request $request, $code)
    {
        $bill = Recharge::where('order_code', $code)->where('user_id', $request->user->id)->where('domain', $request->getHost())->first();
        if ($bill) {
            if ($bill->status == 'Success') {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Hóa đơn đã được thanh toán"
                ], 400);
            }

            if ($bill->status == 'Failed') {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Hóa đơn đã bị hủy"
                ], 400);
            }

            if ($bill->expired_at < now()) {
                $bill->status = 'Failed';
                $bill->save();
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Hóa đơn đã hết hạn"
                ], 400);
            }

            $bank = Banking::where('bank_name', $bill->bank_name)->where('domain', $request->getHost())->first();

            if (!$bank) {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => "Ngân hàng không tồn tại"
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => "Lấy thông tin hóa đơn thành công",
                'data' => $bill,
                'bank' => BankResource::make($bank, $bill->amounter)
            ], 201);
        } else {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => "Hóa đơn không tồn tại"
            ], 400);
        }
    }

    public function checkPayment(Request $request, $code)
    {
        $bill = Recharge::where('order_code', $code)->where('user_id', $request->user->id)->where('domain', request()->getHost())->first();
        if ($bill) {

            if ($bill->expired_at < now()) {
                $bill->status = 'Failed';
                $bill->save();
                return response()->json(['status' => 'error', 'message' => 'Thanh toán thất bại hoặc hết hạn'], 400);
            }
            if ($bill->status == 'Success' && $bill->is_read !== 1) {
                $bill->is_read = 1;
                $bill->save();
                return response()->json(['status' => 'success', 'message' => 'Thanh toán thành công'], 201);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Thanh toán chưa được xác nhận'], 201);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy hoá đơn'], 400);
        }
    }

    public function getWebsite(Request $request)
    {
        $website = PartnerWebsite::where('user_id', $request->user->id)->where('domain', request()->getHost())->first();
        if ($website) {
            return response()->json([
                'status' => 'success',
                'code' => 201,
                'message' => "Lấy thông tin website thành công",
                'site' => $website->name
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => "Lấy dữ liệu thành công",
                'site' => ''
            ], 201);
        }
    }

    public function updateWebsite(Request $request)
    {

        if ($request->user->level == 'member') {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => "Cấp bậc của bạn phải từ cộng tác viên trở lên!"
            ], 400);
        }

        $website = PartnerWebsite::where('name', $request->domain)->where('user_id', '!=', $request->user->id)->first();

        if ($website) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => "Tên miền đã tồn tại trong hệ thống!"
            ], 400);
        } else {
            $website = PartnerWebsite::where('user_id', $request->user->id)->where('domain', request()->getHost())->first();

            if (!$website) {
                $website = new PartnerWebsite();
                $website->user_id = $request->user->id;
                $website->name = $request->domain;
                $website->url = 'https://' . $request->domain;
                $website->status = 'pending';
                $website->is_domain = site('is_domain') ?? env('APP_MAIN_SITE');
                $website->domain = request()->getHost();

                $cld = new CloudflareController();
                $add = $cld->addDomain($request->domain);
                if ($add['status'] == 'success') {
                    $zone_id = $add['data']['zone_id'];
                    $zone_status = $add['data']['zone_status'];
                    $zone_name = $add['data']['zone_name'];
                    $website->zone_data = json_encode($add['data']);
                    $website->zone_name = $zone_name;
                    $website->zone_id = $zone_id;
                    $website->zone_status = $zone_status;
                    $website->save();

                    $cpanel = new CpanelController();
                    $cpanel->createDomain($request->domain);
                    return response()->json([
                        'status' => 'success',
                        'code' => 201,
                        'message' => "Tạo website thành công"
                    ], 201);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'code' => 400,
                        'message' => $add['message']
                    ], 400);
                }
            }

            if ($website->name != $request->domain) {
                $cld = new CloudflareController();
                $add = $cld->addDomain($request->domain);
                if ($add['status'] === 'success') {
                    $cld->deleteDomain($website->zone_id);
                    $zone_id = $add['data']['zone_id'];
                    $zone_status = $add['data']['zone_status'];
                    $zone_name = $add['data']['zone_name'];
                    $website->name = $request->domain;
                    $website->url = 'https://' . $request->domain;
                    $website->zone_data = json_encode($add['data']);
                    $website->zone_name = $zone_name;
                    $website->zone_id = $zone_id;
                    $website->zone_status = $zone_status;
                    $website->name = $request->domain;
                    $website->status = 'pending';
                    $website->save();

                    $cpanel = new CpanelController();
                    $cpanel->deleteDomain($website->domain);
                    $cpanel->createDomain($request->domain);
                    return response()->json([
                        'status' => 'success',
                        'code' => 201,
                        'message' => "Tạo website thành công"
                    ], 201);
                } else {
                    return  response()->json([
                        'status' => 'error',
                        'code' => 400,
                        'message' => $add['message']
                    ], 400);
                }
            }
        }
    }
}
