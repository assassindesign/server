<?php
/**
 * @package Core
 * @subpackage storage
 */
class kS3SharedPathManager extends kPathManager
{
	/**
	 * will return a pair of file_root and file_path
	 * This is the only function that should be extended for building a different path
	 *
	 * @param ISyncableFile $object
	 * @param int $subType
	 * @param $version
	 */
	public function generateFilePathArr(ISyncableFile $object, $subType, $version = null, $storageProfileId = null)
	{
		list($root, $filePath) = $object->generateFilePathArr($subType, $version, true);
		$filePath = str_replace('/content/', '/', $filePath);
		$root = kSharedFileSystemMgr::getSharedRootByType(kSharedFileSystemMgrType::S3);
		
		KalturaLog::debug("S3 Path [{$root}{$filePath}]");
		return array($root, $filePath);
	}
}