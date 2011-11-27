<?php
/*
 *  File:			simpleparse.php
 *  Description:	Simple SDK web/command line example with parsing
 *  Author:			Nicholas Robinson 11/26/2011
 */
 
# Define volume chage
define('DV', -10);
 
# Include BoxeeBoxPHPRCI class
require_once('../BoxeeBoxPHPRCI.class.php');

# Connect to BoxeeBox
$bbphprci = new BoxeeBoxPHPRCI('boxeebox');

# Get raw starting volume output
$rawResult = $bbphprci->getVolume();

# Parse for starting volume percentage
preg_match('/\<html\>\n<li\>(.*)\<\/html\>/', $rawResult, $matches);
$startingVolume = (int) $matches[1];

# Output starting volume
echo 'Starting Volume: ' . $startingVolume . '<br />';

# Change volume by DV
echo 'Changing Volume by ' . DV . ':' . $bbphprci->SetVolume($startingVolume + DV) . '<br />';

# Verify new volume
echo 'New Volume:' . $bbphprci->getVolume() . '<br />';
?>