<?php

require_once("/etc/apicollection/librenms_config.php");

$backuproot = "/var/backup/librenms";

require_once("../librenms.php");
$Class = new librenms\librenms\librenms($librenms_host, $librenms_token, $librenms_usessl);


mkdir ($backuproot."/devices", true);

$Devices = $Class->GetDevices();
foreach($Devices as $Device)
{
    file_put_contents("/var/backup/".$Device->id.".raw", print_r($Device, true));
}

?>