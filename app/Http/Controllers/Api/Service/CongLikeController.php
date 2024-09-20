<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CongLikeController extends Controller
{
    public $apiToken = '';
    public $apiUrl = 'https://conglike.com/api/';
    public $data = [
        'post_id' => '',
        'page_id' => '',
        'soluong' => 0,
        'num_package' => 0, // số ngày
        'package_id' => 0,
    ];

    public function __construct()
    {
        $this->apiToken = env('CONGLIKE_API_TOKEN');
    }

    public function order($path)
    {
        return $this->send($path, $this->data);
    }

    public function send($url, $data = [])
    {

        $dataNew = array_merge($data, ['token' => $this->apiToken]);
        $dataNew = http_build_query($dataNew);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://conglike.com/api/' . $url . '?' . $dataNew,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }
}
