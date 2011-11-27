<?php
require_once('BoxeeBoxPHPRCI.class.php');
$bbphprci = new BoxeeBoxPHPRCI('10.10.10.110');
echo ($bbphprci->SetVolume(80));
echo ($bbphprci->getVolume());
?>