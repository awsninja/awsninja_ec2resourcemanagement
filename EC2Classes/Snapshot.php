<?php

class EC2_Snapshot {

	private $snapshotId;
	private $volumeId;
	private $status;
	private $startTime;
	private $progress;
	private $volumeSize;
	
	
	public function getVolumeSize()
	{
		return $this->volumeSize;
	}
	
	public function setVolumeSize($volumeSize)
	{
		$this->volumeSize = $volumeSize;
	}
	
	public function getProgress() {
		return $this->progress;
	}
	
	public function getSnapshotId() {
		return $this->snapshotId;
	}
	
	public function getStartTime() {
		return $this->startTime;
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function getVolumeId() {
		return $this->volumeId;
	}
	
	public function setProgress($progress) {
		$this->progress = $progress;
	}
	
	public function setSnapshotId($snapshotId) {
		$this->snapshotId = $snapshotId;
	}
	
	public function setStartTime($startTime) {
		$this->startTime = $startTime;
	}
	
	public function setStatus($status) {
		$this->status = $status;
	}
	
	public function setVolumeId($volumeId) {
		$this->volumeId = $volumeId;
	}
	
	public static function ParseDescribeSnapshotsResponse($xml)
	{
		$snaps = array();
		$x = new SimpleXMLElement($xml);
		foreach($x->snapshotSet->item as $snapshot)
		{
			$snap = new EC2_Snapshot();
			$snap->setSnapshotId($snapshot->snapshotId);
			$snap->setVolumeId($snapshot->volumeId);
			$snap->setStatus($snapshot->status);
			$snap->setStartTime($snapshot->startTime);
			$snap->setProgress($snapshot->progress);
			$snap->setVolumeSize($snapshot->volumeSize);
			$snaps["{$snapshot->snapshotId}"] = $snap;
		}
		return $snaps;		
	}
	
	
	public static function ParseCreateSnapshotResponse($xml)
	{
//		$xml = '<?xml version="1.0"?' . '>
//<CreateSnapshotResponse xmlns="http://ec2.amazonaws.com/doc/2009-11-30/">
//    <requestId>733ffe54-faf5-463c-980a-b1b317f6fdba</requestId>
//    <snapshotId>snap-b36134da</snapshotId>
//    <volumeId>vol-6e09fc07</volumeId>
//    <status>pending</status>
//    <startTime>2010-02-02T02:38:39.000Z</startTime>
//    <progress/>
//    <ownerId>175676212959</ownerId>
//    <volumeSize>5</volumeSize>
//    <description/>
//</CreateSnapshotResponse>';
		
		$x = new SimpleXMLElement($xml);

		$snap = new EC2_Snapshot();
		$snap->setSnapshotId($x->snapshotId);
		$snap->setVolumeId($x->volumeId);
		$snap->setStatus($x->status);
		$snap->setStartTime($x->startTime);
		$snap->setProgress($x->progress);
		$snap->setVolumeSize($x->volumeSize);
	
		return $snap;
		
	}

	
}

?>