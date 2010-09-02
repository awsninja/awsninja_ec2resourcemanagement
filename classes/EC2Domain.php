<?php

/*
 * 
 * EC2ResourceManagement
 * 
 * EC2Domain is an abstract class.	Sub-classes are used to represent EC2 objects(e.g. Instances, Volumes, Snapshots, etc.).
 * Information about those objects is stored in Amazon SimpleDb.
 * 
 */

require_once(NINJA_BASEPATH . 'awsninja_core/SimpleDbService.php');

abstract class EC2Domain {

	private $itemName;
	protected $sdbs;
	
	function __construct( $itemName=null )
	{
		$this->sdbs = SimpleDbService::instance();
		if (isset($itemName))
		{
			$this->itemName = $itemName;
			$this->get();
		}
	}
	
	//Abstract Methods - Must be implemented by sub-classes.	
	public static abstract function getFieldStructure();
	public static abstract function getDomainName();
	
	
	public function getItemName()
	{
		return $this->itemName;
	}
	
	public function setItemName($itemName)
	{
		$this->itemName = $itemName;
	}

	public function getRole()
	{
		$parts = explode('-', $this->getItemName());
		return $parts[0];
	}
	
	public function save()
	{
		$this->put();
	}

		
	public function delete()
	{
		$this->sdbs->DeleteAttributes($this->getDomainName(), $this->itemName);
	}
		
	
	public static function getSingleItemFromQuery($domain, $qry)
	{
		$results = self::getFromQuery($domain, $qry);
		if (isset($results[0]))
		{
			return $results[0];
		}
		else
		{
			return null;
		}
	}
	
	public static function getFromQuery($domain, $qry)
	{
		if (!isset($qry))
		{
			throw new Exception("must pass the qry parameter");
		}
		$sdb = SimpleDBService::instance();
		$res = $sdb->Select($qry);
		$objAry = array();
		$objs = $res;
		foreach($objs as $key=>$attrCol)				
		{
			$className = "$domain";
			$obj = new $className();
			$params = $obj->getFieldStructure();
			$obj->setItemName($key);
			foreach($attrCol as $attr)
			{
				if (isset($params["{$attr->getName()}"]))
				{
					$obj->{'set' . $params["{$attr->getName()}"]['PHPName']}($attr->getValue());
				}
			}
			$objAry[] = $obj;
		}
		return $objAry;
	}	
	
	//get() and put() read and write from SimpleDb.
	private function put()
	{
		if (!isset($this->itemName))
		{
			throw new Exception('You forgot to set the item name.');
		}
		$fields = $this->getFieldStructure();
		$attributes = new SimpleDBAttributeCollection();
		foreach($fields as $key=>$value)
		{
			$simpleAttr = new SimpleDBAttribute($key, $this->{'get' . $value['PHPName']}(), true);
			$attributes->add($simpleAttr);
		}
		$res = $this->sdbs->PutAttributes($this->getDomainName(), $this->getItemName(), $attributes);
		if ($res->responseCode != 200)
		{
			for($i=0; $i<count($res->errors); $i++)
			{
				$err = $res->errors[$i];
				if ($err['Code'] == 'NoSuchDomain')
				{
					$this->createDomain();
					sleep(1);
					$this->put();
				}
				else
				{
					print_r($err);
					echo("unhandled error\n");
				}
			}
		}
	}

	private function get()
	{
		$attrCol = $this->sdbs->GetAttributes($this->getDomainName(), $this->itemName);
	 	$fields = $this->getFieldStructure(); 
	 	if (isset($attrCol))
	 	{
		 	foreach($attrCol as $attr)
			{
				$nm = "{$attr->getName()}";
				if (isset($fields[$nm]))
				{
					$this->{"set$nm"}($attr->getValue());
				}
			}
	 	}
	}
	
	private function createDomain()
	{
		$res  = $this->sdbs->CreateDomain($this->getDomainName());
	}
		


		
		
}

?>