<?php
/*
 *  File:			simple.php
 *  Description:	Simple SDK web/command line example
 *  Author:			Nicholas Robinson 11/26/2011
 */
 
# Include BoxeeBoxPHPRCI class
require_once('../BoxeeBoxPHPRCI.class.php');

# Connect to BoxeeBox
$bbphprci = new BoxeeBoxPHPRCI('boxeebox');

# Set Volume
echo 'Changing Volume to 75:' . $bbphprci->SetVolume(75) . '<br />';
?>