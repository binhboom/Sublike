<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class Hacklike17Controller extends Controller
{
    public $apiUrl = 'https://hacklike17.com/api/';

    public $apiToken = '';

    public $data = [
        'uid' => '',
        'count' => 0,
        'server' => '',
        'reaction' => 'like',
        'speed' => 0,
        'speed_server_2' => 'default',
        'list_comment' => '',
        'comments' => '',
        'content' => '',
        'type_view' => 0,
        'minutes' => 30,
        'days' => 0,
    ];

    public function __construct()
    {
        $this->apiToken = env('HACKLIKE17_API_TOKEN');
    }

    public function order($path)
    {
        $data = $this->data;
        $url = $this->apiUrl . $path;

        return $this->send($data, $url);
    }

    public function statusOrder($orderId = '')
    {

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'form_params' => [
                'token' => $this->apiToken,
                'order_ids[]' => $orderId
            ]
        ];
        $request = new Psr7Request('POST', 'https://hacklike17.com/api/facebook/get-orders', $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody(), true);
    }

    public function refundOrder($orderId = '')
    {
        $url = $this->apiUrl . 'faccebook/refund';

        $data = [
            'id' => $orderId,
        ];

        return $this->send($data, $url);
    }

    public function warrantyOrder($orderId = '')
    {
        $url = $this->apiUrl . 'faccebook/warranty';

        $data = [
            'id' => $orderId,
        ];

        return $this->send($data, $url);
    }

    public function getPrices()
    {
        $url = $this->apiUrl . 'price';

        return $this->send([], $url);
    }



    public function send($data = [], $url = '')
    {

        $data = array_merge($data, ['token' => $this->apiToken]);
        // $data = http_build_query($data);

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'form_params' => $data
        ];
        $request = new Psr7Request('POST', $url, $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody(), true);

        // echo $res->getBody();
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => $url,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => $data,
        //     CURLOPT_HTTPHEADER => array(
        //         'Content-Type: application/x-www-form-urlencoded'
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // return json_decode($response, true);
    }
}
