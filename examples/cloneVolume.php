#!/usr/bin/php -q
<?php

define('NINJA_BASEPATH', dirname(__FILE__) . '/../../');
require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2ManagementService.php');

$ec2Svc = new EC2ManagementService();
$vol = new Volume('Staging-01-app'); //The parameter must match an ItemName from the Volume domain on SimpleDb.
$destInst = new Instance('Development-01'); //The parameter must match an ItemName from the Instance domain on SimpleDb.
$ec2Svc->cloneVolume($vol, $destInst);



?>