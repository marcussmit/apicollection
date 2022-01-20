<?php

require_once("API.php");

class openprovider
{

	private $username = "";
	private $password = "";

	private $API = null;

	function __construct($username, $passwordhash)
	{
		$this->username = $username;
		$this->password = $passwordhash;
		$this->API = new OP_API ('https://api.openprovider.eu');

	}

	function __destruct()
	{
	}

	function BuildRequest($command, $authenticate = true)
	{
		$Parameters[''] = null;
		
		$REQ = new OP_Request();
		$REQ->setCommand($command);
		$REQ->setArgs(array(
			'domains' => array(
				array('name' => 'openprovider', 'extension' => 'nl'),
       				array('name' => 'non-existing-domain', 'extension' => 'co.uk')
			)
		));

		// Authentication requested ?
		if ($authenticate === true)
			$REQ->setAuth(array('username' => $this->username, 'password' => $this->password));

		return $OP_Request;
	}
	
	function check_domain_free()
	{
		$this->API->checkDomainRequest();
	}


	function returncode_to_message($code, &$extended)
	{
		switch ($code)
		{
			case 999:
			{
				$extended = "Dit is en test";
				return false;
			}
			default:
			{
				$extended = "Unknown error code '$code'. Please contact the developers.";
				return true;
			}
		}
	}


	function FetchType($Type)
	{
		$RET = "";

	}

	public function TransferDomain($domain, $authCode, $dns, $handle)
	{
		// Split domain name and add as a parameter:
		$temp = explode(".", $domain);
		$Parameters['domain'] = array("name"=>$temp[0], "extension"=>$temp[1]);

		// Set the required handles:
		$Parameters['ownerHandle']     = "$handle";
		$Parameters['billingHandle']   = "$handle";
		$Parameters['adminHandle']     = "$handle";
		$Parameters['techHandle']      = "$handle";

		$Parameters['period']          = 1;
		$Parameters['authCode']        = $authCode;

		$Parameters['nsGroup']         = $dns['nsGroup'];
		$Parameters['nsTemplateGroup'] = $dns['nsTemplateGroup'];

		// Build the request:
		$Request = new OP_Request();
		$Request->setCommand("transferDomainRequest");
		$Request->setAuth(array('username' => $this->username, 'password' => $this->password));
		$Request->setArgs($Parameters);

		return $this->API->process($Request);
	}

	public function AddDnsRecord($domain, $host, $type, $value, $ttl = 900, $prio = 0)
	{

		// Step 1: Fetch the current zone:
		$Parameters = array();
		$Parameters['domain'] = $domain;

		$Request = new OP_Request();
		$Request->setCommand("retrieveZoneDnsRequest");
		$Request->setAuth(array('username' => $this->username, 'password' => $this->password));
		$Request->setArgs($Parameters);

		$Ret = $this->API->process($Request);

		// Step 2: Copy the record to a new request:
		$Parameters['records'] = $Ret->results;

		// Step 3: Update the zone:
		$Parameters = array();
		$Parameters['domain'] = $domain;

		$Request = new OP_Request();
		$Request->setCommand("updateZoneDnsRequest");
		$Request->setAuth(array('username' => $this->username, 'password' => $this->password));
		$Request->setArgs($Parameters);

		return $this->API->process($Request);
	}

	public function ListDomains()
	{
		$Request = $this->BuildRequest("searchDomainRequest", true);
		return $this->API->process($Request);
	}
}

?>
