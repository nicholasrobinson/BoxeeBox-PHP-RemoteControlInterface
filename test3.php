<?php

require_once('Connections/HTTPGET.class.php');
require_once('Services/RemoteControlInterface.class.php');

# Hardcode server for development
$serverAddress = '10.10.10.110';
$serverPort = 8800;

# Generate API command
$rciAPI = new RemoteControlInterface();
$command = $rciAPI->GetVolume();

$httpGetTransaction = new HTTPGET($serverAddress, $serverPort, $command);
$apiResponse = $httpGetTransaction->execute();

echo $apiResponse;
?>