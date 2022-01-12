<?php
         
class netbox_class
{
 
        var $token = "";
        var $url = "";
 
        public function __construct($url, $token)
        {
                $this->token = $token;
                $this->url = $url;
        }
 
        private function request($path, $mode = "GET", $parameters = null)
        {
 
                $url = $this->url.$path;
                $parms = "";
 
                if ($mode == "GET")
                {
                        $parms = "?";
                        foreach($parameters as $parm=>$val) { $parms .= $parm."=".urlencode($val)."&"; }
                        $url .= $parms;
                }
 
                $hCurl = curl_init($url);
                curl_setopt($hCurl, CURLOPT_HTTPHEADER, array("Authorization: Token ".$this->token));
                curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, "1");
                curl_setopt($hCurl, CURLOPT_SSL_VERIFYPEER, "0");
                return curl_exec($hCurl);
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
                $Modues = $this->GetModules();

                foreach($Modules as $objecttype)
                { 
                        $fullpath = $BaseDir . "/" . $objecttype['path'];
                        print "Maak $fullpath.\r\n";
                        if (!file_exists($fullpath)) mkdir ($fullpath, 777, true);
                        // Fetch one object, to retrieve the counter:
                        $Item = $this->request("/".$objecttype."/", "GET", array("offset"=>0, "limit"=>1));
                        $Decoded = json_decode($Item, true);
                        $count = $Decoded['count'];
                        print "Object type $objecttype heeft $count items.\r\n";
 
                        // Fetch all items:
                        $items_per_query = 1;
                        for($i = 0; $i < $count; $i += $items_per_query)
                        {
                                print "Ophalen items $i tot maximaal ".($i + $items_per_query -1)."\r\n";
                                $Item = $this->request("/".$objecttype."/", "GET", array("offset"=>$i, "limit"=>1));
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
                $list = $this->request("/ipam/aggregates/", "GET");
                $list = json_decode($list);
                return $list['results'];
        }
 
        /// Section IPAM / IP-ADDRESSES
        public function ipam_ipaddresses_list($offset, $limit)
        {
                $Parameters['offset'] = $offset;
                $Parameters['limit']  = $limit;
                $list = $this->request("/ipam/ip-addresses/", "GET", $Parameters);
                $list = json_decode($list);
                return $list['results'];
        }
 
}
 
?>