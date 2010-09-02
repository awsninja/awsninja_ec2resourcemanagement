<?php

/*
 * 
 * EC2ResourceManagement
 * 
 * Instances Class - Keeps track of your EC2 Instances.
 * 
 */

require_once('EC2Domain.php');


class Instance extends EC2Domain {
	
	private $instanceId;
	
	public function getInstanceId()
	{
		return $this->instanceId;
	}

	public function setInstanceId($instanceId)
	{
		$this->instanceId = $instanceId;
	}
	
	public static function getFieldStructure()
	{
		$res = array(
			'instanceId'=>array(
				'PHPName'=>'InstanceId'
			)
		);
		return $res;
	}
	
	
	
	public static function getDomainName()
	{
		return 'Instance';
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