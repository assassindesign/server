<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaJsonProcSerializer extends KalturaJsonSerializer
{
	public function __construct()
	{
		if (!isset($_GET["callback"]))
		{
			throw new KalturaAPIException(APIErrors::MANDATORY_PARAMETER_MISSING, 'callback');
		}

		// check for a valid callback, prevent xss
		$ALLOWED_REGEX = "/^[0-9_a-zA-Z.]*$/";
		if (!preg_match($ALLOWED_REGEX, $_GET["callback"]))
		{
			throw new KalturaAPIException(APIErrors::INVALID_FIELD_VALUE, 'callback');
		}
	}

	public function setHttpHeaders()
	{
		header("Content-Type: application/javascript");
	}
	
	public function getHeader()
	{
		return $_GET["callback"] .  "(";
	}
	
	public function getFooter($execTime = null)
	{
		return ");";
	}
}
