<?php


class ComcastArrayOflong extends SoapArray
{				
	public function getType()
	{
		return 'http://www.theplatform.com/package/:ArrayOflong';
	}
				
	public function __construct()
	{
		parent::__construct("long");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


