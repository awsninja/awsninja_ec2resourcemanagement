
EC2ResourceManagement - A PHP EC2 Framework - Version 0.0

INTRODUCTION
------------
For details visit the related article on the AWS Ninja blog:
http://wp.me/pWVsZ-3Y

REQUIREMENTS
------------

PHP 5
awsninja_core - Core components for all AWSNinja libraries.


INSTALLATION
------------

  1. Copy the awsninja_ec2resourcemanagement folder to the same location 
     that contains the awsninja_core directory.
  
  2. Make sure your AWS Key and Secret Key are set in the config.php in
     the AWSCore directory.  View config.samp.php if you need guidance.
  
  3. From the command line, enter the examples subdriectory and  run the 
     buildEC2ResourceInventory.php script to intially configure your
     EC2 resources in your SimpleDb database.
  
	4. Inspect and run the other examples to learn how the EC2ResourceManagement
	   package works.
	   
NOTES
-----
The classes directory contains classes that represent the EC2Resource records
you have in SimpleDb.  The "EC2Classes" directory contains classes that
represent actual EC2 objects.  The "EC2Classes" are essentially the objects
that are returned from the EC2 API.

