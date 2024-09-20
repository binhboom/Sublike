<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaostarController extends Controller
{
    private $api_token;

    public $path;
    public $server;

    public $data = [
        'object_id' => '',
        'quantity' => '',
        'object_type' => '',
        'package_name' => '',
        'list_message' => '',
        'num_minutes' => '',
        'num_day' => '',
        'slbv' => '',
    ];


    public function __construct()
    {
        $this->api_token = env('BAOSTAR_API_TOKEN');
    }

    public function createOrder()
    {
        $url = 'https://api.baostar.pro/api/' . $this->path . '/buy';
        $data = $this->data;

        /* $dataPost = [
            'object_id' => $data['order_link'] ?? '',
            'quantity' => $data['quantity'] ?? '',
            'object_type' => $data['reaction'] ?? '',
            'package_name' => $data['server_order'] ?? '',
            'list_message' => $data['comment'] ?? '',
            'num_minutes' => $data['minutes'] ?? '',
            'num_day' => $data['days'] ?? '',
            'slbv' => $data['quantity'] ?? '',
            ]; */

        $dataPost = $this->data;

        // $dataPost = http_build_query($dataPost);
        $result = $this->sendRequest($url, $dataPost);
        if (isset($result)) {
            if ($result['success'] == true) {
                return [
                    'status' => true,
                    'message' => 'Tạo đơn hàng thành công',
                    'data' => [
                        'code_order' => $result['data']['id'],
                    ],
                ];
            } else {
                return [
                    'status' => false,
                    'message' => $result['message'],
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'Tạo đơn hàng thất bại vui lòng thử lại',
            ];
        }
    }

    public function order($order_list, $type = 'facebook')
    {

        $data = json_encode([
            'type' => $type,
            'list_ids' => $order_list,
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.baostar.pro/api/logs-order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'api-key: ' . $this->api_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    public function sendRequest($url, $data)
    {

        /*  $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => '  ',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "object_id":"717244166141162",
                "quantity":50,
                "object_type":"like",
                "package_name":"facebook_like"
            }',
            CURLOPT_HTTPHEADER => array(
                'api-key: ' . $this->api_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl); */

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1000,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'api-key: ' . $this->api_token,
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }
}
