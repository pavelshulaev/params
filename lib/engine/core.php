<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 29.10.2016
 * Time: 18:33
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Params\Engine;

use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\DB\Result;

class Core
{
	/**
	 * @var array
	 */
	protected static $defaults = [
		'query'     => [],
		'empty'     => '-',
		'template'  => ['{ID}' => '[{ID}] {NAME}'],
		'class'     => '',
		'method'    => '',
		'filter'    => [],
		//'elements'  => []
	];

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
	 * @param $params
	 * @return mixed
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function checkParams(array $params = [])
	{
		foreach (self::$defaults as $key => $default)
			if (!array_key_exists($key, $params))
				$params[$key] = $default;

		return $params;
	}

	/**
	 * @param array $params
	 * @return array|null
	 * @throws ArgumentOutOfRangeException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function prepare(array $params = [])
	{
		$params     = self::checkParams($params);
		$cacheKey   = Cache::getKey(serialize($params));

		if(false === (Cache::check($cacheKey))) {

			$empty  = $params['empty'];
			$result = is_null($empty)
				? []
				: [0 => $empty];

			$class      = $params['class'];
			$method     = $params['method'];

			if (!method_exists($class, $method))
				return $result;


			$template   = $params['template'];
			$query      = $params['query'];

			if (count($params['filter']))
				$query['filter'] = array_replace($query['filter'], $params['filter']);

			$keyTemplate    = key($template);
			$nameTemplate   = $template[$keyTemplate];

			if (empty($keyTemplate) || empty($nameTemplate))
				return $result;

			if (!isset($params['elements'])) {
				/**
				 * @var Result $rcElements
				 */
				$rcElements = $class::$method($query);

				// check if empty result
				if (is_null($rcElements))
					return $result;

				$elements = $rcElements->fetchAll();
			} else {
				$elements = $params['elements'];
			}

			$result = self::prepareResult($elements, $keyTemplate,
				$nameTemplate, $result);

			Cache::set($cacheKey, $result);
		}

		return Cache::get($cacheKey);
	}

	/**
	 * @param array $elements
	 * @param       $keyTemplate
	 * @param       $nameTemplate
	 * @param array $result
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function prepareResult(array $elements, $keyTemplate, $nameTemplate, array $result = [])
	{
		$nameMask   = self::getMask($nameTemplate);
		$keyMask    = self::getMask($keyTemplate);

		foreach ($elements as $element)
		{
			$key    = self::prepareName($element, $keyMask, $keyTemplate);
			$name   = self::prepareName($element, $nameMask, $nameTemplate);

			$result[$key] = $name;
		}

		return $result;
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
	 * @param array $params
	 * @return array|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function prepareEmpty(array $params = [])
	{
		$params['elements'] = [];

		return self::prepare($params);
	}


}