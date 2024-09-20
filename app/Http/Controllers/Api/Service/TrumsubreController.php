<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class TrumsubreController extends Controller
{
    private $api_token;
    public $path = "";
    public $data = [
        'object_id' => '',
        'quantity' => '',
        'speed' => '',
        'comment' => '',
        'minutes' => '',
        'time' => '',
        'days' => '',
        'post' => '',
        'reaction' => '',
        'server_order' => '',
        'social' => '',
    ];

    public function __construct()
    {
        $this->api_token = env('TRUMSUBRE_API_TOKEN');
    }

    public function CreateOrder()
    {
        $url = "https://trumsubre.com/api/service/";
        $headers[] = 'Api-token: ' . $this->api_token;
        $headers[] = 'Content-Type: application/json';
        $uri = $url . $this->path . '/order';
        $data = $this->data;
        $dataPost = [
            'link_post' => $data['object_id'] ?? '',
            'idgroup' => $data['object_id'] ?? '',
            'idpost' => $data['object_id'] ?? '',
            'idfb' => $data['object_id'] ?? '',
            'idpage' => $data['object_id'] ?? '',
            'idcomment' => $data['object_id'] ?? '',
            'link_story' => $data['object_id'] ?? '',
            'link_video' => $data['object_id'] ?? '',
            'username' => $data['object_id'] ?? '',
            'server_order' => $data['server_order'] ?? 'null',
            'reaction' => $data['reaction'] ?? 'like',
            'amount' => $data['quantity'] ?? '0',
            'speed' => $data['speed'] == '1' ? 'fast' : 'slow',
            'comment' => $data['comment'] ?? '',
            'minutes' => $data['minutes'] ?? '0',
            'time' => $data['time'] ?? '0',
            'days' => $data['days'] ?? '0',
            'post' => $data['post'] ?? '0',
        ];

        $result = $this->curl($uri, $headers, $dataPost);
        return $result;
    }

    public function speed($order_code)
    {
        $url = "https://trumsubre.com/api/service/";
        $headers[] = 'Api-token: ' . $this->api_token;
        $headers[] = 'Content-Type: application/json';


        $uri = $url . $this->path . '/confirm';
        $data = [
            'action' => 'speed_up',

            'code_order' => $order_code,
        ];
        $result = $this->curlOrder($uri, $headers, $data);

        if (isset($result['status'])) {
            if ($result['status'] == true) {
                return $data = [
                    'status' => true,
                    'message' => $result['message'],

                ];
            } else {
                return $data = [
                    'status' => 'error',
                    'message' => $result['message'],
                ];
            }
        }
    }


    public function order($order_code)
    {

        $client = new Client();
        $headers = [
            'Api-token' => $this->api_token
        ];
        $options = [
            'multipart' => [
                [
                    'name' => 'code_orders',
                    'contents' => $order_code
                ]
            ]
        ];
        $request = new Psr7Request('POST', 'https://trumsubre.com/api/service/' . $this->path . '/list', $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody(), true);

        // $url = "https://trumsubre.com/api/service/";
        // $headers[] = 'Api-token: ' . $this->api_token;
        // $headers[] = 'Content-Type: application/json';

        // $uri = $url . $this->path . '/list';
        // $data = [
        //     'code_orders' => $order_code,
        // ];
        // $result = $this->curlOrder($uri, $headers, $data);
        // return $result;
    }

    public function curl($path, $token, $data = [])
    {
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $path);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $token);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // $result = curl_exec($ch);
        // curl_close($ch);
        // $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // $res = [];
        // return json_decode($result, true);

        // $client = new Client();
        // $request = new Psr7Request('POST', $path, $token, json_encode($data));
        // $res = $client->send($request);
        // return json_decode($res->getBody(), true);


        $client = new Client();
        $headers = [
            'accept' => 'application/json, text/javascript, */*; q=0.01',
            'api-token' => $this->api_token,
            'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
        ];
        $body = http_build_query($data);
        $request = new Psr7Request('POST', $path, $headers, $body);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody(), true);
    }

    public function curlOrder($path, $token, $data = [], $method = 'POST')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $token);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        curl_close($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res = [];
        return json_decode($result, true);
    }
}
