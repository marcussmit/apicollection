<?php

require_once("API.php");

$username = "$openprovider_username";
$password = "$openprovider_password";

// Create a new API connection
$api = new OP_API ('https://api.openprovider.eu');

$request = new OP_Request();
 $request->setCommand('checkDomainRequest')
    ->setAuth(array('username' => $username, 'password' => $password))
    ->setArgs(array(
      'domains' => array(
          array('name' => 'openprovider', 'extension' => 'nl'),
          array('name' => 'non-existing-domain', 'extension' => 'co.uk')
        )
      )
    );
  $reply = $api->setDebug(1)->process($request);
  echo "Code: " . $reply->getFaultCode() . "\n";
  echo "Error: " . $reply->getFaultString() . "\n";
  echo "Value: " . print_r($reply->getValue(), true) . "\n";
  echo "\n---------------------------------------\n";

  echo "Finished example script\n\n";
