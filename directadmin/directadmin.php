<?php

require_once("httpsocket.php");

namespace directadmin\directadmin;

class directadmin
{

	var $hostname = "";
	var $port = 2222;
	var $ssl = false;
	var $username="";
	var $password="";

	public function __construct($hostname, $username, $password, $port = 2222, $ssl=true)
	{
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		$this->port = $port;
		$this->ssl = $ssl;
	}

	private function SendQuery($cmd, $Parameters = array(), $method = "POST")
	{
		$url = ($this->ssl) ? "ssl://".$this->url : $this->url;

		$sock = new HTTPSocket;
		$sock->connect($url, 2222);
		$sock->set_login($this->username, $this->password);

		$sock->set_method = $method;

		$sock->query("/$cmd", $Parameters);
		return $sock->fetch_parsed_body();
	}

	public function GetDomainsForUser($username)
	{
		return null;
	}

	public function AddDomain($domain)
	{
		$Parameters['action'] = "create";
		$Parameters['domain'] = $domain;
		$Parameters['ubandwidth'] = "unlimited";
		$Parameters['ssl'] = "ON";

		return $this->SendQuery("CMD_API_DOMAIN", $Parameters, "POST");
	}

	public function SetCatchall($domain, $forwarder)
	{
		$Parameters['domain'] = $domain;
		$Parameters['catch']  = "address";
		$Parameters['value']  = $forwarder;

		$Parameters['update'] = "Update";

		return $this->SendQuery("CMD_API_EMAIL_CATCH_ALL", $Parameters, "POST");
	}

	public function SetDomainSSL($domain, $method, $forcessl=null)
	{
		// forcessl = "yes" of niet zetten.
		// Method = "directory" of "symlink"

		$Parameters['domain']    = $domain;
		$Parameters['action']    = "private_html";
		$Parameters['force_ssl'] = $forcessl;
		$Parameters['val']       = $method;
		
		if ($forcessl == "yes") $Parameters['force_ssl'] = "yes";

		$Parameters['action'] = "save";
		return $this->SendQuery("CMD_API_SSL", $Parameters, "POST");
	}

	public function GetDomainSSL($domain)
	{
		$Parameters['domain'] = $domain;
		//$Parameters['type']   = "server";
		//$Parameters['action'] = "save";
		return $this->SendQuery("CMD_API_SSL", $Parameters, "GET");
	}

	public function GetProtectedDir($domain, $directory)
	{
		$Parameters['domain'] = $domain;
		$Parameters['directory'] = $directory;

		return $this->SendQuery("CMD_API_PROTECTED_DIRECTORIES", $Parameters, "GET");
	}

	public function GetServerStats()
	{
		return $this->SendQuery("CMD_API_ADMIN_STATS", null, "GET");
	}

	public function GetAllUsers()
	{
		return $this->SendQuery("CMD_API_SHOW_ALL_USERS", null, "GET");
	}

	public function AddIP($domain, $ipaddress)
	{
		$Parameters['add']    = "1";
		$Parameters['action'] = "multi_ip";
		$Parameters['domain'] = $domain;
		$Parameters['ip']     = $ipaddress;
		$Parameters['dns']    = "yes";
		return $this->SendQuery("CMD_API_DOMAIN", $Parameters, "POST");
	}



}

?>
