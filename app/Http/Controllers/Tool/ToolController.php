<?php

namespace App\Http\Controllers\Tool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Support\Facades\Validator;

class ToolController extends Controller
{
    public function getUid(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'link' => 'required|url'
        ]);

        if ($valid->fails()) {
            return response()->json([
                'status' => false,
                'message' => $valid->errors()->first()
            ]);
        } else {
            /* api */
            $link = $request->link;

            function getUID($link)
            {
                $client = new Client();
                $headers = [
                    'accept' => 'application/json, text/javascript, */*; q=0.01',
                    'accept-language' => 'vi,en;q=0.9,en-GB;q=0.8,en-US;q=0.7',
                    'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    'origin' => 'https://id.traodoisub.com',
                    'priority' => 'u=1, i',
                    'referer' => 'https://id.traodoisub.com/',
                    'sec-ch-ua' => '"Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                    'sec-ch-ua-mobile' => '?0',
                    'sec-ch-ua-platform' => '"Windows"',
                    'sec-fetch-dest' => 'empty',
                    'sec-fetch-mode' => 'cors',
                    'sec-fetch-site' => 'same-origin',
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                    'x-requested-with' => 'XMLHttpRequest'
                ];
                $body = 'link=' . $link;
                $request = new Psr7Request('POST', 'https://id.traodoisub.com/api.php', $headers, $body);
                $res = $client->sendAsync($request)->wait();
                $data = json_decode($res->getBody());
                if (isset($data->success) && $data->success == 200) {
                    return $data->id;
                } else {
                    return false;
                }
            }

            $uid = getUID($link);
            if (!$uid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể lấy UID'
                ]);
            }

            try {
                $api_url = "https://buithaihien.com/hienthaibui.php?id=$uid&key=BuiThaiHien";
                $client = new Client();
                $response = $client->request('GET', $api_url);
                $body = $response->getBody();
                $data = json_decode($body);
                if (isset($data) && $data->status == 'success') {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Lấy UID thành công',
                        'data' => [
                            'id' => $data->result->id,
                            'name' => $data->result->name,
                            'username' => $data->result->username,
                            'followers' => $data->result->followers,
                        ]
                    ]);
                } else {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Lấy UID thành công',
                        'data' => [
                            'id' => $uid,
                            'name' => 'Không xác định',
                            'username' => 'Không xác định',
                            'followers' => 'Không xác định',
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Lấy UID thành công',
                    'data' => [
                        'id' => $uid,
                        'name' => 'Không xác định',
                        'username' => 'Không xác định',
                        'followers' => 'Không xác định',
                    ]
                ]);
            }
        }
    }
}
