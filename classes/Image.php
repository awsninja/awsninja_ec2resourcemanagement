<?php
/*
 * 
 * EC2ResourceManagement
 * 
 * Image Class - Keeps track of your AMIs.
 * 
 */

require_once('EC2Domain.php');

class Image extends EC2Domain {
	
	private $imageId;
	
	public function getImageId()
	{
		return $this->imageId;
	}

	public function setImageId($imageId)
	{
		$this->imageId = $imageId;
	}
	
	public static function getFieldStructure()
	{
		$res = array(
			'imageId'=>array(
				'PHPName'=>'ImageId'
			)
		);
		return $res;
	}
	
	public static function getDomainName()
	{
		return 'Image';
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