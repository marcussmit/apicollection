<?php
        
namespace netbox\netbox;

class netbox
{
 
        var $hostname = "";
        var $token    = "";
        var $usessl   = true;

        private $DebugLevel = 5; // 0 = no logging, 5 = maximum. 

        private $eol = "\r\n";

        public function __construct($hostname, $token, $usessl = true)
        {
                // Check values for validity
                if (!is_bool($usessl)) return false;
                $this->token = $token;
                $this->hostname = $hostname;
                $this->usessl = $usessl;
        }
 
        private function Request($path, $mode = "GET", $parameters = null)
        {
                $url = ($this->usessl === true) ? "https://" : "http://";
                $url .= $this->hostname."/api/".$path;
                $parms = "";
 
                if ($mode == "GET")
                {
                        $parms = "?";
                        foreach($parameters as $parm=>$val)
                        {
                                $parms[] = $parm."=".urlencode($val);
                        }
                        $url .= join("&", $parms);
                }
 
                $hCurl = curl_init($url);
                curl_setopt($hCurl, CURLOPT_HTTPHEADER, array("Authorization: Token ".$this->token));
                curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, "1");
                curl_setopt($hCurl, CURLOPT_SSL_VERIFYPEER, "0");
                return curl_exec($hCurl);
        }

        private function debug($level, $message)
        {
                if ($level >= $this->DebugLevel) print $message.$this->eol;
        }
 
        public function GetModules()
        {
                $Modules[] = array("path"=>"circuits/circuit-terminations");
                $Modules[] = array("path"=>"circuits/circuit-types");
                $Modules[] = array("path"=>"circuits/provider-networks");
                $Modules[] = array("path"=>"dcim/console-server-ports");
                $Modules[] = array("path"=>"dcim/devices");
                $Modules[] = array("path"=>"dcim/regions");
                $Modules[] = array("path"=>"dcim/site-groups");
                $Modules[] = array("path"=>"dcim/sites");
                $Modules[] = array("path"=>"dcim/virtual-chassis");
                $Modules[] = array("path"=>"extras/config-contexts");
                $Modules[] = array("path"=>"extras/custom-links");
                $Modules[] = array("path"=>"ipam/asns");
                $Modules[] = array("path"=>"ipam/fhrp-group-assignments");
                $Modules[] = array("path"=>"ipam/ip-addresses");
                return $Modules;
        }

        // Create backup:
        public function CreateBackup($BaseDir)
        {
                // Feth the list of object types that are available to fetch:
                $Modules = $this->GetModules();

                foreach($Modules as $Module)
                { 
                        $fullpath = $BaseDir . "/" . $Module['path'];
                        if (!file_exists($fullpath))
                        {
                                $this->debug(5, "Create $fullpath");
                                mkdir ($fullpath, 777, true);
                        } 
                        // Fetch one object, to retrieve the counter:
                        $Item = $this->Request($Module['path'], "GET", array("offset"=>0, "limit"=>1));
                        $Decoded = json_decode($Item, true);
                        $count = $Decoded['count'];
                        $this->debug(5, "Found $count items for ".$Module['path']);
 
                        // Fetch all items:
                        $items_per_query = 100;
                        for($i = 0; $i < $count; $i += $items_per_query)
                        {
                                $this->debug(5, "Fetch items $i up to ".($i + $items_per_query -1));
                                $Item = $this->Request("/".$Module['path']."/", "GET", array("offset"=>$i, "limit"=>$items_per_query));
                                $arrItem = json_decode($Item);
                                $Result = $arrItem->results[0];
                                for ($n = 0; $n < $items_per_query; $n++)
                                {
                                        file_put_contents($fullpath."/".$Result->id.".raw", print_r($Result, true));
                                }
                        }
 
                }
        }
 
        // Section IPAM / AGGREGATES
        public function ipam_aggregates_list()
        {
                $list = $this->Request("/ipam/aggregates/", "GET");
                $list = json_decode($list);
                return $list['results'];
        }
 
        /// Section IPAM / IP-ADDRESSES
        public function ipam_ipaddresses_list($offset, $limit)
        {
                $Parameters['offset'] = $offset;
                $Parameters['limit']  = $limit;
                $list = $this->Request("/ipam/ip-addresses/", "GET", $Parameters);
                $list = json_decode($list);
                return $list['results'];
        }
 
}
 
?>