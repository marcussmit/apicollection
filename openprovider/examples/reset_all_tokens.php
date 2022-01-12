<?php

require_once("../openprovider_config.php");
require_once("../openprovider_class.php");
$Class = new openprovider_class($openprovider_username, $openprovider_password);

// Fetch and store all domains:
$Domains = $Class->GetAllDomains();
foreach($Domains as $domainname)
{
    $Domain = $Class->GetDomain($domainname);
    mkdir("/var/backup/openprovider/domains", true)
    file_put_contents("/var/backup/openprovider/domains/$domainname/".$Domain['id'].".raw", print_r($Domain, true));
}
?>