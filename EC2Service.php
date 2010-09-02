<?php

/**
 * EC2 Service
 * 
 * A PHP library for managing EC2 resouces.
 * 
 * @author Jay Muntz
 * 
 * Copyright 2010 Jay Muntz (http://www.awsninja.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * “Software”), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 *
 */

class EC2Service
{
    private static $instance;
    private $secretKey;
    private $accessKey;


    
    public function __construct ()
    {
    	require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2Classes/Attachment.php');
			require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2Classes/BlockDeviceMappingType.php');
			require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2Classes/EBSBlockDeviceType.php');
			require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2Classes/Image.php');
			require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2Classes/Instance.php');
			require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2Classes/Snapshot.php');
			require_once(NINJA_BASEPATH . 'awsninja_ec2resourcemanagement/EC2Classes/Volume.php');
			$this->accessKey = AWS_ACCESS_KEY;
			$this->secretKey = AWS_SECRET_KEY;
		}

    function deleteSecurityGroup($groupName)
    {
    	$params = array(
    		'GroupName' => $groupName
    	);
    	$reqStr = $this->createAmazonRequestString("DeleteSecurityGroup", $params );
    	return $this->sendRequest($reqStr);

    }

		function requestSpotInstances($spotPrice, $launchSpecificationImageId, $launchSpecificationSecurityGroup, $launchSpecificationInstanceType, $availabilityZone, $keyPairName=null)
		{
			$params = array(
				'SpotPrice'=>$spotPrice,
				'InstanceCount'=>1,
				'Type'=>'one-time',
				'LaunchSpecification.ImageId'=>$launchSpecificationImageId,
				'LaunchSpecification.SecurityGroup.1'=>$launchSpecificationSecurityGroup,
				'LaunchSpecification.InstanceType'=>$launchSpecificationInstanceType,
				'LaunchSpecification.Placement.AvailabilityZone'=>$availabilityZone
			);
			if (isset($keyPairName))
			{
				$params['LaunchSpecification.KeyName.1'] = $keyPairName;
			}
		
			$reqStr = $this->createAmazonRequestString('RequestSpotInstances', $params );
			return $this->sendRequest('RequestSpotInstances', $params);
		}

		function describeSpotInstanceRequests($requestId)
		{
			$params = array(
				'SpotInstanceRequestId.1'=>$requestId
			);	
			$reqStr = $this->createAmazonRequestString('DescribeSpotInstanceRequests', $params );
			//echo($reqStr . "\n\n");
			return $this->sendRequest('DescribeSpotInstanceRequests', $params);
			
		}

		function stopInstance($instanceId)
		{
			$params = array(
				'InstanceId'=>$instanceId
			);

			$reqStr = $this->createAmazonRequestString('StopInstances', $params );
			return $this->sendRequest('StopInstances', $params);
		}


		function startInstance($instanceId)
		{
			$params = array(
				'InstanceId'=>$instanceId
			);
			$reqStr = $this->createAmazonRequestString('StartInstances', $params );
			return $this->sendRequest('StartInstances', $params);
		}

   
		function modifyInstanceAttribute($instanceId, $attribute='disableApiTermination', $value=true)
		{
    	$params = array(
    		'InstanceId'=>$instanceId,
    		'Attribute'=>$attribute,
    		'Value'=>$value
    	);		
			return $this->sendRequest('ModifyInstanceAttribute', $params);
		}
		
    function authorizeSecurityGroupIngress($groupName, $sourceSecurityGroupName, $sourceSecurityGroupOwnerId, $ipProtocol, $fromPort, $toPort, $cidrIp)
    {    	
    	$params = array(
    		'GroupName' => $groupName
    	);
    	
    	if (isset($sourceSecurityGroupName))
    	{
    		$params["SourceSecurityGroupName"] = $sourceSecurityGroupName;
    		$params["SourceSecurityGroupOwnerId"]= $sourceSecurityGroupOwnerId;
    	}
    	elseif (isset($ipProtocol))
    	{
    		 $params["IpProtocol"] = $ipProtocol;
    		 $params["FromPort"] = $fromPort;
    		 $params["ToPort"] = $toPort;
    		 $params["CidrIp"] = $cidrIp;
    	}
    	else
    	{
    		throw new Exception("proper parameters not set.");
    	}
    	$reqStr = $this->createAmazonRequestString('DeleteSecurityGroup', $params );
    	return $this->sendRequest('DeleteSecurityGroup', $params);
    }
    
    function deregisterImage($imageId)
    {
    	$params = array(
    		"ImageId" => $imageId
    	);
    	$reqStr = $this->createAmazonRequestString('DeregisterImage', $params );
    	return $this->sendRequest('DeregisterImage', $params );
    }
    
    function bundleInstance($instanceId, $s3AccessKey, $s3Bucket, $s3Prefix)
    {
    	$uploadPolicy = new Amazon_EC2_Util_S3UploadPolicy($this->accessKey,
            $this->secretKey,
            $s3Bucket,
            $s3Prefix,
            $expireInMinutes=1440
       );   
       $params = array(
       		'InstanceId'=>$instanceId,
        	'Storage.S3.AWSAccessKeyId'=>$this->accessKey,
        	'Storage.S3.Bucket'=>$s3Bucket,
        	'Storage.S3.Prefix'=>$s3Prefix,
        	'Storage.S3.UploadPolicy'=>$uploadPolicy->getPolicyString(),
        	'Storage.S3.UploadPolicySignature'=>$uploadPolicy->getPolicySignature()
        );
        
        $reqStr = $this->createAmazonRequestString('BundleInstance', $params );
	    	return $this->sendRequest('BundleInstance', $params );
    }
    
    function describeBundleTasks($bundleId=null)
    {
    	$params = array();
    	if(isset($bundleId))
    	{
    		$params['BundleId'] = $bundleId;
    	}
      $reqStr = $this->createAmazonRequestString('DescribeBundleTasks', $params );
    	return $this->sendRequest('DescribeBundleTasks', $params);  	
    }
    
    
    function registerImage($pathToImageManifest)
    {
    	
        $params = array(
    		'ImageLocation' => $pathToImageManifest
    	);
    	$reqStr = $this->createAmazonRequestString('RegisterImage', $params );
    	//echo($reqStr);
    	return $this->sendRequest('RegisterImage', $params);		
    	
    }
    
    function createSecurityGroup($groupName, $groupDescription)
    {
    	$params = array(
    		"GroupName" => $groupName,
    		"GroupDescription"=>$groupDescription
    	);
    	$reqStr = $this->createAmazonRequestString('CreateSecurityGroup', $params );
    	echo($reqStr. "\n");
    	return $this->sendRequest('CreateSecurityGroup', $params );
    }
    
    
    function describeInstances($instanceId = null)
    {

    	$params = array(
    	//	"GroupName" => $groupName
    	);
			if (isset($instanceId))
			{
				$params['InstanceId.1'] = $instanceId;			
			}
    	$reqStr = $this->createAmazonRequestString('DescribeInstances', $params );
    	return $this->sendRequest('DescribeInstances', $params );
    }
    
   
    function describeImages()
    {
      $params = array(
    		'Owner' => 'self'
    	);
    	$reqStr = $this->createAmazonRequestString('DescribeImages', $params );
    	return $this->sendRequest('DescribeImages', $params);
    }
    
    function associateAddress($instanceID, $publicIp)
    {
    	$params = array (
    		"InstanceId"=>$instanceID,
    		"PublicIP"=>$publicIp
    	);
    	$reqStr = $this->createAmazonRequestString('AssociateAddress', $params );
    	return $this->sendRequest('AssociateAddress', $params);
    }
    
    function createVolume($availabilityZone, $size=null, $snapShotId=null)
    {
    	if (!isset($size) && !isset($snapShotId))
    	{
    		throw new Exception('must set either a size or a snapShotId');
    	}
    	$params = array(
    		'AvailabilityZone'=>$availabilityZone
		);
		if (isset($size))
		{
			$params['Size']=$size;
		}
		if (isset($snapShotId))
		{
			$params['SnapshotId'] = $snapShotId;
		}
		$reqStr = $this->createAmazonRequestString('CreateVolume', $params );
		return $this->sendRequest('CreateVolume', $params );
    }
    
    function describeVolumes($volumeId=null){
    	$params = array();
    	if (isset($volumeId))
    	{
    		$params['VolumeId'] = $volumeId;
    	}
    	$reqStr = $this->createAmazonRequestString('DescribeVolumes', $params );
			return $this->sendRequest('DescribeVolumes', $params);
    }
    
    function attachVolume($volumeId, $instanceId, $device)
    {
    	$params = array (
    		'VolumeId'=>$volumeId,
    		'InstanceId'=>$instanceId,
    		'Device'=>$device
    	);
    	$reqStr = $this->createAmazonRequestString('AttachVolume', $params );
    	return $this->sendRequest('AttachVolume', $params);
    }
    
    function runInstances($imageId, $minCount=1, $maxCount=1, $keyName=null, $groupId=null, $data=null, $version=null, $encoding=null, $instanceType='m1.small', $availabilityZone='us-east-1c', $kernelId=null, $ramdiskId=null, $virtualName=null, $deviceName=null,$monitoringEnabled=null)
    {
		$params = array(
			'ImageId'=>$imageId,
			'MinCount'=>$minCount,
			'MaxCount'=>$maxCount
		);
		if (isset($keyName))
		{
	    	$params['KeyName']=$keyName;
		}
    	if (isset($groupId))
		{
    		$params['SecurityGroup']=$groupId ;
		}
		if (isset($data))
		{
    		$params['Data']=$data;
    	}
		if (isset($version))
		{
    		$params['Version']=$version ;
    	}
		if (isset($encoding))
		{
    		$params['Encoding']=$encoding;
    	}
		if (isset($instanceType))
		{
    		$params['InstanceType']=$instanceType ;
    	}
		if (isset($availablityZone))
		{
    		$params['AvailabilityZone'] = $availabilityZone;
    	}
		if (isset($kernelId))
		{
    		$params['KernelId']=$kernelId;
    	}
		if (isset($ramdiskId))
		{
    		$params['RamdiskId']=$ramdiskId;
    	}
		if (isset($virtualName))
		{
    		$params['VirtualName']=$virtualName ;
    	}
		if (isset($deviceName))
		{
    		$params['DeviceName']=$deviceName;
    	}
		if (isset($monitoringEnabled))
    	{
    		$params['MonitoringEnabled']=$monitoringEnabled;
    	}
		
		$reqStr = $this->createAmazonRequestString('RunInstances', $params );
    	return $this->sendRequest('RunInstances', $params);
    }
    
    
    function deleteVolume($volumeId)
    {
    	$params = array(
    		'VolumeId'=>$volumeId
    	);
    	return $this->sendRequest('DeleteVolume', $params);
    }
    
    
    
    function detachVolume($volumeId, $instanceId=null, $device=null)
    {
    	$params = array(
    		"VolumeId"=>$volumeId
    	);
    	if (isset($instanceId))
    	{
    		$params["InstanceId"] = $instanceId;
    	}
    	if (isset($device))
    	{
    		$params["Device"] = $device;
    	}
      $reqStr = $this->createAmazonRequestString('DetachVolume', $params );
    	return $this->sendRequest('DetachVolume', $params);
    }
    
    function createSnapshot($volumeId, $description = null)
    {
    	$params = array(
    		'VolumeId'=>$volumeId
    	);
    	if (isset($description))
    	{
    		$params['Description'] = $description;
    		
    	}
    	$reqStr = $this->createAmazonRequestString("CreateSnapshot", $params );
    	return $this->sendRequest('CreateSnapshot', $params);
    }
    
    function describeSnapshots($snapShotId=null)
    {
    	$params = array();
    	if (isset($snapShotId))
    	{
				$params['SnapshotId']=$snapShotId;
    	}
    	else
    	{
	  		$params['Owner'] = 'self';  	
    	}
    	$reqStr = $this->createAmazonRequestString('DescribeSnapshots', $params );
    	return $this->sendRequest('DescribeSnapshots', $params );
    }
    
    function deleteSnapshot($snapshotId)
    {
    	$params = array(
    		'SnapshotId'=>$snapshotId
    	); 
    	$reqStr = $this->createAmazonRequestString('DeleteSnapshot', $params );
    	return $this->sendRequest('DeleteSnapshot', $params);   	
    }
    
		function terminateInstance($instanceId)
		{
			$params = array(
				'InstanceId.1'=>$instanceId
			); 
			$reqStr = $this->createAmazonRequestString('TerminateInstances', $params );
			return $this->sendRequest('TerminateInstances', $params);   	
    }
    
  private function sendRequest($action, $nameValPairs)
	{
			//echo("$action\n");
			$nameValPairs['Action'] = $action;
			$params = $this->_addRequiredParameters($nameValPairs);
   		$query = $this->_getParametersAsString($params);
        $url = parse_url ('https://ec2.amazonaws.com/');
        $post  = "POST / HTTP/1.0\r\n";
        $post .= "Host: " . $url['host'] . "\r\n";
        $post .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
        $post .= "Content-Length: " . strlen($query) . "\r\n";
        $post .= "User-Agent: DocMonk\r\n";
        $post .= "\r\n";
        $post .= $query;

        $response = '';
        if ($socket = @fsockopen($url['host'], $url['port'] === null? 80 : $url['port'], $errno, $errstr, 10))
        {
            fwrite($socket, $post);
            while (!feof($socket))
            {
                $response .= fgets($socket, 1160);
            }
            fclose($socket);
            list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
            $other = preg_split("/\r\n|\n|\r/", $other);
            list($protocol, $code, $text) = explode(' ', trim(array_shift($other)), 3);
        }
        else
        {
            throw new Exception ("Unable to establish connection to host " . $url['host'] . " $errstr");
        }
        
        if ($code == 200)
        {
        	return $responseBody;
        }
        else
        {
        		echo($responseBody);
	        //	throw new Exception("Error");
        }
	}
   
    
	static function notCaseSensitive($param1, $param2)
	{
		$p1 = strtolower($param1);
		$p2 = strtolower($param2);	
		if ($p1 < $p2)
		{
			return -1;
		}
		elseif ($p1 > $p2)
		{
			return 1;
		}
		else 
		{
			throw new Exception("The strings are the same.");
		}
	}
	
	private function createAmazonRequestString($action, $nameValPairs)
	{
		$nameValPairs['Action'] = $action;
		$params = $this->_addRequiredParameters($nameValPairs);
		$ct = 0;
		$qryStr = '';
		foreach($params as $key=>$val)
		{
			if ($ct == 0)
			{
				$qryStr .= $key . '='. urlencode($val);
			}
			else 
			{
				$qryStr .= '&' . $key . '='. urlencode($val);		
			}
			$ct++;
		}
		
		return 'https://ec2.amazonaws.com/?' . $qryStr;
	}

	
    private function _addRequiredParameters(array $parameters)
    {
        $parameters['AWSAccessKeyId'] = $this->accessKey;
        $parameters['Timestamp'] = $this->_getFormattedTimestamp();
        $parameters['Version'] = '2009-11-30';
        $parameters['SignatureVersion'] = 2;
        $parameters['SignatureMethod'] = 'HmacSHA256';
        $parameters['Signature'] = $this->_signParameters($parameters, $this->secretKey);
        return $parameters;
    }
    
    private function _signParameters(array $parameters, $key) {
        $stringToSign = $this->_calculateStringToSignV2($parameters);
        return $this->_sign($stringToSign, $key);
    }
	
    private function _calculateStringToSignV2(array $parameters) {
        $data = 'POST';
        $data .= "\n";
        $data .= 'ec2.amazonaws.com';
        $data .= "\n";
        $data .= '/';
        $data .= "\n";
        uksort($parameters, 'strcmp');
        $data .= $this->_getParametersAsString($parameters);
        return $data;
    }
	
    private function _urlencode($value) {
		return str_replace('%7E', '~', rawurlencode($value));
    }
	
    private function _getParametersAsString(array $parameters)
    {
        $queryParameters = array();
        foreach ($parameters as $key => $value)
        {
            $queryParameters[] = $key . '=' . $this->_urlencode($value);
        }
        return implode('&', $queryParameters);
    }
	
    private function _sign($data, $key)
    {
        return base64_encode(
            hash_hmac('sha256', $data, $key, true)
        );
    }
    
    private function _getFormattedTimestamp()
    {
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
    }

}


    /**
    * This class represents S3 upload policy. Policy string
    * representaion and signature to be used within EC2 bundling API.
    */
    class Amazon_EC2_Util_S3UploadPolicy

    {
        private $_policySignature;
        private $_policyString;

        public function __construct(
            $awsAccessKeyId,
            $awsSecretKey,
            $bucketName,
            $prefix,
            $expireInMinutes = 1440)
        {
            $policy = "";
            $policy .= ("{");
            $policy .= ("\"expiration\": \"");
            $policy .= $this->_getFormattedTimestamp($expireInMinutes);
            $policy .= ("\",");
            $policy .= ("\"conditions\": [");
            $policy .= ("{\"bucket\": \"");
            $policy .= ($bucketName);
            $policy .= ("\"},");
            $policy .= ("{\"acl\": \"");
            $policy .= ("ec2-bundle-read");
            $policy .= ("\"},");
            $policy .= ("[\"starts-with\", \"\$key\", \"");
            $policy .= ($prefix);
            $policy .= ("\"]");
            $policy .= ("]}");
            $this->_policyString = base64_encode($policy);
            $this->_policySignature = base64_encode($this->_sign($this->_policyString, $awsSecretKey));
        }

        /**
         * Policy signature in base64 format
         * Use signature generated by this method
         * for passing to EC2 bunding calls along with policy.
         * @return Base64 signature
         */
        public function getPolicySignature() {
            return $this->_policySignature;
        }

        /**
         * Base64 representation of the serialized policy.
         * Use policy generated by this method
         * for passing to EC2 bunding calls.
         * @return Base64 policy
         */
        public function getPolicyString()
        {
            return $this->_policyString;

        }

        /**
         * Computes RFC 2104-compliant HMAC signature.
         */
        private function _sign($data, $key)
        {
            return
            hash_hmac('sha1', $data, $key, true);
        }


        /**
         * Formats date as ISO 8601 timestamp
         */
        private function _getFormattedTimestamp($expireInMinutes)
        {
            return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", (time() + ($expireInMinutes * 60)));
        }


    }




?>