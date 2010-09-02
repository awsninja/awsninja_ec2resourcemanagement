#!/usr/bin/php -q
<?php

define('NINJA_BASEPATH', dirname(__FILE__) . '/../../');
require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2ManagementService.php');

$ec2Svc = new EC2ManagementService();
$vol = new Volume('Development-01-data');   //The parameter must match an ItemName from the Volume domain on SimpleDb.
$ec2Svc->attachVolume($vol);


?>