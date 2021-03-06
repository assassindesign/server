<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.thumbnailSources
 */

class kImagickSource extends kThumbnailSource
{
	protected $imagick;

	public function  __construct($imagick)
	{
		$this->imagick = $imagick;
	}

	public function getImage()
	{
		return $this->imagick;
	}
}