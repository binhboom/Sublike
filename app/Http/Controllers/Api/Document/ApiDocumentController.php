<?php

namespace App\Http\Controllers\Api\Document;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\OrdersResource;
use App\Http\Resources\Document\ServersResource;
use App\Http\Resources\Document\ServiceByIdResource;
use App\Http\Resources\Document\ServicesResource;
use App\Http\Resources\Document\ServicesWithServersResources;
use App\Http\Resources\Document\UserResource;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceServer;
use App\Models\User;
use Illuminate\Http\Request;

class ApiDocumentController extends Controller
{
    public function getMe(Request $request)
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

            return response()->json([
                'code' => '200',
                'status' => 'success',
                'data' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getServices(Request $request)
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

            // service
            $services = Service::where('status', 'active')->where('domain', $request->getHost())->get();
            return response()->json([
                'code' => '200',
                'status' => 'success',
                'data' => ServicesResource::collection($services),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getServiceById(Request $request, $id)
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

            // service
            $service = Service::where('status', 'active')->where('id', $id)->where('domain', env('APP_MAIN_SITE'))->first();
            if ($service) {
                return response()->json([
                    'code' => '200',
                    'status' => 'success',
                    'data' => new ServiceByIdResource($service),
                ]);
            } else {
                return response()->json([
                    'code' => '404',
                    'status' => 'error',
                    'message' => 'Không tìm thấy dịch vụ !',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getServersByServices(Request $request)
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

            // services
            $services = Service::where('status', 'active')->where('domain', $request->getHost())->get();

            return response()->json([
                'code' => '200',
                'status' => 'success',
                'data' => ServicesWithServersResources::collection($services),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // serrver
    public function getServers(Request $request)
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

            // service
            $servers = ServiceServer::where('status', 'active')->where('domain', $request->getHost())->get();

            return response()->json([
                'code' => '200',
                'status' => 'success',
                'data' => ServersResource::collection($servers),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getServerById(Request $request, $id)
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

            // service
            $server = ServiceServer::where('status', 'active')->where('id', $id)->where('domain', $request->getHost())->first();
            if ($server) {
                return response()->json([
                    'code' => '200',
                    'status' => 'success',
                    'data' => new ServersResource($server),
                ]);
            } else {
                return response()->json([
                    'code' => '404',
                    'status' => 'error',
                    'message' => 'Không tìm thấy server !',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // order
    public function getOrders(Request $request)

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

            $service = $request->get('service');

            if (!$service) {
                $orders = $user->orders()->orderBy('id', 'desc')->get();
            }else{
                $orders = $user->orders()->where('service_id', $service)->orderBy('id', 'desc')->get();
            }

            return response()->json([
                'code' => '200',
                'status' => 'success',
                'data' => OrdersResource::collection($orders),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getOrderById(Request $request, $id){
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

            $order = Order::where('id', $id)->where('user_id', $user->id)->first();

            if ($order) {
                return response()->json([
                    'code' => '200',
                    'status' => 'success',
                    'data' => new OrdersResource($order),
                ]);
            } else {
                return response()->json([
                    'code' => '404',
                    'status' => 'error',
                    'message' => 'Không tìm thấy đơn hàng !',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function currentUser(Request $request)
    {
        try {
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
