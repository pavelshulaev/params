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
		'empty'     => '-',
		'template'  => ['{ID}' => '[{ID}] {NAME}'],
		'class'     => '',
		'method'    => '',
		'filter'    => [],
		'add_filter'=> [],
		'order'     => ['SORT' => 'ASC']
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
	protected static function prepareParams(array $params = [])
	{
		// set default if empty
		foreach (self::$defaults as $key => $default)
			if (!array_key_exists($key, $params))
				$params[$key] = $default;

		// fix template
		$template       = $params['template'];

		$keyTemplate    = key($template);
		$nameTemplate   = $template[$keyTemplate];

		if (empty($nameTemplate))
			$params['template'] = [$keyTemplate => $keyTemplate];
		elseif (empty($keyTemplate))
			$params['template'] = [$nameTemplate => $nameTemplate];

		// fix select
		if (!isset($params['select']))
			$params['select'] = self::getSelectFromTemplate($params['template']);

		// add_filter
		if (isset($params['add_filter']))
			$params['filter'] = array_replace($params['filter'], $params['add_filter']);

		unset($params['add_filter']);

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
		$params     = self::prepareParams($params);
		$cacheKey   = Cache::getKey(serialize($params));
		$reload     = isset($params['reload']) && $params['reload'];

		if((false === (Cache::check($cacheKey))) || $reload)  {

			$empty  = $params['empty'];
			$result = is_null($empty)
				? []
				: [0 => $empty];

			$class  = $params['class'];
			$method = $params['method'];

			if (!method_exists($class, $method))
				return $result;

			$query = [
				'filter'    => $params['filter'],
				'select'    => $params['select'],
				'order'     => $params['order']
			];
			/**
			 * @var Result $rcElements
			 */
			$rcElements = $class::$method($query);

			// check if empty result
			if (!$rcElements->getSelectedRowsCount())
				return $result;

			$elements   = $rcElements->fetchAll();
			$result     = self::prepareResult($elements, key($params['template']),
				$params['template'][key($params['template'])], $result);

			Cache::set($cacheKey, $result);
		}

		return Cache::get($cacheKey);
	}

	/**
	 * @param $template
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected function getSelectFromTemplate($template)
	{
		$keyTemplate    = key($template);
		$nameTemplate   = $template[$keyTemplate];

		return array_unique(array_merge(self::getFieldsNames($keyTemplate),
			self::getFieldsNames($nameTemplate)));
	}

	/**
	 * @param $string
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected function getFieldsNames($string)
	{
		preg_match_all('/{([^}]+)}/usi', $string, $matches);

		return isset($matches[1])
			? $matches[1]
			: [];
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