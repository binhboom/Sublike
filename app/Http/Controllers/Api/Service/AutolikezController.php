<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class AutolikezController extends Controller
{
    /** API URL */
    public $api_url = 'https://api.at88.vn/api/v2/';

    /** Your API key */
    public $api_key = '';

    public function __construct()
    {
        $this->api_key = env('AUTO_LIKEZ_API_KEY');
    }

    public $path = "";
    public $data = [];


    public function createOrder()
    {
        $url = $this->api_url . $this->path . '/order';

        $data = [
            'package_name' => $this->data['server_order'],
            'object_id' => $this->data['object_id'],
            'quantity' => $this->data['quantity'],
            'object_type' => $this->data['object_type'], // reaction
            'num_minutes' => $this->data['num_minutes'] ?? 0,
            'list_messages' => $this->data['list_messages'] ?? '',
            'month' => $this->data['month'] ?? 0,
        ];

        $client = new Client();
        $headers = [
            'api-token' => $this->api_key,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'form_params' => $data
        ];
        $request = new Psr7Request('POST', $url, $headers);
        // $res = $client->send($request, $options);

        // if ($res->getStatusCode() == 200) {
        //     return json_decode($res->getBody(), true);
        // } else {
        //     return throw new \Exception(json_decode($res->getBody(), true)['message']);
        // }

        try {
            $res = $client->send($request, $options);

            if ($res->getStatusCode() == 200) {
                return json_decode($res->getBody(), true);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => json_decode($res->getBody(), true)['message']
                ], $res->getStatusCode());
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $responseBody = json_decode($response->getBody()->getContents(), true);
                $message = $responseBody['message'] ?? 'Unknown error';
                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ], $response->getStatusCode());
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        //     try {
        //         $res = $client->send($request, $options);

        //         if ($res->getStatusCode() == 200) {
        //             return json_decode($res->getBody(), true);
        //         } else {
        //             return response()->json([
        //                 'status' => 'error',
        //                 'message' => json_decode($res->getBody(), true)['message']
        //             ], $res->getStatusCode());
        //         }
        //     } catch (RequestException $e) {
        //         if ($e->hasResponse()) {
        //             $response = $e->getResponse();
        //             $responseBody = json_decode($response->getBody()->getContents(), true);
        //             $message = $responseBody['message'] ?? 'Unknown error';
        //             return response()->json([
        //                 'status' => 'error',
        //                 'message' => $message
        //             ], $response->getStatusCode());
        //         } else {
        //             return response()->json([
        //                 'status' => 'error',
        //                 'message' => $e->getMessage()
        //             ], 500);
        //         }
        //     }
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $e->getMessage()
        //     ], 500);
        // }
    }


    public function getOrder($order_id)
    {

        $url = $this->api_url . 'list-order?model=facebook';

        $client = new Client();
        $headers = [
            'api-token' => $this->api_key,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'form_params' => [
                'model' => 'facebook',
                'id' => $order_id
            ]
        ];
        $request = new Psr7Request('POST', $url, $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody(), true);
    }
}
