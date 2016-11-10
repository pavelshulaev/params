<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 29.10.2016
 * Time: 18:33
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Params;

use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\DB\Result;

class Core
{
	/**
	 * @throws SystemException
	 * @throws \Bitrix\Main\LoaderException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function checkModule()
	{
		if (!isset(static::$moduleName))
			return;

		if (!Loader::includeModule(static::$moduleName))
			throw new SystemException('Module "' . static::$moduleName . '" not found');
	}

	/**
	 * @param Result|null $rcElements
	 * @param null        $empty
	 * @param array       $template
	 * @return array|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function prepare(Result $rcElements = null, $empty = null, array $template = ['{ID}' => '[{ID}] {NAME}'])
	{
		$result = is_null($empty)
			? []
			: [0 => $empty];

		// check if empty result
		if (is_null($rcElements))
			return $result;

		$keyTemplate    = key($template);
		$nameTemplate   = $template[$keyTemplate];

		if (empty($keyTemplate) || empty($nameTemplate))
			return $result;

		// check cache
		$cacheKey = Cache::getKey(serialize($rcElements) . $empty . serialize($template));

		if(false === (Cache::check($cacheKey))) {

			$nameMask   = self::getMask($nameTemplate);
			$keyMask    = self::getMask($keyTemplate);

			while($element = $rcElements->fetch())
			{
				$key    = self::prepareName($element, $keyMask, $keyTemplate);
				$name   = self::prepareName($element, $nameMask, $nameTemplate);

				$result[$key] = $name;
			}

			Cache::set($cacheKey, $result);
		}

		return Cache::get($cacheKey);
	}

	/**
	 * @param $element
	 * @param $mask
	 * @param $template
	 * @return mixed
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function prepareName($element, $mask, $template)
	{
		$name = $template;

		foreach ($mask as $fieldTemplate => $fieldName)
			$name = str_replace($fieldTemplate, $element[$fieldName], $name);

		return $name;
	}

	/**
	 * @param $template
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function getMask($template)
	{
		$mask = [];
		preg_match_all('/{([^}]+)}/si', $template, $matches);

		// check if empty template
		if (!count($matches[0]) || !count($matches[1]))
			return $mask;

		foreach ($matches[0] as $num => $fieldTemplate)
			$mask[$fieldTemplate] = $matches[1][$num];

		return $mask;
	}

	/**
	 * @param string $empty
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function prepareEmpty($empty = null)
	{
		return self::prepare(null, $empty);
	}


}