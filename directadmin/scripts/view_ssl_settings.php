#!/bin/php
<?php

# This file accepts two parameters:
# 1. userid to logon with
# 2. domain to show its ssl settings.

if ($argc != 3)
{
	die("Usage: ".$argv[0]." <userid> <domain>");
}


// Request password for this user:
print "Geef het Directadmin wachtwoord voor ".$argv[1].": ";
$password = fgets(STDIN);

// Load directadmin class
require_once(dirname(__FILE__)."/../directadmin.class.php");
$DA = new directadmin("ssl://$directadmin_host", "$directadmin_user", $password);

print_r($DA->getDomainSSL($argv[2]));

?>
