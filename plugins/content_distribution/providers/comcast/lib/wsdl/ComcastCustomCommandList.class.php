<?php


class ComcastCustomCommandList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:CustomCommandList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCustomCommand");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


