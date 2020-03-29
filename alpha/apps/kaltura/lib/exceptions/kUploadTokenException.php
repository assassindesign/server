<?php
/**
 * @package Core
 * @subpackage errors
 */
class kUploadTokenException extends kCoreException
{
	const UPLOAD_TOKEN_FILE_NAME_IS_MISSING_FOR_UPLOADED_FILE = "UPLOAD_TOKEN_FILE_NAME_IS_MISSING_FOR_UPLOADED_FILE";
	
	const UPLOAD_TOKEN_FILE_IS_NOT_VALID = "UPLOAD_TOKEN_FILE_IS_NOT_VALID"; 
	
	const UPLOAD_TOKEN_FAILED_TO_MOVE_UPLOADED_FILE = "UPLOAD_TOKEN_FAILED_TO_MOVE_UPLOADED_FILE";
	
	const UPLOAD_TOKEN_UPLOAD_ERROR_OCCURRED = "UPLOAD_TOKEN_UPLOAD_ERROR_OCCURRED";
	
	const UPLOAD_TOKEN_FILE_NOT_FOUND_FOR_RESUME = "UPLOAD_TOKEN_FILE_NOT_FOUND_FOR_RESUME";
	
	const UPLOAD_TOKEN_INVALID_STATUS = "UPLOAD_TOKEN_INVALID_STATUS";
	
	const UPLOAD_TOKEN_NOT_FOUND = "UPLOAD_TOKEN_NOT_FOUND";

	const UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE = "UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE";

	const UPLOAD_TOKEN_FILE_TYPE_RESTRICTED = "UPLOAD_TOKEN_FILE_TYPE_RESTRICTED";
	
	const UPLOAD_TOKEN_MAX_AUTO_FINALIZE_RETRIES_REACHED  = "UPLOAD_TOKEN_MAX_AUTO_FINALIZE_RETRIES_REACHED";
	
	const UPLOAD_TOKEN_AUTO_FINALIZE_CACHE_NOT_INITIALIZED  = "UPLOAD_TOKEN_AUTO_FINALIZE_CACHE_NOT_INITIALIZED";
	
	const UPLOAD_TOKEN_FILE_IS_EMPTY = "UPLOAD_TOKEN_FILE_IS_EMPTY";
}