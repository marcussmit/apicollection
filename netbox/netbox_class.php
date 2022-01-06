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
 
        // Create backup:
        public function CreateBackup($BaseDir)
        {
                $objecttypes[] = "circuits/circuit-terminations";
                $objecttypes[] = "circuits/circuit-types";
                $objecttypes[] = "circuits/provider-networks";
                $objecttypes[] = "dcim/console-server-ports";
                $objecttypes[] = "dcim/devices";
                $objecttypes[] = "dcim/regions";
                $objecttypes[] = "dcim/site-groups";
                $objecttypes[] = "dcim/sites";
                $objecttypes[] = "dcim/virtual-chassis";
                $objecttypes[] = "extras/config-contexts";
                $objecttypes[] = "extras/custom-links";
                $objecttypes[] = "ipam/asns";
                $objecttypes[] = "ipam/fhrp-group-assignments";
                $objecttypes[] = "ipam/ip-addresses";
 
                foreach($objecttypes as $objecttype)
                {
 
                        $fullpath = $BaseDir . "/" . $objecttype;
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