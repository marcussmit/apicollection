<?php

$backuproot = "/var/backup/librenms";

require_once("../librenms.php");
$Class = new librenms\librenms\librenms.php;

mkdir ($backuproot."/devices", true);

$Devices = $Class->GetDevices();
foreach($Devices as $Device)
{
    file_put_contents("/var/backup/".$Device->id.".raw", print_r($Device, true));
}

?>