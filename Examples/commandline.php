#!/usr/bin/php
<?php
/*
 *  File:			commandline.php
 *  Description:	SDK commandline example
 *  Author:			Nicholas Robinson 11/27/2011
 */
 
# Parse command line arguments
$args = getopt('h:c:');
if (!isset($args['h']) || !isset($args['c']))
{
	echo "Usage: ./commandline.php -h <hostname> -c command \n";
	echo "Example: ./commandline.php -h boxeebox -c GetVolume\n";
	exit(1);
}
$hostname = $args['h'];
$command = $args['c'];

# Include BoxeeBoxPHPRCI class
require_once('../BoxeeBoxPHPRCI.class.php');

# Connect to BoxeeBox
$bb = new BoxeeBoxPHPRCI($hostname);

# Issue GetVolume Query
echo "GetVolume():\n";
echo $bb->$command() . "\n";

?>