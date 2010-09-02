<?php

class EC2_EbsBlockDeviceType {
	
	private $deleteOnTermination;
	private $snapshotId;
	private $volumeSize;
	
	public function getDeleteOnTermination() {
		return $this->deleteOnTermination;
	}

	public function getSnapshotId() {
		return $this->snapshotId;
	}

	public function getVolumeSize() {
		return $this->volumeSize;
	}

	public function setDeleteOnTermination($deleteOnTermination) {
		$this->deleteOnTermination = $deleteOnTermination;
	}

	public function setSnapshotId($snapshotId) {
		$this->snapshotId = $snapshotId;
	}

	public function setVolumeSize($volumeSize) {
		$this->volumeSize = $volumeSize;
	}

	public static function parseDescribeEbsBlockDeviceTypeResponse($xml)
	{
		$x = new SimpleXMLElement($xml);
		$bdmt = new EC2_EbsBlockDeviceType();
		$bdmt->deleteOnTermination = $x->deleteOnTermination;
		$bdmt->snapshotId = $x->snapshotId;
		$bdmt->volumeSize = $x->volumeSize;
		return $bdmt;
	}
}


?>