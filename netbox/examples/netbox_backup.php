<?php

   require_once("../netbox_class.php");
   require_once("../netbox_config.php");

   $Netbox = new netbox_class($netbox_url, $netbox_token);

   // Define the basedir where the backups should be dumped:
   $backupdir = "/var/backup/netbox";

   // Get the modules that are reachable:
   $Modules = $Netbox->getModules();

    // Loop through the modules:
    foreach($Modules as $module)
    {
        // Create the directory:
        mkdir($backupdir.$module, 777, true)
        $offset = 0; $limit =50;
        do{
            print "Fetching IP-addresses from Netbox";

            $List = $Netbox->ipam_ipaddresses_list($offset, $limit);
            foreach($List->results as $Item)
            {
                print ".";
                file_put_contents($backupdir."/ipam/ip-addresses/".$item->id.".raw". print_r($Item, true));
            }

        }
    }
?>