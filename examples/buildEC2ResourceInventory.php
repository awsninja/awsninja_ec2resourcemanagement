#!/usr/bin/php -q
<?php

define('NINJA_BASEPATH', dirname(__FILE__) . '/../../');
require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2ManagementService.php');
//Will examine your private AMIs, Instances, Volumes and Snapshots and create the SimpleDB inventory.

$ec2Svc = new EC2ManagementService();

$ec2 = new EC2Service();

$descImgsResponse = $ec2->describeImages();
$images = EC2_Image::ParseDescribeImagesResponse($descImgsResponse);
$imgCt = count($images);

$imageObjs = array();
for($i=0; $i<$imgCt; $i++)
{
	$ec2Img = $images[$i];
	$imageName = prompt("What is the name of image {$ec2Img->getImageId()}?  (e.g. WebServer, Database)");
	$img = new Image();
	$img->setItemName($imageName);
	$img->setImageId($ec2Img->getImageId());
	$img->save();
	$iid = (string)$img->getImageId(); //not sure why it's not a string already.  oh well.
	$imageObjs[$iid] = $img;
}

$descInstResponse = $ec2->describeInstances();
$instances = EC2_Instance::ParseDescribeInstancesResponse($descInstResponse);
$instCt = count($instances);
$instanceObjs = array();
for($i=0;$i<$instCt; $i++)
{
	$ec2Inst = $instances[$i];
	//see if we know the image.  If not we will skip
	foreach(array_keys($imageObjs) as $imgId)
	{
		if ($ec2Inst->getImageId() == $imgId)
		{
			$instId  = (string)$ec2Inst->getInstanceId();
			$role = prompt("What is the role of instance {$instId}?  (e.g. Development, Staging, Production)");
			$inst = new Instance();
			$inst->setItemName("{$role}-01");
			$inst->setInstanceId($instId);
			$inst->save();
			$instanceObjs[(string)$inst->getInstanceId()] = $inst;
			break;  //break foreach
		}
	}
}


$descVolsResponse = $ec2->describeVolumes();
$volumes = EC2_Volume::ParseDescribeVolumesResponse($descVolsResponse);
$volCt = count($volumes);
for($i=0;$i<$volCt;$i++)
{
	$ec2Vol = $volumes[$i];
	$status = $ec2Vol->getStatus();
	
	if ($status == 'in-use')
	{
		$attchCol = $ec2Vol->getAttachmentCollection();
		$attch = $attchCol[0];  //currently a volume can only have one attachment
		$device = $attch->getDevice();
		if ($device == '/dev/sda1')
		{
			echo("Skipping EBS Boot Volume\n");
		}
		else
		{
			$instanceId = (string)$attch->getInstanceId();
			$instanceAttached = $instanceObjs[$instanceId];
			$volRole = prompt("What is the role of the volume {$ec2Vol->getVolumeId()} attached to the {$instanceAttached->getItemName()} instance?");
			$filePath = prompt("What is the full path that the {$volRole} volume on {$instanceAttached->getItemName()} instance?");
			$vol = new Volume();
			$vol->setItemName($instanceAttached->getItemName() . '-' . $volRole);
			$vol->setVolumeId($ec2Vol->getVolumeId());
			$vol->setInstanceItemName($instanceAttached->getItemName());
			$vol->setRole($volRole);
			$vol->setFileSystemPath($filePath);
			$vol->setDeviceName($attch->getDevice());
			$vol->save();
		}		
	}
	else
	{
		echo("Skipping unattached volume {$ec2Vol->getVolumeId()}\n");
	}
}






function prompt($text)
{
	$response = '';
	if (strlen($response) == 0)
	{
		echo($text . "\n");
		$response  = trim(fgets(STDIN));
	}
	echo("Got it!\n");
	return $response;
}




?>