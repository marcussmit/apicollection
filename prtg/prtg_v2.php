<?php

class prtg
{
    private static $hostname;
    private static $bearer;

    public function __construct($hostname)
    {
        self::hostname = $hostname;
    }

    private function sendrequest($url)
    {
        $hCurl = curl_init();
        curl_setopt($hCurl, CURLOPT_HTTPHEADER, $header);
    }

    public function GetBearer($username, $password)
    {
        $url = "https://".self::hostname."/api/v2/session";
        self::sendrequest("/", ["username"=>$username, "password"=>$password]);
    }

    public function SetBearer($bearer)
    {
        self::bearer = $bearer;
    }

}

?>