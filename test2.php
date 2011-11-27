<?php

require_once('Connections/UDPXML.class.php');

$udpServer = new UDPXML();
$udpServer->discover();
echo $udpServer->getServerAddress() . "\n";
echo $udpServer->getServerHostname() . "\n";
echo $udpServer->getServerPort() . "\n";

# Define constants
define('PROTOCOL', 'http');
define('API_END_POINT', '/xbmcCmds/xbmcHttp?command=');

# Define a list of commands
$commands = array(
						'SendKey(270)',
						'SendKey(272)',
						'SendKey(272)'
				);

# Loop through each command
foreach ($commands as $command)
{
	# Initialize curl handler
	$curlHandler = curl_init();

	# Configure curl target
	curl_setopt ($curlHandler, CURLOPT_URL, PROTOCOL . '://' . $udpServer->getServerAddress() . ':' . $udpServer->getServerPort() . API_END_POINT . $command);

	# Execute curl request (supressing output)
	ob_start();
	$response = curl_exec($curlHandler);
	ob_end_clean();

	# Close curl handler
	curl_close($curlHandler);
}

?>