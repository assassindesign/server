<?php
require_once __DIR__ . '/kBaseMemcacheConf.php';

class kRemoteMemCacheConf extends kBaseMemcacheConf implements kKeyCacheInterface,kMapCacheInterface
{
	const MAP_LIST_KEY = 'MAP_LIST_KEY';
	const MAP_DELIMITER = '|';
	const GLOBAL_INI_SECTION_REGEX = '/\[.*\S.*\]/m';
	public function loadKey()
	{
		$key=null;
		$cache = $this->getCache();
		if($cache)
			$key = $cache->get(kBaseConfCache::CONF_CACHE_VERSION_KEY);

		if (!$key)
			$key = self::generateKey();

		//key must be supplied.
		return $key;
	}

	public function storeKey($key, $ttl=30){return;}

	public function load($key, $mapName)
	{
		$hostname = $this->getHostName();
		return $this->loadByHostName($mapName,$hostname);
	}

	public function loadByHostName($mapName,$hostname, $excludeHost = false)
	{
		$mapNames = $this->getRelevantMapList($mapName, $hostname, $excludeHost);
		$this->orderMap($mapNames);
		return $this->mergeMaps($mapNames, $mapName);
	}

	protected function getRelevantMapList($requestedMapName , $hostname, $excludeHost = false)
	{
		$filteredMapsList = array($requestedMapName);
		$mapsList = null;
		$cache = $this->getCache();
		if($cache)
		{
			$mapsList = $cache->get(self::MAP_LIST_KEY);
			if ($mapsList)
			{
				foreach ($mapsList as $mapName => $version)
				{
					$mapVar = explode(self::MAP_DELIMITER, $mapName);
					$storedMapName = $mapVar[0];
					$hostPattern = isset($mapVar[1]) ? $mapVar[1] : null;
					if ($requestedMapName == $storedMapName)
					{
						if ($hostname === $hostPattern && $excludeHost)
						{
							continue;
						}

						if ($hostPattern && $hostname != $hostPattern && $hostPattern !== '#')
						{
							$hostPattern = str_replace('#', '.*', $hostPattern);
							if(!preg_match('/' . $hostPattern . '/', $hostname))
								continue;
						}
						$filteredMapsList[] = $mapName;
					}
				}
			}
		}
		return $filteredMapsList;
	}

	protected function mergeMaps($mapNames)
	{
		$cache = $this->getCache();
		if (!$cache)
		{
			return null;
		}
		$content = null;
		$globalContent = null;
		/** Note: we are concatenating the text content to a single ini file content since some inheritence sections are in
		 * different maps and only after merging them we can create the merged ini file and validate it.
		 * Since there are also global parameters in many merged files we need to merge them get them seperatly before merging the content
		 * otherwise they will be merged to the previous map last section and will not be in global section anymore.
		 */
		foreach ($mapNames as $mapName)
		{
			$map = $cache->get(self::CONF_MAP_PREFIX . $mapName);
			if ($map)
			{
				$mapContnet = json_decode($map, true);
				if (is_array($mapContnet))
				{
					KalturaLog::debug("Retrieved content in array format from RemoteCache for map - $mapName with content: \n" . print_r($mapContnet, true));
					$mapContnet = iniUtils::arrayToIniString($mapContnet);
				}
				//get global section data - PREG_OFFSET_CAPTURE return offset starting point in index[1] of match
				preg_match(self::GLOBAL_INI_SECTION_REGEX, $mapContnet, $matches, PREG_OFFSET_CAPTURE);
				if (isset($matches[0][1]))// find the split point between the global part and the other sections
				{
					$globalContent .= PHP_EOL . substr($mapContnet, 0, $matches[0][1]);
					$content .= PHP_EOL . substr($mapContnet, $matches[0][1]);
				}
				else
				{
					$globalContent .= PHP_EOL . $mapContnet;
				}
			}
		}
		return iniUtils::iniStringToIniArray($globalContent . PHP_EOL . $content);
	}

	public function getHostList($requesteMapName , $hostNameRegex = null)
	{
		$hostList = array();
		$cache = $this->getCache();
		if(!$cache)
		{
			return $hostList;
		}

		$mapsList = $cache->get(self::MAP_LIST_KEY);
		foreach ($mapsList as $mapName => $version)
		{
			$mapVar = explode(self::MAP_DELIMITER, $mapName);
			$storedMapName = $mapVar[0];
			$hostPattern = isset($mapVar[1]) ? $mapVar[1] : null;
			if ($requesteMapName == $storedMapName)
			{
				if(!$hostNameRegex || preg_match('/'.$hostNameRegex.'/' ,$hostPattern ) )
				{
					$hostList[] = $hostPattern;
				}
			}
		}

		return $hostList;
	}
	public function getMap($mapName,$hostName)
	{
		$cache = $this->getCache();
		if(!$cache)
		{
			return null;
		}
		$mapKeyInCache = self::CONF_MAP_PREFIX.$mapName.self::MAP_DELIMITER.$hostName;
		return $cache->get($mapKeyInCache);
	}
}
