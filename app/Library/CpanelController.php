<?php

namespace App\Library;

class CpanelController
{
    // private $cpanel_server = "https://host98.vietnix.vn:2083/"; // link host: VD: https://web07.vn-server.com:2083/cpsess3957128501/
    // private $username_cpanel = "fbvnvn"; //username cpanel
    // private $password_cpanel = "Trietduong3032007@"; //password cpanel

    private $cpanel_server = ""; // link host: VD: https://web07.vn-server.com:2083/cpsess3957128501/
    private $username_cpanel = ""; //username cpanel
    private $password_cpanel = ""; //password cpanel

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->cpanel_server = env('CPANEL_HOST');
        $this->username_cpanel = env('CPANEL_USER');
        $this->password_cpanel = env('CPANEL_PASS');
    }


    public function createDomain($domain)
    {
        $url = $this->cpanel_server . "/json-api/cpanel?cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=Park&cpanel_jsonapi_func=park&domain=$domain";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $header[0] = "Authorization: Basic " . base64_encode($this->username_cpanel . ":" . $this->password_cpanel) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return, true);
    }

    public function deleteDomain($domain)
    {
        $url = $this->cpanel_server . "/json-api/cpanel?cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=Park&cpanel_jsonapi_func=unpark&domain=$domain";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $header[0] = "Authorization: Basic " . base64_encode($this->username_cpanel . ":" . $this->password_cpanel) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $url);
        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return, true);
    }
}
