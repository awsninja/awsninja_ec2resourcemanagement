#!/usr/bin/php -q
<?php

define('NINJA_BASEPATH', dirname(__FILE__) . '/../../');
require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2ManagementService.php');

//run every hour to make backups

$ec2Svc = new EC2ManagementService();

$vols = Volume::getFromQuery('SELECT * FROM Volume');


$volCt = count($vols);
for($i=0; $i<$volCt; $i++)
{
	$vol = $vols[$i];
	$ec2Svc->makeSnapshot($vol, 'Backup');
}


?>