<?php


class ComcastReleaseList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/value/:ReleaseList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastRelease");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


