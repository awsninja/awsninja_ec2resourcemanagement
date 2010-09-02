<?php

class EC2_Attachment {

	private $attachTime;
	private $device;
	private $instanceId;
	private $status;
	private $volumeId;
	
	
	public function __construct($xml)
	{
		$x = new SimpleXMLElement($xml);
		$this->setAttachTime($x->attachTime);
		$this->setDevice($x->device);
		$this->setInstanceId($x->instanceId);
		$this->setStatus($x->status);
		$this->setVolumeId($x->volumeId);
	}
	
	
	
	
	public function getAttachTime() {
		return $this->attachTime;
	}
	
	public function getDevice() {
		return $this->device;
	}
	
	public function getInstanceId() {
		return $this->instanceId;
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function getVolumeId() {
		return $this->volumeId;
	}
	
	public function setAttachTime($attachTime) {
		$this->attachTime = $attachTime;
	}
	
	public function setDevice($device) {
		$this->device = $device;
	}
	
	public function setInstanceId($instanceId) {
		$this->instanceId = $instanceId;
	}
	
	public function setStatus($status) {
		$this->status = $status;
	}
	
	public function setVolumeId($volumeId) {
		$this->volumeId = $volumeId;
	}

	public static function ParseDescribeAttachemntsResponse($xml)
	{
		$attchments = array();
		$x = new SimpleXMLElement($xml);
		foreach($x->attachmentSet->item as $attachment)
		{
			array_push($attchments, new EC2_Attachment($attachment->asXML()));
		}
		return $attchments;		
		
	}
}

?>