<?php

//require_once('EC2/BlockDeviceMappingType.php');

class EC2_Image {

	private $architecture;
	private $blockDeviceMapping;
	private $description;
	private $imageId;
	private $imageLocation;
//	private $imageOwnerAlias;
	private $imageOwnerId;
	private $imageState;
	private $imageType;
	private $isPublic;
	private $kernelId;
	private $name;
	private $platform;
//	private $productCodes;
	private $ramdiskId;
	private $rootDeviceName;
	private $rootDeviceType;
//	private $stateReason;
	
	public function getArchitecture() {
		return $this->architecture;
	}

	public function getBlockDeviceMapping() {
		return $this->blockDeviceMapping;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getImageId() {
		return $this->imageId;
	}

	public function getImageLocation() {
		return $this->imageLocation;
	}

//	public function getImageOwnerAlias() {
//		return $this->imageOwnerAlias;
//	}

	public function getImageOwnerId() {
		return $this->imageOwnerId;
	}

	public function getImageState() {
		return $this->imageState;
	}

	public function getImageType() {
		return $this->imageType;
	}

	public function getIsPublic() {
		return $this->isPublic;
	}

	public function getKernelId() {
		return $this->kernelId;
	}

	public function getName() {
		return $this->name;
	}

	public function getPlatform() {
		return $this->platform;
	}

//	public function getProductCodes() {
//		return $this->productCodes;
//	}

	public function getRamdiskId() {
		return $this->ramdiskId;
	}

	public function getRootDeviceName() {
		return $this->rootDeviceName;
	}

	public function getRootDeviceType() {
		return $this->rootDeviceType;
	}

//	public function getStateReason() {
//		return $this->stateReason;
//	}

	public function setArchitecture($architecture) {
		$this->architecture = $architecture;
	}

	public function setBlockDeviceMapping($blockDeviceMapping) {
		$this->blockDeviceMapping = $blockDeviceMapping;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function setImageId($imageId) {
		$this->imageId = $imageId;
	}

	public function setImageLocation($imageLocation) {
		$this->imageLocation = $imageLocation;
	}

//	public function setImageOwnerAlias($imageOwnerAlias) {
//		$this->imageOwnerAlias = $imageOwnerAlias;
//	}

	public function setImageOwnerId($imageOwnerId) {
		$this->imageOwnerId = $imageOwnerId;
	}

	public function setImageState($imageState) {
		$this->imageState = $imageState;
	}

	public function setImageType($imageType) {
		$this->imageType = $imageType;
	}

	public function setIsPublic($isPublic) {
		$this->isPublic = $isPublic;
	}

	public function setKernelId($kernelId) {
		$this->kernelId = $kernelId;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setPlatform($platform) {
		$this->platform = $platform;
	}

//	public function setProductCodes($productCodes) {
//		$this->productCodes = $productCodes;
//	}

	public function setRamdiskId($ramdiskId) {
		$this->ramdiskId = $ramdiskId;
	}

	public function setRootDeviceName($rootDeviceName) {
		$this->rootDeviceName = $rootDeviceName;
	}

	public function setRootDeviceType($rootDeviceType) {
		$this->rootDeviceType = $rootDeviceType;
	}

//	public function setStateReason($stateReason) {
//		$this->stateReason = $stateReason;
//	}


	
	public static function ParseDescribeImagesResponse($xml)
	{
		$x = new SimpleXMLElement($xml);
		$res = array();
		foreach($x->imagesSet->item as $item)
		{
			$img = new EC2_Image();
			$img->architecture = $item->architecture;
			if (isset($item->description))
			{
				$img->description = $img->description;
			}
			else
			{
				$img->description = '';
			}
			$img->imageId = $item->imageId;
			$img->imageLocation = $item->imageLocation;
			$img->imageOwnerId = $item->imageOwnerId;
			$img->imageState = $item->imageState;
			$img->imageType = $item->imageType;
			$img->isPublic = $item->isPublic;
			$img->kernelId = $item->kernelId;
			$img->name = $item->name;
			$img->platform = $item->platform;
			$img->ramdiskId = $item->randiskId;
			$img->rootDeviceName = $item->rootDeviceName;
			$img->rootDeviceType = $item->rootDeviceType;
			$bdmAry=array();
			foreach($item->blockDeviceMapping->item as $item)
			{
					$bdmAry[] =  EC2_BlockDeviceMappingType::parseDescribeBlockDeviceMappingTypeResponse($item->asXML());
			}
			$img->blockDeviceMapping = $bdmAry;
			$res[] = $img;			
		}
		return $res;		
	}
	



}