<?php
/*
 * 
 * EC2ResourceManagement
 * 
 * Snapshot Class - Keeps track of your EC2 Volume Snapshots
 * 
 */

require_once('EC2Domain.php');


class Snapshot extends EC2Domain {
	
	private $volumeRole;
	private $dateCreated;
	private $dateCreatedReadable;
	private $serverRole;
	private $snapshotRole;
	
	
	public function getSnapshotRole() {
		return $this->snapshotRole;
	}

	public function setSnapshotRole($snapshotRole) {
		$this->snapshotRole = $snapshotRole;
	}

	public function getVolumeRole() {
		return $this->volumeRole;
	}

	public function getDateCreated() {
		return $this->dateCreated;
	}

	public function getDateCreatedReadable() {
		return $this->dateCreatedReadable;
	}

	public function getServerRole() {
		return $this->serverRole;
	}

	public function setVolumeRole($volumeRole) {
		$this->volumeRole = $volumeRole;
	}

	public function setDateCreated($dateCreated) {
		$this->dateCreated = $dateCreated;
		$this->dateCreatedReadable = $this->_getFormattedTimestamp($dateCreated);
	}


	public function setServerRole($serverRole) {
		$this->serverRole = $serverRole;
	}

	
	public static function getFieldStructure()
	{
		$res = array(
			'volumeRole'=>array(
				'PHPName'=>'VolumeRole'
			),
			'dateCreated'=>array(
				'PHPName'=>'DateCreated'
			),
			'dateCreatedReadable'=>array(
				'PHPName'=>'DateCreatedReadable'
			),
			'serverRole'=>array(
				'PHPName'=>'ServerRole'
			),
			'snapshotRole'=>array(
				'PHPName'=>'SnapshotRole'
			)
		);
		return $res;
	}
	
	
	
	public static function getDomainName()
	{
		return 'Snapshot';
	}
	

	public static function getSingleItemFromQuery($qry)
	{
		return parent::getSingleItemFromQuery(self::getDomainName(), $qry);
	}
	
	public static function getFromQuery($qry)
	{
		return parent::getFromQuery(self::getDomainName(), $qry);
	}
	
	
    private function _getFormattedTimestamp($date=null)
    {
    	if (!isset($date))
    	{
    		$date = time();
    	}
       return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", $date);
    }
	
	
}





?>