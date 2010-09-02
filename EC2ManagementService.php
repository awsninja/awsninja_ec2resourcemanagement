<?php
/**
 * EC2ResourceManagement Service
 * 
 * A PHP library for managing EC2 resouces.
 * 
 * @author Jay Muntz
 * 
 * Copyright 2010 Jay Muntz (http://www.awsninja.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * “Software”), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 *
 */

class EC2ManagementService {

	private $ec2;

  public function __construct()
  {
  	require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2Service.php');
		require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/config.php');
		require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/config.php');
		require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/classes/Instance.php');
		require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/classes/Snapshot.php');
		require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/classes/Volume.php');
		require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/classes/Image.php');
		$this->ec2 = new EC2Service();
  }
	
  
  /**
   *  Returns the instance-id of the EC2 instance that it is running on.
   */
	public function getCurrentInstanceId()
  {
  	$instanceId =  file_get_contents('http://169.254.169.254/2009-04-04/meta-data/instance-id');
  	if ($instanceId === false)
  	{
  		throw new Exception("Could not get current instance id.  Are you sure you're running on an Amazon EC2 instance?");
  	}
		return $instanceId;  	
  }
  
  /**
   * Creates a Snapshot from the Volume
   * 
   * @param Volume $vol The Volume object representing the EC2 Volume that you want to snapshot
   * @param string $role A string describing the Role of the snapshot in your application. (e.g. database, web root, image store, etc).
   */
  function makeSnapshot(Volume $vol, $role='Misc')
  {
  	echo("Create Snapshot\n");
  	$desc = "Backup of {$vol->getInstanceItemName() } - {$vol->getRole()} - {$vol->getVolumeId()}";
 		$createSnapshotRes = $this->ec2->createSnapshot($vol->getVolumeId(), $desc);
		$ec2Snap = EC2_Snapshot::ParseCreateSnapshotResponse($createSnapshotRes);
		echo("Snapshot id is {$ec2Snap->getSnapshotId()}\n");
		while($ec2Snap->getStatus() == 'pending')
		{
			echo("Snapshot status is {$ec2Snap->getStatus()}\n");
			sleep(2);
			$xml = $this->ec2->describeSnapshots($ec2Snap->getSnapshotId());
			$ec2Snaps = EC2_Snapshot::ParseDescribeSnapshotsResponse($xml);
			$ec2Snap = $ec2Snaps[(string)$ec2Snap->getSnapshotId()];
		}
		echo("Snapshot status is {$ec2Snap->getStatus()}\n");	
		$newSnap = new Snapshot($ec2Snap->getSnapshotId());
		$newSnap->setVolumeRole($vol->getRole());
		$newSnap->setDateCreated(time());
		$newSnap->setServerRole($vol->getRole());
		$newSnap->setSnapshotRole($role);
		$newSnap->save();
  	return $ec2Snap;
  }
  
	/**
	 * Creates an identical copy of a EC2 Volume and a corresponding record in the SimpleDB Volume domain.  It does this by creating a Snapshot, then creating a Volume from the Snapshot.
	 * @param Volume $vol -The Volume to be Snapshotted
	 * @param Instance $destInst  The Instance that the new Volume should be associated with. 
	 */
	function cloneVolume(Volume $vol, Instance $destInst)
	{
		$ec2Snap = $this->makeSnapshot($vol);
		$descInstXml = $this->ec2->describeInstances($destInst->getInstanceId());
		$ec2Insts = EC2_Instance::ParseDescribeInstancesResponse($descInstXml);
		$ec2Inst = $ec2Insts[0];
		echo("Creating Volume from snapshot\n");
		$cvXML = $this->ec2->createVolume($ec2Inst->getAvailabilityZone(), $ec2Snap->getVolumeSize(), $ec2Snap->getSnapshotId());
		$newVol = EC2_Volume::ParseCreateVolumeResponse($cvXML);
		echo("New Volume id is {$newVol->getVolumeId()}\n");
		$newSdbVol = new Volume("{$destInst->getItemName()}-{$vol->getRole()}"); //if this instance/role already exists, it it is updated
		echo("Copying Properties\n");
		//other parameters are copied
		$newSdbVol->setDeviceName($vol->getDeviceName());
		$newSdbVol->setVolumeId($newVol->getVolumeId());
		$newSdbVol->setFileSystemPath($vol->getFileSystemPath());
		$newSdbVol->setInstanceItemName($destInst->getItemName());
		$newSdbVol->setRole($vol->getRole());
		$newSdbVol->save();
		return $newSdbVol;
	}
  
  
  
  /**
   * Attach a Volume to an Instance.  The Instance is determined by the Volume's record in SimpleDb
   * @param Volume $vol The volume to attach.
   */
  public function attachVolume(Volume $vol)
  {
		$instanceId = $vol->getInstanceItemName();
  	echo("#$instanceId#\n");
		$inst = new Instance($instanceId);

		$inst = new Instance($instanceId);
  	$ec2instanceId = (string)$inst->getInstanceId();
  	
  	echo("Attaching to {$vol->getDeviceName()}\n");
  	$this->ec2->attachVolume($vol->getVolumeId(), $ec2instanceId, $vol->getDeviceName());

		$ec2Vols = EC2_Volume::ParseDescribeVolumesResponse($this->ec2->describeVolumes($vol->getVolumeId()));
  	$ec2Vol = $ec2Vols[0];
  	$status = $ec2Vol->getStatus();
  	while($status != 'in-use')
  	{
			echo("Status is $status. Sleeping 5 seconds.\n");
  		sleep(5);
	  	$ec2Vols = EC2_Volume::ParseDescribeVolumesResponse($this->ec2->describeVolumes($vol->getVolumeId()));
	  	$ec2Vol = $ec2Vols[0];
	  	$status = $ec2Vol->getStatus();
  	}

  	echo("Attached!\n");
  	echo("Waiting five seconds.\n");
  	sleep(5);
  	return true;  		
  }
  
  /**
   * Launch an single EC2 Instance
   * @param Image $img The Image that you want to launch
   * @param string $role The Role of instance. For instance "Development-Web" if the instance is your Development Web Server
   * @param string $keyPairName The keypair name that you will use to log into your instance
   * @param array $config An array of additional configuration options
   */
	public function launchInstance(Image $img, $role, $keyPairName, $config=array(
		'SecurityGroup'=>'default',
		'InstanceType'=>'m1.small',
		'AvailabilityZone'=>'us-east-1d',
		'SpotMaxPrice'=>0.9,
		'Spot'=>true
	))
	{
		if (isset($config['Spot']) && $config['Spot'])
		{
			$serverSpotRes = $this->ec2->requestSpotInstances($config['SpotMaxPrice'], $img->getImageId(), $config['SecurityGroup'], $config['InstanceType'], $config['AvailabilityZone'], $keyPairName);
			$serverSpotResx = new SimpleXMLElement($serverSpotRes);
			$spotInstanceRequestId =  $serverSpotResx->spotInstanceRequestSet->item->spotInstanceRequestId;
			$state = 'new';
			while($state != 'active')
			{
				echo("Spot request state is $state.  Wait 10 seconds.\n");
				sleep(10);
				$res = $this->ec2->describeSpotInstanceRequests($spotInstanceRequestId);
				$xml = new SimpleXMLElement($res);
				$state = $xml->spotInstanceRequestSet->item->state;
			}
			$instanceId = $xml->spotInstanceRequestSet->item->instanceId;
			echo("$instanceId\n");
			$sdbInstance = new Instance($role . '-01');
			$sdbInstance->setInstanceId($instanceId);
			$sdbInstance->save();
		}
		else
		{
			echo("Starting regular instance\n");
			$instRespnonseXml = $this->ec2->runInstances($dmc->getValue(), 1, 1, $keyPairName, $config['SecurityGroup'], null, null,null, $config['InstanceType'], $config['AvailabilityZone'], null, null, null, null,null);
	    $instAry = EC2_Instance::ParseRunInstancesResponse($instRespnonseXml);
			$inst = $instAry[0];
			$instanceId = $xml->spotInstanceRequestSet->item->instanceId;
			$sdbInstance = new Instance($img->getItemName() . '-01');
			$sdbInstance->setInstanceId($instanceId);
		}
		echo("Our instance is launching.\n");
		echo("Wait 60 seconds to make sure it's started\n");
		sleep(60);
		return true;
	}
		
	private function parseDescribeVolumesResponse($xml)
	{
		throw new Exception('Not finished');
		$vols = array();
		$x = new SimpleXMLElement($xml);
		$res = array();
		foreach($x->volumeSet->item as $volume)
		{
			$r = array();
			print_r($volume);
			exit;			
			//array_push($vols, new PDFTO_EC2_Volume($volume->asXML()));
		}
		return $vols;		
	}
	
}


?>