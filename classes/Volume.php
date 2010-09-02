<?php

/*
 * 
 * EC2ResourceManagement
 * 
 * Volume Class - Keeps track of your EBS Volumes
 * 
 */

require_once('EC2Domain.php');


class Volume extends EC2Domain {
	
	private $deviceName;
	private $volumeId;
	private $instanceItemName;
	private $role;
	private $fileSystemPath;	
	
	public function getInstanceItemName()
	{
		return $this->instanceItemName;
	}

	public function setInstanceItemName($instanceItemName)
	{
		$this->instanceItemName = $instanceItemName;
	}

	public function getDeviceName() {
		return $this->deviceName;
	}

	public function getVolumeId() {
		return $this->volumeId;
	}

	public function getRole() {
		return $this->role;
	}

	public function getFileSystemPath() {
		return $this->fileSystemPath;
	}

	public function setDeviceName($deviceName) {
		$this->deviceName = $deviceName;
	}

	public function setVolumeId($volumeId) {
		$this->volumeId = $volumeId;
	}

	public function setRole($role) {
		$this->role = $role;
	}

	public function setFileSystemPath($fileSystemPath) {
		$this->fileSystemPath = $fileSystemPath;
	}
	

	public static function getFieldStructure()
	{
		$res = array(
			'deviceName'=>array(
				'PHPName'=>'DeviceName'
			),
			'volumeId'=>array(
				'PHPName'=>'VolumeId'
			),
			'instanceItemName'=>array(
				'PHPName'=>'InstanceItemName'
			),
			'role'=>array(
				'PHPName'=>'Role'
			),
			'fileSystemPath'=>array(
				'PHPName'=>'FileSystemPath'
			)
		);
		return $res;
	}
	
	public static function getDomainName()
	{
		return 'Volume';
	}

	public static function getSingleItemFromQuery($qry)
	{
		return parent::getSingleItemFromQuery(self::getDomainName(), $qry);
	}
	
	public static function getFromQuery($qry)
	{
		return parent::getFromQuery(self::getDomainName(), $qry);
	}
	
}





?>