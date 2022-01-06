#!/bin/php
<?php

require_once(dirname(__FILE__)."/../directadmin.class.php");
$DA = new directadmin("ssl://$directadmin_host", "$directadmin_username", "$directadmin_password");
print_r($DA->getServerStats());

?>
