<?php
/*
 * 
 * EC2ResourceManagement
 * 
 */

class EC2_Volume {

	private $volumeId;
	private $size;
	private $snapshotId;
	private $availabilityZone;
	private $status;
	private $createTime;
	private $attachmentCollection;
			
	
	public function __construct($xml=null)
	{
		if (isset($xml))
		{
			$x = new SimpleXMLElement($xml);
			$this->setVolumeId($x->volumeId);
			$this->setSize($x->size);
			$this->setAvailabilityZone($x->availabilityZone);
			$this->setStatus($x->status);
			$this->setCreateTime($x->createTime);
			$this->setAttachmentCollection(EC2_Attachment::ParseDescribeAttachemntsResponse($x->asXML()));
		}
	}
	
	public function getAttachmentCollection() {
		return $this->attachmentCollection;
	}
	
	public function getAvailabilityZone() {
		return $this->availabilityZone;
	}
	
	public function getCreateTime() {
		return $this->createTime;
	}
	
	public function getSize() {
		return $this->size;
	}
	
	public function getSnapshotId() {
		return $this->snapshotId;
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function getVolumeId() {
		return $this->volumeId;
	}
	
	public function setAttachmentCollection($attachmentCollection) {
		$this->attachmentCollection = $attachmentCollection;
	}
	
	public function setAvailabilityZone($availabilityZone) {
		$this->availabilityZone = $availabilityZone;
	}
	
	public function setCreateTime($createTime) {
		$this->createTime = $createTime;
	}
	
	public function setSize($size) {
		$this->size = $size;
	}
	
	public function setSnapshotId($snapshotId) {
		$this->snapshotId = $snapshotId;
	}
	
	public function setStatus($status) {
		$this->status = $status;
	}
	
	public function setVolumeId($volumeId) {
		$this->volumeId = $volumeId;
	}

	public static function ParseDescribeVolumesResponse($xml)
	{
		$vols = array();
		$x = new SimpleXMLElement($xml);
		foreach($x->volumeSet->item as $volume)
		{
			array_push($vols, new EC2_Volume($volume->asXML()));
		}
		return $vols;		
	}

	public static function ParseCreateVolumeResponse($xml)
	{
		$x = new SimpleXMLElement($xml);
		$vol =  new EC2_Volume();	
		$vol->setVolumeId($x->volumeId);
		$vol->setSize(($x->size));
		$vol->setSnapshotId($x->snapshotId);
		$vol->setStatus($x->status);
		$vol->setCreateTime($x->createTime);
		return $vol;	
	}
	
	
}

?>