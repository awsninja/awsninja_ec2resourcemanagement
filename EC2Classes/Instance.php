<?php

class EC2_Instance {
	private $instanceId;
	private $imageId;
	private $instanceState;
	private $privateDnsName;
	private $dnsName;
	private $reason;
	private $amiLaunchIndex;
	private $instanceType;
	private $launchTime;
//	private $placement;
	private $availabilityZone;
	private $kernelId;
	private $ramDiskId;
	private $securityGroup;
	
	public function __construct()
	{
	}
	
	
	public function getAvailabilityZone()
	{
		return $this->availabilityZone;
	}

	public function setAvailabilityZone($availabilityZone)
	{
		$this->availabilityZone = $availabilityZone;
	}

	public function getPrivateIpAddress()
	{
		return gethostbyname($this->getPrivateDnsName());
	}
	
	
	public function getPublicIPAddress()
	{
		return gethostbyname($this->getDnsName());
	}
	
	public function getSecurityGroup()
	{
		return $this->securityGroup;
	}
	public function setSecurityGroup($securityGroup)
	{
		$this->securityGroup = $securityGroup;
	}
	
	public function getAmiLaunchIndex() {
		return $this->amiLaunchIndex;
	}
	
	public function getDnsName() {
		return $this->dnsName;
	}
	
	public function getImageId() {
		return $this->imageId;
	}
	
	public function getInstanceId() {
		return $this->instanceId;
	}
	
	public function getInstanceState() {
		return $this->instanceState;
	}
	
	public function getInstanceType() {
		return $this->instanceType;
	}
	
	public function getKernelId() {
		return $this->kernelId;
	}
	
	public function getLaunchTime() {
		return $this->launchTime;
	}
	
//	public function getPlacement() {
//		return $this->placement;
//	}
	
	public function getPrivateDnsName() {
		return $this->privateDnsName;
	}
	
	public function getRamDiskId() {
		return $this->ramDiskId;
	}
	
	public function getReason() {
		return $this->reason;
	}
	
	public function setAmiLaunchIndex($amiLaunchIndex) {
		$this->amiLaunchIndex = $amiLaunchIndex;
	}
	
	public function setDnsName($dnsName) {
		$this->dnsName = $dnsName;
	}
	
	public function setImageId($imageId) {
		$this->imageId = $imageId;
	}
	
	public function setInstanceId($instanceId) {
		$this->instanceId = $instanceId;
	}
	
	public function setInstanceState($instanceState) {
		$this->instanceState = $instanceState;
	}
	
	public function setInstanceType($instanceType) {
		$this->instanceType = $instanceType;
	}
	
	public function setKernelId($kernelId) {
		$this->kernelId = $kernelId;
	}
	
	public function setLaunchTime($launchTime) {
		$this->launchTime = $launchTime;
	}
	
//	public function setPlacement($placement) {
//		$this->placement = $placement;
//	}
	
	public function setPrivateDnsName($privateDnsName) {
		$this->privateDnsName = $privateDnsName;
	}
	
	
	public function setRamDiskId($ramDiskId) {
		$this->ramDiskId = $ramDiskId;
	}
	
	public function setReason($reason) {
		$this->reason = $reason;
	}

	public static function ParseDescribeInstancesResponse($xml)
	{
		$x = new SimpleXMLElement($xml);
		$res = array();
		foreach($x->reservationSet->item as $item)
		{
			$securityGroup = $item->groupSet->item->groupId;
			foreach($item->instancesSet->item as $inst)
			{		
				$instance = new EC2_INSTANCE();
				$instance->setInstanceId($inst->instanceId);
				$instance->setImageId($inst->imageId);
				
				$instanceState = array(
					'code'=>$inst->instanceState->code,
					'name'=>$inst->instanceState->name
				);
				$instance->setInstanceState($instanceState);
				$instance->setPrivateDnsName($inst->privateDnsName);
				$instance->setDnsName($inst->dnsName);
				$instance->setReason($inst->reason);
				$instance->setAmiLaunchIndex($inst->amiLaunchIndex);
				$instance->setInstanceType($inst->instanceType);
				$instance->setLaunchTime($inst->launchTime);
				$instance->setAvailabilityZone($inst->placement->availabilityZone);
//				$instance->setPlacement($inst->placement);
				$instance->setKernelId($inst->kernelId[0]);
				$instance->setRamDiskId($inst->ramDiskId);
				$instance->setSecurityGroup($inst->securityGroup);
				array_push($res, $instance);
			}
		}
		return $res;		
	}
	
	public static function ParseRunInstancesResponse($xml)
	{
		$x = new SimpleXMLElement($xml);
		$res = array();
//		foreach($x->reservationSet->item as $item)
//		{
			$securityGroup = $x->groupSet->item->groupId;
			foreach($x->instancesSet->item as $inst)
			{		
				$instance = new PDFTO_EC2_INSTANCE();
				$instance->setInstanceId($inst->instanceId);
				$instance->setImageId($inst->imageId);
				
				$instanceState = array(
					'code'=>$inst->instanceState->code,
					'name'=>$inst->instanceState->name
				);
				$instance->setInstanceState($instanceState);
				$instance->setPrivateDnsName($inst->privateDnsName);
				$instance->setDnsName($inst->dnsName);
				$instance->setReason($inst->reason);
				$instance->setAmiLaunchIndex($inst->amiLaunchIndex);
				$instance->setInstanceType($inst->instanceType);
				$instance->setLaunchTime($inst->launchTime);
				$instance->setAvailabilityZone($inst->placement->availabilityZone);
				
//				$instance->setPlacement($inst->placement);
				$instance->setKernelId($inst->kernelId[0]);
				$instance->setRamDiskId($inst->ramDiskId);
				$instance->setSecurityGroup($inst->securityGroup);
				array_push($res, $instance);
//			}
		}
		return $res;		
	}
	
	
	
	
}

?>