<?php

namespace prtg\prtg;

/**
* Class prtg
* @package satrobit\prtg-php
*/
class prtg
{

        private static $server;
        private static $username;
        private static $password;
        private static $passhash;

        /**
        * @param string $server
        * @param string $username
        * @param string $password
        */
        function __construct($server)
        {
                if (empty($server)) throw new Exception('Server parameter cannot be empty.');

                self::$server = rtrim($server, '/\\');
        }

    function GetPassHash($username, $password)
    {
        $response =  $this->get('api/getpasshash.htm', ['username' => $username, 'password' => $password], false, false);

                if (!is_numeric($response)) return false;

                return $response;
    }

    function SetCredentials($username, $passhash)
    {
        $this->username = $username;
        $this->passhash = $passhash;
    }


        /**
        * @param string $url
        *
        * @return string
        */
        private function sendRequest($url)
        {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $response = curl_exec($ch);
                curl_close($ch);
                return $response;
        }

    /**
    * @param string $path
    * @param array $parameters
    * @param bool $auth
    * @param bool $json
    *
    * @return array
    */
    private function get($path, $parameters, $json = true, $auth = true)
    {

        // Add authentication ?
        if ($auth)
        {
            $parameters['username'] = $this->username;
            $parameters['passhash'] = $this->passhash;
        }

        $baseUrl = "https://".self::$server;

        // Build queryString. Avoid using http_build_query:
        $queryString = "";
        foreach($parameters as $parm=>$val)
        {
           $queryString .= ($queryString == "") ? "?" : "&";
           $queryString .= $parm . "=" . $val;
        }
        //$queryString = http_build_query($parameters);
        $requestUrl = $baseUrl . '/' . $path . $queryString;
#print "Request: $requestUrl\r\n";

        $response = $this->sendRequest($requestUrl);

                if ($json) return json_decode($response, true);
                return $response;
        }

        /**
        * @param int $sensorId
        *
        * @return array
        */
        public function getsensordetails($sensorId)
        {
                $response =  $this->get('api/getsensordetails.json', ['id' => $sensorId]);

                if (is_null($response)) throw new Exception('Could not find the sensor.');

                return $response;
        }

        /**
        * @param int $sensorId
        * @param string $sdate
        * @param string $edate
        * @param int $avg
        *
        * @return array
        */
        public function historicdata($sensorId, $sdate, $edate, $avg = 0)
        {
                $response =  $this->get('api/historicdata.json', ['id' => $sensorId, 'sdate' => $sdate, 'edate' => $edate, 'avg' => $avg]);
                if (is_null($response)) throw new Exception('Could not find the sensor.');

                return $response;
        }

        /**
        * @param int $sensorId
        * @param string $sdate
        * @param string $edate
        * @param int $graphid
        * @param string $type
        * @param int $avg
        * @param int $height
        * @param int $width
        *
        * @return string
        */
        public function chart($sensorId, $sdate, $edate, $graphid, $type = 'svg', $avg = 15, $height = 270, $width = 850)
        {
                $response =  $this->get('chart.' . $type, ['id' => $sensorId, 'sdate' => $sdate, 'edate' => $edate, 'avg' => $avg, 'graphid' => $graphid, 'height' => $height, 'width' => $width], false);

                if ($response == 'Error creating chart.') throw new Exception('Error creating chart.');

                return $response;
        }

    public function ListDevices($parameters = array())
    {
        $parameters['content'] = 'devices';
        $parameters['count'] = '4000';
        $parameters['columns'] = 'objid,probe,group,host,device,status';
        return $this->get('api/table.json', $parameters, true, true);
    }

    public function ListGroups($parameters = array())
    {
        // columns=objid,name,probe,condition,fold,groupnum,devicenum,upsens,downsens,downacksens,partialdownsens,warnsens,pausedsens,unusualsens,undefinedsens,totalsens,schedule,basetype,baselink,notifiesx,intervalx,access,dependency,position,status,comments,priority,message,parentid,tags,type,active&count=*
        // filter_parentid=$parentId
        $parameters['content'] = 'groups';
        $parameters['count']   = 4000;
        $parameters['columns'] = 'objid,group,status,tags,priority,parentid';
        return $this->get('api/table.json', $parameters, true, true);
    }

    public function ListSensors()
    {
        return $this->get('api/table.json', ['content' => 'sensors'], true, true);
    }

    public function GetSensorTree()
    {
        return $this->get('api/table.json', ['content' => 'sensortree'], true, true);
    }

    public function CreateGroup($basegroup, $newgroup)
    {
        return "Not yet implemented!";
        return $this->post('api/');
    }
}

?>
