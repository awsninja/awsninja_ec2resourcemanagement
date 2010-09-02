#!/usr/bin/php -q
<?php

define('NINJA_BASEPATH', dirname(__FILE__) . '/../../');
require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2ManagementService.php');

$ec2Svc = new EC2ManagementService();
$ec2 = new EC2Service();

$instanceId = $ec2Svc->getCurrentInstanceId();

$instance = Instance::getSingleItemFromQuery("SELECT * FROM Instance WHERE instanceId = '$instanceId'");
$volQry = "SELECT * FROM Volume WHERE instanceItemName = '{$instance->getItemName()}'";
$volumes = Volume::getFromQuery($volQry);



foreach($volumes as $vol)
{
	$ec2VolId = $vol->getVolumeId();		
	$res = $ec2->describeVolumes($ec2VolId);
	$volAry = EC2_Volume::ParseDescribeVolumesResponse($res);
	$eVol = $volAry[0];
	if ("{$eVol->getStatus()}" == 'available')
	{
		$res = $ec2->attachVolume($vol->getVolumeId(), $instanceId, $vol->getDeviceName());
	}
	else
	{
		
	}
}



//keep checking status, then mount
$readyToAttach = false;
while(!$readyToAttach)
{
	$foundUnavailable = false;
	foreach($volumes as $vol)
	{
		$ec2VolId = $vol->getVolumeId();
		$newVolRes = $ec2->describeVolumes($ec2VolId);
		$newVols = EC2_Volume::ParseDescribeVolumesResponse($newVolRes);
		$newVol = $newVols[0];
		if ($newVol->getStatus() == 'in-use' )
		{
			//do nothing
		}
		else
		{
			$foundUnavailable = true;
		}
	}
	if (!$foundUnavailable)
	{
		$readyToAttach = true;
	}
	else
	{
		sleep(5);
	}
}	


foreach($volumes as $vol)
{
	if (!file_exists($vol->getFileSystemPath()))
	{
		mkdir($vol->getFileSystemPath());
	}
	$mountStr = "exec mount " . $vol->getDeviceName() . ' ' . $vol->getFileSystemPath();
	exec($mountStr);

}






?>