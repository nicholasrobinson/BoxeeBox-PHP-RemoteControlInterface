<?php
/*
 *  File:			simplediscovery.php
 *  Description:	Simple SDK web/command line example with automatic discovery
 *  Author:			Nicholas Robinson 11/26/2011
 */
 
# Include BoxeeBoxPHPRCI class
require_once('../BoxeeBoxPHPRCI.class.php');

# Initialize BBPHPRCI SDK
$bbphprci = new BoxeeBoxPHPRCI();

# Attempt automatic discovery
$discovered = $bbphprci->discover();
# If a BoxeeBox is dicovered
if ($discovered)
{
	# Extract address and port for display
	list($serverAddress, $serverPort) = $discovered;
	# Output discovery results
	echo 'Discovered ' . $serverAddress . ':' . $serverPort . "\n";
	# Set Volume to 75
	echo 'Changing Volume to 75:' . $bbphprci->SetVolume(75) . "\n";
}
# Otherwise inform of failed discovery
else
{
	echo 'Discovery FAILED';
}

?>