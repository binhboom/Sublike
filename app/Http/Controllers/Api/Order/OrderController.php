<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Api\Service\BaostarController;
use App\Http\Controllers\Api\Service\BoosterviewsController;
use App\Http\Controllers\Api\Service\SmmFollowsController;
use App\Http\Controllers\Api\Service\CheoTuongTacController;
use App\Http\Controllers\Api\Service\CongLikeController;
use App\Http\Controllers\Api\Service\Hacklike17Controller;
use App\Http\Controllers\Api\Service\SmmKingController;
use App\Http\Controllers\Api\Service\SubgiareController;
use App\Http\Controllers\Api\Service\TraodoisubController;
use App\Http\Controllers\Api\Service\TrumsubreController;
use App\Http\Controllers\Api\Service\TuongTacSaleController;
use App\Http\Controllers\Api\Service\SmmgenController;
use App\Http\Controllers\Api\Service\SmmcoderController;
use App\Http\Controllers\Api\Service\AutolikezController;
use App\Http\Controllers\Api\Service\SmmCustomController;
use App\Http\Controllers\Api\Service\TwoMxhController;
use App\Http\Controllers\Controller;
use App\Library\TelegramSdk;
use App\Models\Order;
use App\Models\ServerAction;
use App\Models\Service;
use App\Models\ServiceServer;
use App\Models\SmmPanelPartner;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function createOrder(Request $request)
    {
        try {

            $api_token = $request->header('X-Access-Token');

            if (!$api_token) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Không tìm thấy Bạn chưa đăng nhập !',
                ], 401);
            }

            $domain = $request->getHost();
            $user = User::where('api_token', $api_token)->where('domain', $domain)->first();

            if (!$user) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'X-Access-Token không hợp lệ 1!',
                    'data' => [
                        'domain' => $domain,
                        'api_token' => $api_token,
                    ],
                ], 401);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Tài khoản của bạn hiện tại không được phép thực hiện hành động này !',
                ], 401);
            }

            $valid = Validator::make($request->all(), [
                'provider_package' => 'required',
                'provider_server' => 'required',
            ], [
                'provider_package.required' => 'Không tìm thấy gói cần mua !',
                'provider_server.required' => 'Vui lòng chọn server cần mua !',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'code' => '400',
                    'status' => 'error',
                    'message' => $valid->errors()->first(),
                ], 400);
            }

            if ($domain === env('APP_MAIN_SITE')) {

                $service = Service::where('package', $request->provider_package)->where('domain', env('APP_MAIN_SITE'))->first();

                if (!$service) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Gói cần mua không tồn tại !',
                    ], 400);
                }

                if ($service->status !== 'active') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Gói cần mua hiện không khả dụng !',
                    ], 400);
                }

                $provider_server = str_replace('sv-', '', $request->provider_server);

                $server = ServiceServer::where('service_id', $service->id)->where('package_id', $provider_server)->where('domain', $domain)->first();

                if (!$server) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Máy chủ này không tồn tại !',
                    ], 400);
                }

                if ($server->visibility !== 'public') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Máy chủ này không khả dụng !',
                    ], 400);
                }

                if ($server->status !== 'active') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Máy chủ này hiện đang bảo trì !',
                    ], 400);
                }

                $validService = Validator::make($request->all(), [
                    'object_id' => 'required',
                ], [
                    'object_id.required' => 'Vui lòng nhập UID hoặc Link cần mua !',
                ]);

                if ($validService->fails()) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => $validService->errors()->first(),
                    ], 400);
                }

                $serverAction = ServerAction::where('server_id', $server->id)->first();

                if (!$serverAction) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Máy chủ này hiện đang bị lỗi vui lòng thử lại sau !',
                    ], 400);
                }

                if ($serverAction->quantity_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'quantity' => 'required|integer'
                    ], [
                        'quantity.required' => 'Vui lòng chọn số lượng cần mua !',
                        'quantity.integer' => 'Số lượng cần mua phải là số !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                }

                if ($serverAction->reaction_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'reaction' => 'required'
                    ], [
                        'reaction.required' => 'Vui lòng cảm xúc !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                }

                if ($serverAction->comments_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'comments' => 'required'
                    ], [
                        'comments.required' => 'Vui lòng nhập nội dung bình luận !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }

                    $count = 0;
                    $comments = explode("\n", $request->comments);
                    $comments = array_filter($comments, 'trim');
                    $comments = array_values($comments);
                    $count = count($comments);
                    $request->merge(['quantity' => $count]);
                }

                if ($serverAction->minutes_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'minutes' => 'required|integer'
                    ], [
                        'minutes.required' => 'Vui lòng chọn Số Phút cần mua !',
                        'minutes.integer' => 'Số Phút cần mua phải là số !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                }

                if ($serverAction->posts_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'posts' => 'required'
                    ], [
                        'posts.required' => 'Vui lòng chọn số bài viết cần mua !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }

                    $newPost = $request->posts == 'unlimited' ? 1 : $request->posts;
                    $request->merge(['posts' => $newPost]);
                }

                if ($serverAction->time_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'duration' => 'required|integer'
                    ], [
                        'duration.required' => 'Vui lòng chọn số bài viết cần mua !',
                        'duration.integer' => 'Số bài viết cần mua phải là số !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                }

                if ($server->limit_day !== 0) {
                    $orderToday = Order::where('server_id', $server->id)->whereDate('created_at', Carbon::today())->count();

                    if ($orderToday >= $request->quantity) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Máy chủ này đã đạt giới hạn mua hàng trong ngày !',
                        ], 400);
                    }
                }

                if ($request->quantity < $server->min) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Số lượng cần mua phải lớn hơn hoặc bằng ' . $server->min . ' !',
                    ], 400);
                }

                if ($request->quantity > $server->max) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Số lượng cần mua phải nhỏ hơn hoặc bằng ' . $server->max . ' !',
                    ], 400);
                }

                $price = $server->levelPrice($user->level);

                $total = $price * $request->quantity;

                if ($serverAction->time_status === 'on') {
                    $total = $total * $request->duration;
                }

                if ($serverAction->minutes_status === 'on') {
                    $total = $total * $request->minutes;
                }

                if ($serverAction->posts_status === 'on') {
                    $total = $total * $request->posts;
                }

                if ($user->balance < ceil($total)) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Số dư của bạn không đủ để thực hiện giao dịch này !',
                    ], 400);
                }


                if ($server->providerName == 'subgiare') {
                    $sgr = new SubgiareController();
                    $sgr->path = $server->providerLink;
                    $sgr->data = [
                        'object_id' => $request->object_id,
                        'server_order' => $server->providerServer,
                        'quantity' => $request->quantity,
                        'reaction' => $request->reaction,
                        'speed' => 'fast',
                        'comment' => $request->comments,
                        'minutes' => $request->minutes,
                        'time' => $request->time,
                        'days' => $request->duration,
                        'post' => $request->posts,
                    ];

                    $result = $sgr->createOrder();
                    if (isset($result) && $result['status'] === true) {
                        $orderID = $result['data']['code_order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == '2mxh') {
                    $twoMXH = new TwoMxhController();
                    $twoMXH->path = $server->providerLink;
                    $twoMXH->data = [
                        'object_id' => $request->object_id,
                        'quantity' => $request->quantity,
                        'reaction' => $request->reaction,
                        'comment' => $request->comments,
                        'minutes' => $request->minutes,
                        'time' => $request->time,
                        'duration' => $request->duration,
                        'post' => $request->posts,
                        'server_order' => $server->providerServer,
                        'num_post'
                    ];

                    $result = $twoMXH->CreateOrder();
                    if (isset($result) && $result['status'] == true) {
                        $orderID = $result['data']['order']['order_id'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'baostar') {
                    $baoStar = new BaostarController();
                    $baoStar->path = $server->providerLink;
                    $baoStar->data = [
                        'object_id' => $request->object_id,
                        'quantity' => $request->quantity,
                        'object_type' => $request->reaction,
                        'package_name' => $server->providerServer,
                        'list_message' => $request->comments,
                        'num_minutes' => $request->minutes,
                        'num_day' => $request->duration,
                        'slbv' => $request->posts,
                    ];

                    $result = $baoStar->createOrder();
                    if (isset($result) && $result['status'] == true) {
                        $orderID = $result['data']['code_order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'boosterviews') {
                    $boosterviews = new BoosterviewsController();

                    $result = $boosterviews->order([
                        'service' => $server->providerServer,
                        'link' => $request->object_id,
                        'quantity' => $request->quantity,
                        'comments' => $request->comments,
                    ]);

                    if (isset($result) && $result['order']) {
                        $orderID = $result['order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'cheotuongtac') {
                    $cheotuongtac = new CheoTuongTacController();

                    $result = $cheotuongtac->order([
                        'service' => $server->providerServer,
                        'link' => $request->object_id,
                        'quantity' => $request->quantity,
                        'comments' => $request->comments,
                    ]);

                    // dd($result);
                    if (isset($result) && isset($result['order'])) {
                        $orderID = $result['order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['error'] ?? $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'smmking') {
                    $smmking = new SmmKingController();

                    $result = $smmking->order([
                        'service' => $server->providerServer,
                        'link' => $request->object_id,
                        'quantity' => $request->quantity,
                        'comments' => $request->comments,
                    ]);

                    if (isset($result) && $result['order']) {
                        $orderID = $result['order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'tuongtacsale') {
                    $tuongtacsale = new TuongTacSaleController();

                    $result = $tuongtacsale->order([
                        'service' => $server->providerServer,
                        'link' => $request->object_id,
                        'quantity' => $request->quantity,
                        'comments' => $request->comments,
                    ]);

                    if (isset($result) && $result['order']) {
                        $orderID = $result['order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'hacklike17') {

                    $hacklike17 = new Hacklike17Controller();
                    $hacklike17->data = [
                        'uid' => $request->object_id,
                        'link' => $request->object_id,
                        'count' => $request->quantity,
                        'server' => $server->providerServer,
                        'reaction' => $request->reaction,
                        'speed' => '0',
                        'speed_server_2' => 'default',
                        'list_comment' => $request->comments,
                        'comments' => $request->comments,
                        'content' => $request->comments,
                        'type_view' => 0,
                        'minutes' => $request->minutes,
                        'days' => $request->duration,
                        'note' => $request->note ?? '',
                    ];

                    $result = $hacklike17->order($server->providerLink);
                    // dd($result);

                    if (isset($result) && $result['status'] == 1) {
                        $orderID = $result['order_id'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['msg'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'conglike') {
                    $conglike = new CongLikeController();
                    $conglike->data = [
                        'post_id' => $request->object_id,
                        'page_id' => $request->object_id,
                        'soluong' => $request->quantity,
                        'num_package' => $request->duration,
                        'package_id' => $server->providerServer,
                    ];
                    $result = $conglike->order($server->providerLink);
                    if (isset($result) && $result['code'] == 100) {
                        $orderID = $request->object_id;
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'trumsubre') {
                    $sgr = new TrumsubreController();
                    $sgr->path = $server->providerLink;
                    $sgr->data = [
                        'object_id' => $request->object_id,
                        'server_order' => $server->providerServer,
                        'quantity' => $request->quantity,
                        'reaction' => $request->reaction,
                        'speed' => 'fast',
                        'comment' => $request->comments,
                        'minutes' => $request->minutes,
                        'time' => $request->time,
                        'days' => $request->duration,
                        'post' => $request->posts,
                    ];

                    $result = $sgr->createOrder();
                    if (isset($result) && $result['status'] === true) {
                        $orderID = $result['data']['code_order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'smmgen') {
                    $boosterviews = new SmmgenController();

                    $result = $boosterviews->order([
                        'service' => $server->providerServer,
                        'link' => $request->object_id,
                        'quantity' => $request->quantity,
                        'comments' => $request->comments,
                    ]);

                    if (isset($result) && $result['order']) {
                        $orderID = $result['order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'smmcoder') {
                    $boosterviews = new SmmcoderController();

                    $result = $boosterviews->order([
                        'service' => $server->providerServer,
                        'link' => $request->object_id,
                        'quantity' => $request->quantity,
                        'comments' => $request->comments,
                    ]);

                    if (isset($result) && $result['order']) {
                        $orderID = $result['order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'traodoisub') {
                    $orderCode = 'OD_' . time() . rand(1000, 9999);
                    $tds = new TraodoisubController();
                    $tds->path = $server->providerLink;

                    $tds->data = [
                        'id' => $request->object_id,
                        'sl' => $request->quantity,
                        'is_album' => 'not',
                        'speed' => '1',
                        'post' => $request->posts,
                        'time_pack' => $request->duration,
                        'packet' => $server->quantity,
                        'dateTime' => Carbon::now()->format('Y-m-d H:i:s'),
                        'noidung' => json_encode(explode(PHP_EOL, $request->comments)),
                        'maghinho' => $orderCode,
                        'loaicx' => strtoupper($request->reaction),
                        'timeLive' => $request->minutes,
                        'slMat' => $request->quantity,
                        'idvip' => $request->object_id,
                    ];

                    $result = $tds->order();
                    // printf($result);
                    // die();
                    if ($result === 'Mua thành công!' || $result == 'Thành Công!') {
                        $orderID =  time() . rand(1000, 9999);
                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        // echo($result);
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result ?? "Có lỗi xảy ra với máy chủ này vui lòng thử lại sau!",
                        ], 400);
                    }
                } elseif ($server->providerName == 'smmfollows') {
                    $smmfl = new SmmFollowsController();

                    $result = $smmfl->order([
                        'service' => $server->providerServer,
                        'link' => $request->object_id,
                        'quantity' => $request->quantity,
                        'comments' => $request->comments,
                    ]);

                    if (isset($result) && $result['order']) {
                        $orderID = $result['order'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'autolikez') {
                    $smmfl = new AutolikezController();
                    $smmfl->path = $server->providerLink;
                    $smmfl->data = [
                        'server_order' => $server->providerServer,
                        'object_id' => $request->object_id,
                        'quantity' => $request->quantity,
                        'object_type' => $request->reaction,
                        'num_minutes' => $request->minutes,
                        'list_messages' => $request->comments,
                        'month' => $request->duration,
                        'num_minutes' => $request->minutes,
                    ];

                    $result = $smmfl->createOrder();

                    if ($result instanceof \Illuminate\Http\JsonResponse) {
                        $result = $result->getData(true);
                    }

                    if ($result['status'] == 200 || isset($result['success']) == true) {
                        $orderID = $result['data']['id'];
                        $orderCode = 'INV_24' . rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            'posts' => $request->posts,
                            'duration' => $request->duration,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                } elseif ($server->providerName == 'codedynamic') {
                    $orderID =  time() . rand(1000, 9999);
                    $orderCode = 'INV_24' . rand(1000000, 9999999);

                    $orderData = [
                        "user_id" => $user->id,
                        "service_id" => $service->id,
                        "server_id" => $server->id,
                        "order_code" => $orderCode,
                        "object_id" => $request->object_id,
                        "quantity" => $request->quantity,
                        "reaction" => $request->reaction,
                        "comments" => htmlentities($request->comments),
                        "minutes" => $request->minutes,
                        'posts' => $request->posts,
                        'duration' => $request->duration,
                        "price" => $price,
                        'payment' => $total,
                        'note' => $request->note,
                    ];
                } else {

                    $smmChecker = SmmPanelPartner::where('name', $server->providerName)->first();
                    if ($smmChecker) {
                        $smm = new SmmCustomController();
                        $smm->api_url = $smmChecker->url_api;
                        $smm->api_key = $smmChecker->api_token;

                        $result = $smm->order([
                            'service' => $server->providerServer,
                            'link' => $request->object_id,
                            'quantity' => $request->quantity,
                            'comments' => $request->comments,
                        ]);

                        if (isset($result) && $result['order']) {
                            $orderID = $result['order'];
                            $orderCode = 'INV_24' . rand(1000000, 9999999);
                            $orderData = [
                                "user_id" => $user->id,
                                "service_id" => $service->id,
                                "server_id" => $server->id,
                                "order_code" => $orderCode,
                                "object_id" => $request->object_id,
                                "quantity" => $request->quantity,
                                "reaction" => $request->reaction,
                                "comments" => htmlentities($request->comments),
                                "minutes" => $request->minutes,
                                'posts' => $request->posts,
                                'duration' => $request->duration,
                                "price" => $price,
                                'payment' => $total,
                                'note' => $request->note,
                            ];
                        } else {
                            return response()->json([
                                'code' => '400',
                                'status' => 'error',
                                'message' => $result['message'],
                            ], 400);
                        }
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Hệ thống không hỗ trợ dịch vụ này vui lòng thử lại!',
                        ], 400);
                    }

                    // return response()->json([
                    //     'code' => '400',
                    //     'status' => 'error',
                    //     'message' => 'Hệ thống không hỗ trợ dịch vụ này !',
                    // ], 400);
                }

                $order = new Order();
                $order->user_id = $user->id;
                $order->service_id = $service->id;
                $order->server_id = $server->id;
                $order->orderProviderName = $server->providerName;
                $order->orderProviderServer = $server->providerServer;
                $order->order_package = $service->package;
                $order->object_server = $request->provider_server;
                $order->object_id = $request->object_id;
                $order->order_id = $orderID;
                $order->order_code = $orderCode;
                $order->order_data = json_encode($orderData);
                $order->start = 0;
                $order->buff = 0;
                $order->duration = $request->duration;
                $order->posts = 0;
                $order->remaining = $request->duration;
                $order->price = $price;
                $order->payment = $total;
                $order->status = 'Processing';
                $order->ip = $request->ip();
                $order->note = $request->note;
                $order->time = now();
                $order->domain = $domain;
                $order->save();

                if ($order) {

                    // nếu số dư của user nhỏ hơn total thì block user
                    if ($user->balance < $total) {
                        $user->status = 'banned';
                        $user->save();
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Tài khoản của bạn đã bị khoá do thực hiện giao dịch không hợp lệ !',
                        ], 400);
                    }

                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->tran_code = $orderCode;
                    $transaction->type = 'order';
                    $transaction->action = 'sub';
                    $transaction->first_balance = $total;
                    $transaction->before_balance = $user->balance;
                    $transaction->after_balance = $user->balance - $total;
                    $transaction->note = 'Thanh toán đơn hàng ' . $orderCode;
                    $transaction->ip = $request->ip();
                    $transaction->domain = $domain;
                    $transaction->save();

                    $user->balance = $user->balance - $total;
                    $user->save();

                    if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                        $bot_notify = new TelegramSdk();
                        $bot_notify->botNotify()->sendMessage([
                            'chat_id' => siteValue('telegram_chat_id'),
                            'text' => '🛒 <b>Đơn hàng mới được tạo từ website ' . $domain . ' !' . "</b>\n\n" .
                                '👤 <b>Khách hàng:</b> ' . $user->name . ' (' . $user->email . ')' . "\n" .
                                '📦 <b>Gói dịch vụ:</b> ' . $service->package . "\n" .
                                '🔗 <b>Link hoặc UID:</b> ' . $request->object_id . "\n" .
                                '🔢 <b>Số lượng:</b> ' . number_format($request->quantity) . "\n" .
                                '🔗 <b>Máy chủ:</b> ' . $server->package_id . "\n" .
                                '💰 <b>Giá tiền:</b> ' . $price . 'đ' . "\n" .
                                '💵 <b>Thanh toán:</b> ' . $total . 'đ' . "\n" .
                                '📝 <b>Ghi chú:</b> ' . $request->note . "\n",
                            'parse_mode' => 'HTML',
                        ]);
                    }

                    if ($user->telegram_id !== null && $user->telegram_id !== '' && $user->notification_telegram == 'yes') {
                        $bot_notify = new TelegramSdk();
                        $bot_notify->botChat()->sendMessage([
                            'chat_id' => $user->telegram_id,
                            'text' => '🛒 <b>Bạn vừa tạo đơn hàng mới từ website ' . $domain . ' !' . "</b>\n\n" .
                                '📦 <b>Gói dịch vụ:</b> ' . $service->platform->name . " - " . $service->name .
                                '🔗 <b>Link hoặc UID:</b> ' . $request->object_id . "\n" .
                                '🔢 <b>Số lượng:</b> ' . number_format($request->quantity) . "\n" .
                                '🔗 <b>Máy chủ:</b> ' . $server->package_id . "\n" .
                                '💰 <b>Giá tiền:</b> ' . $price . 'đ' . "\n" .
                                '💵 <b>Thanh toán:</b> ' . $total . 'đ' . "\n" .
                                '📝 <b>Ghi chú:</b> ' . $request->note . "\n",
                            'parse_mode' => 'HTML',
                        ]);
                    }

                    return response()->json([
                        'code' => '200',
                        'status' => 'success',
                        'message' => 'Đơn hàng của bạn đã được tạo thành công !',
                        'data' => [
                            'id' => $order->id,
                            'order_code' => $orderCode,
                            'price' => $price,
                            'payment' => $total,
                            'status' => 'Processing',
                        ],
                    ], 200);
                }
            } else {

                $service = Service::where('package', $request->provider_package)->where('domain', env('APP_MAIN_SITE'))->first();

                if (!$service) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Gói cần mua không tồn tại !',
                    ], 400);
                }

                if ($service->status !== 'active') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Gói cần mua hiện không khả dụng !',
                    ], 400);
                }

                $provider_server = str_replace('sv-', '', $request->provider_server);

                $server = ServiceServer::where('service_id', $service->id)->where('package_id', $provider_server)->where('domain', $domain)->first();

                if (!$server) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Máy chủ này không tồn tại !',
                    ], 400);
                }

                if ($server->visibility !== 'public') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Máy chủ này không khả dụng !',
                    ], 400);
                }

                if ($server->status !== 'active') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Máy chủ này hiện đang bảo trì !',
                    ], 400);
                }

                $validService = Validator::make($request->all(), [
                    'object_id' => 'required',
                ], [
                    'object_id.required' => 'Vui lòng nhập UID hoặc Link cần mua !',
                ]);

                if ($validService->fails()) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => $validService->errors()->first(),
                    ], 400);
                }

                $serverAction = ServerAction::where('server_id', $server->id)->first();

                if (!$serverAction) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Máy chủ này hiện đang bị lỗi vui lòng thử lại sau !',
                    ], 400);
                }

                if ($serverAction->quantity_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'quantity' => 'required|integer'
                    ], [
                        'quantity.required' => 'Vui lòng chọn số lượng cần mua !',
                        'quantity.integer' => 'Số lượng cần mua phải là số !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                }

                if ($serverAction->reaction_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'reaction' => 'required'
                    ], [
                        'reaction.required' => 'Vui lòng cảm xúc !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                }

                if ($serverAction->comments_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'comments' => 'required'
                    ], [
                        'comments.required' => 'Vui lòng nhập nội dung bình luận !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }

                    $count = 0;
                    $comments = explode("\n", $request->comments);
                    $comments = array_filter($comments, 'trim');
                    $comments = array_values($comments);
                    $count = count($comments);
                    $request->merge(['quantity' => $count]);
                }

                if ($serverAction->minutes_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'minutes' => 'required|integer'
                    ], [
                        'minutes.required' => 'Vui lòng chọn Số Phút cần mua !',
                        'minutes.integer' => 'Số Phút cần mua phải là số !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                }

                if ($serverAction->posts_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'posts' => 'required'
                    ], [
                        'posts.required' => 'Vui lòng chọn số bài viết cần mua !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                    $newPost = $request->posts == 'unlimited' ? 1 : $request->posts;
                    $request->merge(['posts' => $newPost]);
                }

                if ($serverAction->time_status === 'on') {
                    $valid = Validator::make($request->all(), [
                        'duration' => 'required|integer'
                    ], [
                        'duration.required' => 'Vui lòng chọn số bài viết cần mua !',
                        'duration.integer' => 'Số bài viết cần mua phải là số !',
                    ]);

                    if ($valid->fails()) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $valid->errors()->first(),
                        ], 400);
                    }
                }

                if ($server->limit_day !== 0) {
                    $orderToday = Order::where('server_id', $server->id)->whereDate('created_at', Carbon::today())->count();

                    if ($orderToday >= $request->quantity) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Máy chủ này đã đạt giới hạn mua hàng trong ngày !',
                        ], 400);
                    }
                }

                if ($request->quantity < $server->min) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Số lượng cần mua phải lớn hơn hoặc bằng ' . $server->min . ' !',
                    ], 400);
                }

                if ($request->quantity > $server->max) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Số lượng cần mua phải nhỏ hơn hoặc bằng ' . $server->max . ' !',
                    ], 400);
                }

                $price = $server->levelPrice($user->level);

                if ($serverAction->time_status === 'on') {
                    $total = $price * $request->quantity * $request->duration;
                } elseif ($serverAction->posts_status === 'on') {
                    $posts = $request->posts == 'unlimited' ? 1 : $request->posts;
                    $total = $price * $request->quantity * $posts;
                } elseif ($serverAction->time_status === 'on' && $serverAction->posts_status === 'on') {
                    $posts = $request->posts == 'unlimited' ? 1 : $request->posts;
                    $total = $price * $request->quantity * $request->duration * $posts;
                } else {
                    $total = $price * $request->quantity;
                }


                if ($user->balance < ceil($total)) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Số dư của bạn không đủ để thực hiện giao dịch này !',
                    ], 400);
                }

                /* if ($server->providerName == 'subgiare') {
                    $sgr = new SubgiareController();
                    $sgr->path = $server->providerLink;
                    $sgr->data = [
                        'object_id' => $request->object_id,
                        'server_order' => $server->providerServer,
                        'quantity' => $request->quantity,
                        'reaction' => $request->reaction,
                        'speed' => 'fast',
                        'comment' => $request->comments,
                        'minutes' => $request->minutes,
                        'time' => $request->time,
                        'days' => $request->duration,
                        'post' => $request->posts,
                    ];

                    $result = $sgr->createOrder();
                    if (isset($result) && $result['status'] === true) {
                        $orderID = $result['data']['code_order'];
                        $orderCode = 'INV_24'. rand(1000000, 9999999);

                        $orderData = [
                            "user_id" => $user->id,
                            "service_id" => $service->id,
                            "server_id" => $server->id,
                            "order_code" => $orderCode,
                            "object_id" => $request->object_id,
                            "quantity" => $request->quantity,
                            "reaction" => $request->reaction,
                            "comments" => htmlentities($request->comments),
                            "minutes" => $request->minutes,
                            "price" => $price,
                            'payment' => $total,
                            'note' => $request->note,
                        ];
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => $result['message'],
                        ], 400);
                    }
                }  */

                $admin = User::where('username', site('admin_username'))->where('domain', site('is_domain'))->first();
                if (!$admin) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy tài khoản admin !',
                    ], 400);
                }

                $urlOrder = "https://" . site('is_domain') . "/api/v1/start/create/order";

                $dataSend = array(
                    'provider_package' => $request->provider_package,
                    'provider_server' => $request->provider_server,
                    'object_id' => $request->object_id,
                    'quantity' => $request->quantity,
                    'reaction' => $request->reaction,
                    'comments' => $request->comments,
                    'minutes' => $request->minutes,
                    'posts' => $request->posts,
                    'duration' => $request->duration,
                    'note' => $request->getHost() . ' - Khởi tạo đơn hàng từ API',
                );

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $urlOrder,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($dataSend),
                    CURLOPT_HTTPHEADER => array(
                        'X-Access-Token: ' . $admin->api_token,
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                $result = json_decode($response, true);
                if (isset($result) && $result['status'] == 'success') {
                    $orderID = $result['data']['id'];
                    $orderCode = 'INV_24' . rand(1000000, 9999999);

                    $orderData = [
                        "user_id" => $user->id,
                        "service_id" => $service->id,
                        "server_id" => $server->id,
                        "order_code" => $orderCode,
                        "object_id" => $request->object_id,
                        "quantity" => $request->quantity,
                        "reaction" => $request->reaction,
                        "comments" => htmlentities($request->comments),
                        "minutes" => $request->minutes,
                        "price" => $price,
                        'payment' => $total,
                        'note' => $request->note,
                    ];

                    $order = new Order();
                    $order->user_id = $user->id;
                    $order->service_id = $service->id;
                    $order->server_id = $server->id;
                    $order->orderProviderName = $server->providerName;
                    $order->orderProviderServer = $server->providerServer;
                    $order->order_package = $service->package;
                    $order->object_server = $request->provider_server;
                    $order->object_id = $request->object_id;
                    $order->order_id = $orderID;
                    $order->order_code = $orderCode;
                    $order->order_data = json_encode($orderData);
                    $order->start = 0;
                    $order->buff = 0;
                    $order->duration = $request->duration;
                    $order->remaining = $request->duration;
                    $order->posts = 0;
                    $order->price = $price;
                    $order->payment = $total;
                    $order->status = 'Processing';
                    $order->ip = $request->ip();
                    $order->note = $request->note;
                    $order->time = now();
                    $order->domain = $domain;
                    $order->save();

                    if ($order) {

                        // nếu số dư của user nhỏ hơn total thì block user
                        if ($user->balance < $total) {
                            $user->status = 'banned';
                            $user->save();
                            return response()->json([
                                'code' => '400',
                                'status' => 'error',
                                'message' => 'Tài khoản của bạn đã bị khoá do thực hiện giao dịch không hợp lệ !',
                            ], 400);
                        }

                        $transaction = new Transaction();
                        $transaction->user_id = $user->id;
                        $transaction->tran_code = $orderCode;
                        $transaction->type = 'order';
                        $transaction->action = 'sub';
                        $transaction->first_balance = $total;
                        $transaction->before_balance = $user->balance;
                        $transaction->after_balance = $user->balance - $total;
                        $transaction->note = 'Thanh toán đơn hàng ' . $orderCode;
                        $transaction->ip = $request->ip();
                        $transaction->domain = $domain;
                        $transaction->save();

                        $user->balance = $user->balance - $total;
                        $user->save();

                        if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                            $bot_notify = new TelegramSdk();
                            $bot_notify->botNotify()->sendMessage([
                                'chat_id' => site('telegram_chat_id'),
                                'text' => '🛒 <b>Bạn vừa tạo đơn hàng mới từ website ' . $domain . ' !' . "</b>\n\n" .
                                    '📦 <b>Gói dịch vụ:</b> ' . $service->platform->name . " - " . $service->name .
                                    '🔗 <b>Link hoặc UID:</b> ' . $request->object_id . "\n" .
                                    '🔢 <b>Số lượng:</b> ' . number_format($request->quantity) . "\n" .
                                    '🔗 <b>Máy chủ:</b> ' . $server->package_id . "\n" .
                                    '💰 <b>Giá tiền:</b> ' . $price . 'đ' . "\n" .
                                    '💵 <b>Thanh toán:</b> ' . $total . 'đ' . "\n" .
                                    '📝 <b>Ghi chú:</b> ' . $request->note . "\n",
                                'parse_mode' => 'HTML',
                            ]);
                        }

                        if ($user->telegram_id !== null && $user->telegram_id !== '' && $user->notification_telegram == 'yes') {
                            $bot_notify = new TelegramSdk();
                            $bot_notify->botChat()->sendMessage([
                                'chat_id' => $user->telegram_id,
                                'text' => '🛒 <b>Bạn vừa tạo đơn hàng mới từ website ' . $domain . ' !' . "</b>\n\n" .
                                    '📦 <b>Gói dịch vụ:</b> ' . $service->platform->name . " - " . $service->name .
                                    '🔗 <b>Link hoặc UID:</b> ' . $request->object_id . "\n" .
                                    '🔢 <b>Số lượng:</b> ' . number_format($request->quantity) . "\n" .
                                    '🔗 <b>Máy chủ:</b> ' . $server->package_id . "\n" .
                                    '💰 <b>Giá tiền:</b> ' . $price . 'đ' . "\n" .
                                    '💵 <b>Thanh toán:</b> ' . $total . 'đ' . "\n" .
                                    '📝 <b>Ghi chú:</b> ' . $request->note . "\n",
                                'parse_mode' => 'HTML',
                            ]);
                        }

                        return response()->json([
                            'code' => '200',
                            'status' => 'success',
                            'message' => 'Đơn hàng của bạn đã được tạo thành công !',
                            'data' => [
                                'id' => $order->id,
                                'order_code' => $orderCode,
                                'price' => $price,
                                'payment' => $total,
                                'status' => 'Processing',
                            ],
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => $result['message'] ?? "Tạo đơn hàng thất bại !",
                    ], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function refundOrder(Request $request)
    {
        try {
            $api_token = $request->header('X-Access-Token');

            if (!$api_token) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Không tìm thấy X-Access-Token !',
                ], 401);
            }

            $domain = $request->getHost();
            $user = User::where('api_token', $api_token)->where('domain', $domain)->first();

            if (!$user) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'X-Access-Token không hợp lệ !',
                ], 401);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Tài khoản của bạn hiện tại không được phép thực hiện hành động này !',
                ], 401);
            }

            $valid = Validator::make($request->all(), [
                'order_code' => 'required',
            ], [
                'order_code.required' => 'Vui lòng nhập mã đơn hàng cần hoàn tiền !',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'code' => '400',
                    'status' => 'error',
                    'message' => $valid->errors()->first(),
                ], 400);
            } else {
                $order = Order::where('order_code', $request->order_code)->where('user_id', $user->id)->where('domain', $domain)->first();
                if (!$order) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy đơn hàng cần hoàn tiền !',
                    ], 400);
                }

                if ($order->status === 'Refunded') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã được hoàn tiền trước đó !',
                    ], 400);
                }

                if ($order->status === 'WaitingForRefund') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đang chờ hoàn tiền !',
                    ], 400);
                }

                if ($order->status === 'Completed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã hoàn thành không thể hoàn tiền !',
                    ], 400);
                }

                if ($order->status === 'Cancelled') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã bị hủy không thể hoàn tiền !',
                    ], 400);
                }

                if ($order->status === 'Failed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã thất bại không thể hoàn tiền !',
                    ], 400);
                }

                if ($order->status === 'Partially Refunded') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã được hoàn tiền một phần không thể hoàn tiền !',
                    ], 400);
                }

                if ($order->status === 'Partially Completed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã hoàn thành một phần không thể hoàn tiền !',
                    ], 400);
                }

                $server = $order->server;
                if ($server) {
                    $action = $server->action;
                    if (!$action) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Không tìm thấy thông tin máy chủ !',
                        ], 400);
                    }

                    if ($action->refund_status !== 'on') {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Máy chủ này không hỗ trợ hoàn tiền !',
                        ], 400);
                    }

                    if ($server->providerName == '2mxh') {
                        $twoMXH = new TwoMxhController();
                        $twoMXH->path = $server->providerLink;
                        $result = $twoMXH->orderRefund($order->order_id);
                        if (isset($result) && $result['status'] === true) {
                            $order->status = 'WaitingForRefund';
                            $order->save();
                            return response()->json([
                                'code' => '200',
                                'status' => 'success',
                                'message' => 'Đơn hàng của bạn đã được đưa vào hàng chờ hoàn tiền !',
                            ], 200);
                        } else {
                            return response()->json([
                                'code' => '400',
                                'status' => 'error',
                                'message' => $result['message'],
                            ], 400);
                        }
                    } elseif ($server->providerName == 'hacklike17') {
                        $hacklike17 = new Hacklike17Controller();
                        $result = $hacklike17->refundOrder($order->order_id);
                        if (isset($result) && $result['status'] === true) {
                            $order->status = 'WaitingForRefund';
                            $order->save();
                            return response()->json([
                                'code' => '200',
                                'status' => 'success',
                                'message' => 'Đơn hàng của bạn đã được đưa vào hàng chờ hoàn tiền !',
                            ], 200);
                        } else {
                            return response()->json([
                                'code' => '400',
                                'status' => 'error',
                                'message' => $result['message'],
                            ], 400);
                        }
                    } elseif ($server->providerName == 'codedynamic') {

                        if ($server->action->time_status == 'on') {
                            $order->remaining = 0;
                            $order->status = 'Refunded';
                            $order->save();

                            // send notify telegram
                            if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                $bot_notify = new TelegramSdk();
                                $bot_notify->botNotify()->sendMessage([
                                    'chat_id' => siteValue('telegram_chat_id'),
                                    'text' => '🛒 <b>Đơn hàng đã được hoàn tiền từ website ' . $domain . ' !' . "</b>\n\n" .
                                        '👤 <b>Khách hàng:</b> ' . $user->name . ' (' . $user->email . ')' . "\n" .
                                        '📦 <b>Gói dịch vụ:</b> ' . $order->service->package . "\n" .
                                        '🔗 <b>Link hoặc UID:</b> ' . $order->object_id . "\n" .
                                        '🔢 <b>Số lượng:</b> ' . number_format($order->quantity) . "\n" .
                                        '🔗 <b>Máy chủ:</b> ' . $server->package_id . "\n" .
                                        '💰 <b>Giá tiền:</b> ' . $order->price . 'đ' . "\n" .
                                        '💵 <b>Thanh toán:</b> ' . $order->payment . 'đ' . "\n" .
                                        '📝 <b>Ghi chú:</b> ' . $order->note . "\n",
                                    'parse_mode' => 'HTML',
                                ]);
                            }


                            return response()->json([
                                'code' => '200',
                                'status' => 'success',
                                'message' => 'Đơn hàng của bạn đã được hoàn tiền !',
                            ], 200);
                        } else {
                            return response()->json([
                                'code' => '400',
                                'status' => 'error',
                                'message' => 'Hệ thống không hỗ trợ hoàn tiền cho dịch vụ này !',
                            ], 400);
                        }
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Hệ thống không hỗ trợ hoàn tiền cho dịch vụ này !',
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy máy chủ của đơn hàng này !',
                    ], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function warrantyOrder(Request $request)
    {
        try {
            $api_token = $request->header('X-Access-Token');

            if (!$api_token) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Không tìm thấy X-Access-Token !',
                ], 401);
            }

            $domain = $request->getHost();
            $user = User::where('api_token', $api_token)->where('domain', $domain)->first();

            if (!$user) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'X-Access-Token không hợp lệ !',
                ], 401);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Tài khoản của bạn hiện tại không được phép thực hiện hành động này !',
                ], 401);
            }

            $valid = Validator::make($request->all(), [
                'order_code' => 'required',
            ], [
                'order_code.required' => 'Vui lòng nhập mã đơn hàng cần bảo hành !',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'code' => '400',
                    'status' => 'error',
                    'message' => $valid->errors()->first(),
                ], 400);
            } else {
                $order = Order::where('order_code', $request->order_code)->where('user_id', $user->id)->where('domain', $domain)->first();
                if (!$order) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy đơn hàng cần bảo hành !',
                    ], 400);
                }

                if ($order->status === 'Refunded') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã được hoàn tiền không thể bảo hành !',
                    ], 400);
                }

                if ($order->status === 'WaitingForRefund') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đang chờ hoàn tiền không thể bảo hành !',
                    ], 400);
                }

                if ($order->status === 'Completed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã hoàn thành không thể bảo hành !',
                    ], 400);
                }

                if ($order->status === 'Cancelled') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã bị hủy không thể bảo hành !',
                    ], 400);
                }

                if ($order->status === 'Failed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã thất bại không thể bảo hành !',
                    ], 400);
                }

                if ($order->status === 'Partially Refunded') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã được hoàn tiền một phần không thể bảo hành !',
                    ], 400);
                }

                if ($order->status === 'Partially Completed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã hoàn thành một phần không thể bảo hành !',
                    ], 400);
                }

                $server = $order->server;
                if ($server) {
                    if ($server->providerName == '2mxh') {
                        $twoMXH = new TwoMxhController();
                        $twoMXH->path = $server->providerLink;
                        $result = $twoMXH->warranty($order->order_id);
                        if (isset($result) && $result['status'] === true) {
                            $order->status = 'Processing';
                            $order->save();
                            return response()->json([
                                'code' => '200',
                                'status' => 'success',
                                'message' => 'Đơn hàng của bạn đã được gửi yêu cầu bảo hành!',
                            ], 200);
                        } else {
                            return response()->json([
                                'code' => '400',
                                'status' => 'error',
                                'message' => $result['message'],
                            ], 400);
                        }
                    } else {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Hệ thống không hỗ trợ bảo hành cho dịch vụ này !',
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy máy chủ của đơn hàng này !',
                    ], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            $api_token = $request->header('X-Access-Token');

            if (!$api_token) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Không tìm thấy X-Access-Token !',
                ], 401);
            }

            $domain = $request->getHost();
            $user = User::where('api_token', $api_token)->where('domain', $domain)->first();

            if (!$user) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'X-Access-Token không hợp lệ !',
                ], 401);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Tài khoản của bạn hiện tại không được phép thực hiện hành động này !',
                ], 401);
            }

            $valid = Validator::make($request->all(), [
                'order_code' => 'required',
            ], [
                'order_code.required' => 'Vui lòng nhập mã đơn hàng cần cập nhật !',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'code' => '400',
                    'status' => 'error',
                    'message' => $valid->errors()->first(),
                ], 400);
            } else {
                $order = Order::where('order_code', $request->order_code)->where('user_id', $user->id)->where('domain', $domain)->first();
                if (!$order) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy đơn hàng cần cập nhật !',
                    ], 400);
                }

                if ($order->status === 'Completed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã hoàn thành không thể cập nhật !',
                    ], 400);
                }

                if ($order->status === 'Cancelled') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã bị hủy không thể cập nhật !',
                    ], 400);
                }

                if ($order->status === 'Failed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã thất bại không thể cập nhật !',
                    ], 400);
                }

                if ($order->status === 'Refunded') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã được hoàn tiền không thể cập nhật !',
                    ], 400);
                }

                if ($order->status === 'WaitingForRefund') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đang chờ hoàn tiền không thể cập nhật !',
                    ], 400);
                }

                if ($order->status === 'Partially Refunded') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã được hoàn tiền một phần không thể cập nhật !',
                    ], 400);
                }

                if ($order->status === 'Partially Completed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã hoàn thành một phần không thể cập nhật !',
                    ], 400);
                }

                $server = $order->server;
                if ($server) {
                    if ($server->providerName == '2mxh') {
                        $twoMXH = new TwoMxhController();
                        $twoMXH->path = $server->providerLink;
                        $result = $twoMXH->orderUpdate($order->order_id);
                        if (isset($result) && $result['status'] === true) {
                            $data = $result['data'];
                            $start_number = $data['start_number'];
                            $success_count = $data['success_count'];
                            $status = $data['status'];
                            $id = $data['id'];
                            switch ($status) {
                                case 'Running':
                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Running';
                                    break;
                                case 'Completed':
                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Completed';
                                    break;
                                case 'Canceled':
                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Cancelled';
                                    break;
                                case 'Failed':
                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Failed';
                                    break;
                                case 'Paused':
                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Cancelled';
                                    break;
                                case 'Error':
                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Failed';
                                    break;
                                case 'WaitingForRefund':
                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Cancelled';
                                    break;
                                case 'Refund':
                                    $orderData = json_decode($order->order_data);
                                    $quantity = $orderData->quantity;
                                    $price = $orderData->price;

                                    if ($quantity > $success_count) {
                                        $returned = $quantity - $success_count;
                                    } else {
                                        $returned = $quantity;
                                    }

                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Refunded';

                                    $tranCode = 'INV_24' . rand(1000000, 9999999);
                                    Transaction::create([
                                        'user_id' => $order->user_id,
                                        'tran_code' => $tranCode,
                                        'type' => 'refund',
                                        'action' => 'add',
                                        'first_balance' => $returned,
                                        'before_balance' => $order->user->balance,
                                        'after_balance' => $order->user->balance + ceil($returned * $price),
                                        'note' => 'Hoàn tiền đơn hàng #' . $order->order_code,
                                        'ip' => $request->ip(),
                                        'domain' => $order->domain,
                                    ]);

                                    $order->user->balance += ceil($returned * $price);
                                    $order->user->save();

                                    if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                                        $bot_notify = new TelegramSdk();
                                        $bot_notify->botNotify()->sendMessage([
                                            'chat_id' => siteValue('telegram_chat_id'),
                                            'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được hoàn tiền với số lượng <b>{$returned}</b> tương ứng <b>" . number_format(ceil($returned * $price)) . "đ</b>",
                                            'parse_mode' => 'HTML',
                                        ]);
                                    }
                                    break;
                                default:
                                    $order->start = $start_number;
                                    $order->buff = $success_count;
                                    $order->status = 'Running';
                                    break;
                            }
                            $order->save();
                        } else {
                            return response()->json([
                                'code' => '400',
                                'status' => 'error',
                                'message' => $result['message'],
                            ], 400);
                        }
                    } else {
                        //  cập nhật thành công
                        return response()->json([
                            'code' => '200',
                            'status' => 'success',
                            'message' => 'Đơn hàng của bạn đã được cập nhật thành công !',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy máy chủ của đơn hàng này !',
                    ], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function renewOrder(Request $request)
    {
        try {
            $api_token = $request->header('X-Access-Token');

            if (!$api_token) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Không tìm thấy X-Access-Token !',
                ], 401);
            }

            $domain = $request->getHost();
            $user = User::where('api_token', $api_token)->where('domain', $domain)->first();

            if (!$user) {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'X-Access-Token không hợp lệ !',
                ], 401);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'code' => '401',
                    'status' => 'error',
                    'message' => 'Tài khoản của bạn hiện tại không được phép thực hiện hành động này !',
                ], 401);
            }

            $valid = Validator::make($request->all(), [
                'order_code' => 'required',
                'days' => 'required|numeric|min:1'
            ], [
                'order_code.required' => 'Vui lòng nhập mã đơn hàng cần cập nhật !',
                'days.required' => 'Vui lòng nhập số ngày gia hạn !',
                'days.numeric' => 'Số ngày gia hạn phải là số !',
            ]);

            if ($valid->fails()) {
                return response()->json([
                    'code' => '400',
                    'status' => 'error',
                    'message' => $valid->errors()->first(),
                ], 400);
            } else {
                $order = Order::where('order_code', $request->order_code)->where('user_id', $user->id)->where('domain', $domain)->first();
                if (!$order) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy đơn hàng cần cập nhật !',
                    ], 400);
                }

                if ($order->status === 'Cancelled') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã bị hủy không thể gia hạn !',
                    ], 400);
                }

                if ($order->status === 'Failed') {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Đơn hàng này đã thất bại không thể gia hạn !',
                    ], 400);
                }

                $server = $order->server;
                if ($server) {

                    $price = $order->price;
                    $payment = $order->payment;
                    $quantity = $order->orderdata()['quantity'];
                    $duration = $order->orderdata()['duration'];
                    $posts = $order->orderdata()['posts'] === 'unlimited' ? 1 : $order->orderdata()['posts'];
                    $total = $price * $request->days * $quantity * $posts;

                    if ($user->balance < ceil($total)) {
                        return response()->json([
                            'code' => '400',
                            'status' => 'error',
                            'message' => 'Số dư của bạn không đủ để thực hiện giao dịch này !',
                        ], 400);
                    }

                    $orderdata = json_decode($order->order_data);
                    $orderdata->posts = $orderdata->posts === 'unlimited' ? 1 : $orderdata->posts;
                    $orderdata->payment = $orderdata->payment + ceil($total);
                    $order->order_data = json_encode($orderdata);
                    $order->status = 'Processing';
                    $order->payment = ceil($total);
                    $order->remaining = $request->days;
                    $order->time = now();
                    $order->created_at = now();
                    $order->save();

                    $tranCode = 'INV_24' . rand(1000000, 9999999);
                    Transaction::create([
                        'user_id' => $order->user_id,
                        'tran_code' => $tranCode,
                        'type' => 'renew',
                        'action' => 'sub',
                        'first_balance' => ceil($total),
                        'before_balance' => $order->user->balance,
                        'after_balance' => $order->user->balance - ceil($total),
                        'note' => 'Gia hạn đơn hàng #' . $order->order_code,
                        'ip' => $request->ip(),
                        'domain' => $order->domain,
                    ]);

                    $order->user->balance -= ceil($total);
                    $order->user->save();

                    if (siteValue('telegram_bot_token') && siteValue('telegram_chat_id')) {
                        $bot_notify = new TelegramSdk();
                        $bot_notify->botNotify()->sendMessage([
                            'chat_id' => siteValue('telegram_chat_id'),
                            'text' => "Đơn hàng <b>#{$order->order_code}</b> đã được gia hạn thêm <b>{$request->days}</b> ngày với giá <b>" . number_format(ceil($total)) . "đ</b>",
                            'parse_mode' => 'HTML',
                        ]);
                    }

                    return response()->json([
                        'code' => '200',
                        'status' => 'success',
                        'message' => 'Đơn hàng của bạn đã được gia hạn thành công !',
                    ], 200);
                } else {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'Không tìm thấy máy chủ của đơn hàng này !',
                    ], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
