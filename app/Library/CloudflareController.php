<?php

namespace App\Library;

use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\User;

class CloudflareController
{
    // public $email = "lbd.2005.dev@gmail.com"; //email tài khoản cloudflade
    // public $global_key = "26a1e2f18b47594d4fa5e5ad547a463c24e57"; //api key lấy từ https://dash.cloudflare.com/profile/api-tokens
    // public $account_id = "b68e5ba86f232df07712e14d35b33e7b"; // account id lấy click vào domain rồi kéo xuống tìm acccount id
    // public $token = "ptQTvpT7ojf3tCrUJ-gasT9vI-gjzgWUIKeQffv-"; //tạo token từ https://dash.cloudflare.com/profile/api-tokens
    // public $ip_host = ""; // ip của hosting
    public $email = "hotro.cardvip247.com@gmail.com"; //email tài khoản cloudflade
    public $global_key = ""; //api key lấy từ https://dash.cloudflare.com/profile/api-tokens
    public $account_id = ""; // account id lấy click vào domain rồi kéo xuống tìm acccount id
    public $token = ""; //tạo token từ https://dash.cloudflare.com/profile/api-tokens
    public $ip_host = ""; // ip của hosting

    public function __construct()
    {
        $this->email = env('EMAIL_CLOUDFLARE');
        $this->global_key = env('GLOBAL_API_KEY_CLOUDFLARE');
        $this->account_id = env('ACCOUNT_ID_CLOUDFLARE');
        $this->token = env('TOKEN_API_CLOUDFLARE');
        $this->ip_host = env('IP_HOST');
    }

    public function profile()
    {
        $url = "https://api.cloudflare.com/client/v4/user";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "X-Auth-Email: " . $this->email,
            "X-Auth-Key: " . $this->global_key,
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp, true);
    }

    public function addDomain($domainName)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "account": {
                "id": "' . $this->account_id . '"
            },
            "name": "' . $domainName . '",
            "type": "full"
            }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
                'Cookie: __cflb=0H28vgHxwvgAQtjUGUFqYFDiSDreGJnV1M6FaTYBqaD; __cfruid=358beaec641e06b842f247be1a47c05a8076c644-1714042941'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);
        if (isset($result) && $result['success'] == true) {

            // get dns_id
            $dd = $this->recordDomain($result['result']['id'], $domainName);

            $dns_id = $dd['result'][0]['id'] ?? null;

            return [
                'status' => 'success',
                'message' => 'Thêm tên miền thành công!',
                'data' => [
                    'zone_id' => $result['result']['id'],
                    'zone_name' => $result['result']['name'],
                    'zone_status' => $result['result']['status'],
                    'dns_id' => $dns_id
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => $result['errors'][0]['message'] ?? "Lỗi không xác định vui lòng thử lại sau!"
            ];
        }
    }

    public function deleteDomain($zone_id)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones/' . $zone_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $result = json_decode($response, true);
        if (isset($result) && $result['success'] == true) {
            return [
                'status' => 'success',
                'message' => 'Xóa tên miền thành công!',
                'data' => [
                    'zone_id' => $result['result']['id'],
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => $result['errors'][0]['message'] ?? "Lỗi không xác định vui lòng thử lại sau!"
            ];
        }
    }

    public function findDomain($domain_name)
    {

        $url = "https://api.cloudflare.com/client/v4/zones";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Authorization: Bearer " . $this->token,
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($resp, true);
        $d = $domain_name;
        $v = [];
        foreach ($data['result'] as $v) {
            $id = $v['id'];
            $name = $v['name'];
            $check = strpos($name, $d);
            if ($check !== false) {
                $v['zone_id'] = $id;
                break;
            }
        }
        return $v;
    }

    public function infoDomain($zone_id)
    {
        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id";
        return $this->send($url, "GET");
    }

    public function recordDomain($zone_id, $domain)
    {

        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records?search=$domain";
        return $this->send($url, "GET");
    }

    public function dnsRecord($zone_id, $dns_record)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records/$dns_record");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'X-Auth-Email: ' . $this->email;
        $headers[] = 'X-Auth-Key: ' . $this->global_key;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result, true);
        /* $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records/$dns_record";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "X-Auth-Email: " . $this->email,
            "X-Auth-Key: " . $this->global_key,
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp, true); */
    }

    public function scanDns($zone_id)
    {

        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records/scan";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "X-Auth-Email: " . $this->email,
            "X-Auth-Key: " . $this->global_key,
            "Content-Type: application/json",
            "Content-Length: 0",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp, true);
    }

    public function createDns($zone_id)
    {
        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records";

        $data = [
            "type" => "A",
            "name" => '@',
            "content" => $this->ip_host,
            "ttl" => 1,
            "priority" => 10,
            "proxied" => true
        ];

        return $this->send($url, "POST", $data);
    }

    public function updateDns($zone_id, $dns_record)
    {
        $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone_id .  '/dns_records/' . $dns_record;

        $data = [
            "type" => "A",
            "name" => '@',
            "content" => $this->ip_host,
            "ttl" => 1,
            "priority" => 10,
            "proxied" => true
        ];

        $send = $this->send($url, "PUT", $data);
        return $send;

    }

    public function deleteDns($zone_id, $dns_record)
    {

        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records/$dns_record";
        return $this->send($url, "DELETE");
    }

    public function updateSslTls($zone_id)
    {

        $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/settings/ssl';
        $data = [
            "value" => "full"
        ];

        return $this->send($url, "PATCH", $data);
    }

    public function send($url, $method = "GET", $data = [])
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $this->token,
                'X-Auth-Email: ' . $this->email,
                'X-Auth-Key: ' . $this->global_key,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}

#tips
#step 1: Thêm tên miền
/* $data = Cloudflare::addDomain('luongbinhduong.ga');
print_r($data); */

#step 2: Thông tin tên miền lấy data
// $data = Cloudflare::findDomain();
/* foreach($data['result'] as $v){
    $id = $v['id'];
    $name = $v['name'];
    $d = "shopcodengon.net";
    $check = strpos($name, $d);
    if($check !== false){
        echo $id . "|" . $name;
    }
} */
// $data = Cloudflare::infoDomain('abc3fd07df0d6fc1cf1157e3f0722d8c');

#step 3: Thông tin tên miền lấy data id để sử dụng step 4
// $data = Cloudflare::recordDomain('65d06f72552936b70aae43b2fb0b7098');
// $data = Cloudflare::dnsRecord('ea706bdc19de26348a6d90b32503a06f', 'abc3fd07df0d6fc1cf1157e3f0722d8c');

#step 4: Nhập zone id của tên miền và và id của tên miền lấy từ step 3
// $data = Cloudflare::deleteDns('75288884cd23824e712fe04946639f98', '044c123ed7501712089a9342c72bbb43');

#step 5: Nhập host cho domain site con
// $data = Cloudflare::createDns('75288884cd23824e712fe04946639f98');

#step 6: Cập nhật dns
// $data = Cloudflare::updateDns('65d06f72552936b70aae43b2fb0b7098', '65e9d9c19f37fdb95941178509d40d6a');

/* echo "<pre>";
print_r($data);
echo "</pre>"; */
