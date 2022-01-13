<?php

require_once("OpenProvider.php");

// Setup the class:
$OP = new OpenProvider("$openprovider_username", "$openprovider_password");

// Test: transfer a domain to us:
print_r($OP->TransferDomain("$domainname", "$token"));

?>