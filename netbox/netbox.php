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
                $this->token    = $token;
                $this->hostname = $hostname;
                $this->usessl   = $usessl;
        }
 
        private function Request($path, $mode = "GET", $parameters = null)
        {
                $url = ($this->usessl === true) ? "https://" : "http://";
                $url .= $this->hostname."/api/".$path."/";
                $parms = "";
 
                if ($mode == "GET")
                {
                        $parms = array();
                        foreach($parameters as $parm=>$val)
                        {
                                $parms[] = $parm."=".urlencode($val);
                        }
                        $url .= "?".join("&", $parms);
                }
 
                $this->debug(5, "Fetching url: $url", 3);

                $hCurl = curl_init($url);
                curl_setopt($hCurl, CURLOPT_HTTPHEADER, array("Authorization: Token ".$this->token));
                curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, "1");
                curl_setopt($hCurl, CURLOPT_SSL_VERIFYPEER, "0");

                $Result = curl_exec($hCurl);

                // Store querylog:
                $date = date("YmdHisu");
                if (!file_exists("/var/log/apicollection/netbox/$path/")) mkdir ("/var/log/apicollection/netbox/".$path."/", 755, true);
                file_put_contents("/var/log/apicollection/netbox/$path/$date.log", "$url\r\n\r\n$Result");
                return curl_exec($hCurl);
        }

        private function debug($level, $message, $indent = 0)
        {
                if ($level >= $this->DebugLevel) print str_repeat(".", $indent); print $message.$this->eol;
        }
 
        public function GetModules()
        {
                $Modules[] = array("path"=>"circuits/circuit-terminations", "Identifier"=>"id");
                $Modules[] = array("path"=>"circuits/circuit-types", "Identifier"=>"id");
                $Modules[] = array("path"=>"circuits/provider-networks", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/console-server-ports", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/devices", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/device-types", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/front-ports", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/interface-templates", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/interfaces", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/inventory-items", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/locations", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/manufacturers", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/platforms", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/power-feeds", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/power-outlet-templates", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/power-outlets", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/power-panels", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/rack-roles", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/regions", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/site-groups", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/sites", "Identifier"=>"id");
                $Modules[] = array("path"=>"dcim/virtual-chassis", "Identifier"=>"id");
                $Modules[] = array("path"=>"extras/config-contexts", "Identifier"=>"id");
                $Modules[] = array("path"=>"extras/custom-links", "Identifier"=>"id");
                $Modules[] = array("path"=>"ipam/asns", "Identifier"=>"id");
                $Modules[] = array("path"=>"ipam/fhrp-group-assignments", "Identifier"=>"id");
                $Modules[] = array("path"=>"ipam/ip-addresses", "Identifier"=>"id");
                return $Modules;
        }

        // Create backup:
        public function CreateBackup($BaseDir)
        {
                // Feth the list of object types that are available to fetch:
                $Modules = $this->GetModules();

                foreach($Modules as $Module)
                { 
                        $this->debug(5, "Processing module ".$Module['path']);

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
                                $last_item = (($count - $i) > $items_per_query) ? $i+$items_per_query-1 : $count - $i;
                                $this->debug(5, "Fetch items $i to $last_item", 3);
                                $Item = $this->Request($Module['path'], "GET", array("offset"=>$i, "limit"=>$items_per_query));
                                $arrItem = json_decode($Item);
                                $Results = $arrItem->results;
                                foreach($Results as $Result)
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