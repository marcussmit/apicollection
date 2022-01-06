<?php

require_once("directadmin.class.php");

$DA = new directadmin("ssl://$directadmin_host", "$directadmin_username", "$directadmin_password");


function CreateDomain($domain)
{
	global $DA;

	print "Creating and setting up the domain $domain\r\n";

	// Add the domain:
	$ret = $DA->AddDomain($domain);
        if ($ret['error'] != 0) print "An error has occurred: ".$ret['text']. " (".$ret['details'].")\r\n";

	// Voeg het ipv6 adres toe:
	print "Add IPv6 address\r\n";
	$ret = $DA->AddIP($domain, "## FullIPv6 Address ##");
	print_r($ret);

	// Stel een catchall adres in:
	$ret = $DA->SetCatchall($domain, "$catchall");
	if ($ret['error'] != 0) print "An error has occurred: ".$ret['text']. " (".$ret['details'].")\r\n";

	// Stel de https redirect in:
	$ret = $DA->SetDomainSSL($domain, "server", "yes", "yes");
	if ($ret['server'] != "yes") print "An error has occurred.\r\n";

	print "Completed.\r\n\r\n";
}