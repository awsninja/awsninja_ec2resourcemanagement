<?php

//require_once('EC2/EBSBlockDeviceType.php');

class EC2_BlockDeviceMappingType {
	
	private $deviceName;
	private $ebs;
	private $noDevice;
	private $virtualName;
	
	
	public function getDeviceName() {
		return $this->deviceName;
	}

	public function getEbs() {
		return $this->ebs;
	}

	public function getNoDevice() {
		return $this->noDevice;
	}

	public function getVirtualName() {
		return $this->virtualName;
	}

	public function setDeviceName($deviceName) {
		$this->deviceName = $deviceName;
	}

	public function setEbs($ebs) {
		$this->ebs = $ebs;
	}

	public function setNoDevice($noDevice) {
		$this->noDevice = $noDevice;
	}

	public function setVirtualName($virtualName) {
		$this->virtualName = $virtualName;
	}

	public static function parseDescribeBlockDeviceMappingTypeResponse($xml)
	{
		$x = new SimpleXMLElement($xml);
		$bdmt = new EC2_BlockDeviceMappingType();
		$bdmt->deviceName = $x->deviceName;
		$bdmt->ebs = EC2_EBSBlockDeviceType::parseDescribeEbsBlockDeviceTypeResponse($x->ebs->asXml());
		return $bdmt;
	}
	
	
	

}



?>