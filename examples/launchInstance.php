#!/usr/bin/php -q
<?php

define('NINJA_BASEPATH', dirname(__FILE__) . '/../../');
require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2ManagementService.php');

$ec2Svc = new EC2ManagementService();

$img = new Image('WebServer');
$role = 'Staging';
$ec2Svc->launchInstance($img, $role, 'awsninja');
$role = 'Development';
$ec2Svc->launchInstance($img, $role, 'awsninja');


?>