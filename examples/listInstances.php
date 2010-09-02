#!/usr/bin/php -q
<?php

define('NINJA_BASEPATH', dirname(__FILE__) . '/../../');
require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2ManagementService.php');


$ec2Svc = new EC2ManagementService();
$insts = Instance::getFromQuery('SELECT * FROM Instance');

foreach($insts as $inst)
{
	echo("Instance: {$inst->getItemName()} {$inst->getInstanceId()}\n");
}


?>