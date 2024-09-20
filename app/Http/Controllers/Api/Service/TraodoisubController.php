<?php

namespace App\Http\Controllers\Api\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class TraodoisubController extends Controller
{


    public $path = "";
    private $username = "";
    private $password = "";

    public $data = [
        // 'id' => '',
        // 'sl' => 20,
        // 'is_album' => 'not',
        // 'speed' => '1',
        // 'post' => '5',
        // 'time_pack' => '7', // số ngày
        // 'packet' => 50, // số lượng
        // 'dateTime' => '',
        // 'noidung' => '',
        // 'loaicx' => 'LIKE'
    ];

    public function __construct()
    {
        $this->username = env('USERNAME_TDS');
        $this->password = env('PASSWORD_TDS');
    }

    public function order()
    {
        $login = json_decode($this->login(), true);
        if ($login['success'] == true) {
            $uri = 'https://traodoisub.com/mua/' . $this->path . '/themid.php';
            $data = $this->send($this->data, $uri);
            return $data;
        } else {
            return "Vui lòng thao tác lại";
        }
    }

    public function status($order_id, $all = false)
    {

        $login = json_decode($this->login(), true);
        if ($login['success'] == true) {

            $stack = HandlerStack::create();
            $stack->push(Middleware::retry(function (
                $retries,
                RequestInterface $request,
                $response = null,
                RequestException $exception = null
            ) {
                if ($retries >= 4) {
                    return false;
                }

                if ($response && $response->getStatusCode() === 404 || $response->getStatusCode() == 429) {
                    return true; // Thử lại nếu gặp mã trạng thái 429 (quá nhiều yêu cầu)
                }

                return false;
            }, function ($retries) {
                // Tăng thời gian chờ giữa các lần thử lại
                return 1000 * pow(2, $retries); // Exponential backoff
            }));

            $client = new Client(['handler' => $stack]);
            // sleep(5);
            $headers = [
                'accept' => '*/*',
                'accept-language' => 'vi,en;q=0.9,en-GB;q=0.8,en-US;q=0.7',
                'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'cookie' => $this->getCookie(),
                'origin' => 'https://traodoisub.com',
                'priority' => 'u=1, i',
                'sec-ch-ua' => '"Microsoft Edge";v="125", "Chromium";v="125", "Not.A/Brand";v="24"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"Windows"',
                'sec-fetch-dest' => 'empty',
                'sec-fetch-mode' => 'cors',
                'sec-fetch-site' => 'same-origin',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.0.0',
                'x-requested-with' => 'XMLHttpRequest'
            ];
            // $body = 'page=1&query=3794104854243602';
            $body = $all === true ? 'page=1&query=' : 'page=1&query=' . $order_id;
            $request = new Psr7Request('POST', 'https://traodoisub.com/mua/' . $this->path . '/fetch.php', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody(), true);
        } else {
            return "";
        }
    }

    public function login()
    {
        $client = new Client();

        $response = $client->post('https://traodoisub.com/scr/login.php', [
            'headers' => [
                'accept'             => 'application/json, text/javascript, */*; q=0.01',
                'accept-language'    => 'vi,en;q=0.9,en-GB;q=0.8,en-US;q=0.7',
                'content-type'       => 'application/x-www-form-urlencoded; charset=UTF-8',
                'origin'             => 'https://traodoisub.com',
                'priority'           => 'u=1, i',
                'referer'            => 'https://traodoisub.com/',
                'sec-ch-ua'          => '"Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile'   => '?0',
                'sec-ch-ua-platform' => '"Windows"',
                'sec-fetch-dest'     => 'empty',
                'sec-fetch-mode'     => 'cors',
                'sec-fetch-site'     => 'same-origin',
                'user-agent'         => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'x-requested-with'   => 'XMLHttpRequest'
            ],
            'form_params' => [
                'username' => $this->username,
                'password' => $this->password
            ]
        ]);
        $cookie = $response->getHeader('Set-Cookie');
        $response = $response->getBody()->getContents();

        // save cookie to file
        $file = fopen(__DIR__ . "/cookie.txt", "w");
        fwrite($file, $cookie[0]);
        fclose($file);

        return $response;
    }

    public function send($data = null, $url = null)
    {

        $client = new Client();

        $response = $client->post($url, [
            'headers' => [
                'accept'             => '*/*',
                'accept-language'    => 'vi,en;q=0.9,en-GB;q=0.8,en-US;q=0.7',
                'content-type'       => 'application/x-www-form-urlencoded; charset=UTF-8',
                'cookie'             => $this->getCookie(),
                'origin'             => 'https://traodoisub.com',
                'priority'           => 'u=1, i',
                'referer'            => 'https://traodoisub.com/',
                'sec-ch-ua'          => '"Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile'   => '?0',
                'sec-ch-ua-platform' => '"Windows"',
                'sec-fetch-dest'     => 'empty',
                'sec-fetch-mode'     => 'cors',
                'sec-fetch-site'     => 'same-origin',
                'user-agent'         => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'x-requested-with'   => 'XMLHttpRequest'
            ],
            'form_params' => $data
        ]);

        // return $response->getBody()->getContents();
        return ($response->getBody()->getContents());
    }

    public function getCookie()
    {
        $file = fopen(__DIR__ . "/cookie.txt", "r");
        $cookie = fread($file, filesize(__DIR__ . "/cookie.txt"));
        fclose($file);
        return $cookie;
    }
}
