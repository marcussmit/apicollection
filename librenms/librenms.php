<?php

namespace librenms\librenms;

class librenms
{

    var $hostname = "";
    var $token    = "";
    var $ssl      = true;

    public function __construct($hostname, $token, $ssl = true)
    {
        // Construct function
        $this->hostname = $hostname;
        $this->token    = $token;
        $this->ssl      = $ssl;
    }

    public function __destruct()
    {
        // Destruct function
    }

    private function Request($path = "", $method = "GET")
    {
        // Build URL:
        $url = ($this->ssl == true) ? "https://" : "http://";
        $url .= $this->hostname."/api/v0".$path;

        // Build cURL and send query. Return the body:
        $hCurl = curl_init($url);
        curl_setopt($hCurl, CURLOPT_HTTPHEADER, array("X-Auth-Token: ".$this->token));
        curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($hCurl);

    }

    public function GetDevices()
    {
        return $this->Request("/devices", "GET");
    }

    public function GetLocations()
    {
        return $this->Request("/locations", "GET");
    }
    
    public function GetPorts()
    {
        return $this->Request("/ports", "GET");
    }


}