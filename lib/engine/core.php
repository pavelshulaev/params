<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 29.10.2016
 * Time: 18:33
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params\Engine;

use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\DB\Result;

/**
 * Class Core
 *
 * @package Rover\Params\Engine
 * @author  Pavel Shulaev (https://rover-it.me)
 */
abstract class Core
{
    /**
     * @var string|null
     */
    protected static $moduleName;

	/**
	 * @var array
	 */
	protected static $defaults = array(
		'empty'     => '-',
		'template'  => array('{ID}' => '[{ID}] {NAME}'),
		'class'     => '',
		'method'    => '',
		'filter'    => array(),
		'add_filter'=> array(),
		'order'     => array('SORT' => 'ASC'),
		'select'    => array(),
		'reload'    => false
		//'elements'  => array()
    );

	/**
	 * @throws SystemException
	 * @throws \Bitrix\Main\LoaderException
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	protected static function checkModule()
	{
		if (empty(static::$moduleName))
			return;

		if (!Loader::includeModule(static::$moduleName))
			throw new SystemException('Module "' . static::$moduleName . '" not found');
	}

	/**
	 * @param $params
	 * @return mixed
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	protected static function prepareParams(array $params = array())
	{
		// set default if empty
		foreach (self::$defaults as $key => $default)
			if (!array_key_exists($key, $params))
				$params[$key] = $default;


		$params['template'] = self::prepareTemplate($params['template']);
        $params['select']   = self::addSelectFromTemplate($params['select'], $params['template']);

		// add_filter
		if (isset($params['add_filter'])){
            $params['filter'] = array_replace($params['filter'], $params['add_filter']);
            unset($params['add_filter']);
		}

		return $params;
	}

    /**
     * @param $template
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function prepareTemplate($template)
    {
        if (!is_array($template))
            return array($template => $template);

        $keyTemplate    = key($template);
        $nameTemplate   = $template[$keyTemplate];

        if (!empty($keyTemplate) && !empty($nameTemplate))
            return $template;

        if (empty($nameTemplate))
            return array($keyTemplate => $keyTemplate);

        return array($nameTemplate => $nameTemplate);
    }

    /**
     * @param $empty
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function getStartResult($empty)
    {
        return is_null($empty) ? array() : array(0 => $empty);
    }

    /**
     * @param array $params
     * @return array|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function prepare(array $params = array())
	{
		$params     = self::prepareParams($params);
		$cacheKey   = Cache::getKey(serialize($params));

		if((false === (Cache::check($cacheKey))) || $params['reload'])  {

			$result = self::getStartResult($params['empty']);
			$class  = $params['class'];
			$method = $params['method'];

			if (!method_exists($class, $method))
				return $result;

			$query = array(
				'filter'    => $params['filter'],
				'select'    => $params['select'],
				'order'     => $params['order'],
            );

			if (Dependence::d7CacheAvailable())
                $query['cache'] = array('ttl' => 3600);

			pr($query); die();
			/**
			 * @var Result $rcElements
			 */
			$rcElements = $class::$method($query);

			// check if empty result
			if (!$rcElements->getSelectedRowsCount())
				return $result;

			$elements   = $rcElements->fetchAll();
			$result     = self::prepareResult($elements, $params['template'], $result);

			Cache::set($cacheKey, $result);
		}

		return Cache::get($cacheKey);
	}

    /**
     * @param $select
     * @param $template
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function addSelectFromTemplate($select, $template)
	{
		$keyTemplate    = key($template);
		$nameTemplate   = $template[$keyTemplate];

		return array_unique(array_merge($select,
            self::getFieldsNames($keyTemplate),
			self::getFieldsNames($nameTemplate)));
	}

	/**
	 * @param $string
	 * @return array
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	protected static function getFieldsNames($string)
	{
		preg_match_all('/{([^}]+)}/usi', $string, $matches);

		return isset($matches[1])
			? $matches[1]
			: array();
	}

    /**
     * @param array $elements
     * @param       $template
     * @param array $result
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function prepareResult(array $elements, $template, array $result = array())
	{
	    $keyTemplate    = key($template);
        $nameTemplate   = $template[$keyTemplate];

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
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	protected static function prepareName($element, $mask, $template)
	{
		$name = $template;

		foreach ($mask as $fieldTemplate => $fieldName)
			$name = str_replace($fieldTemplate, $element[$fieldName], $name);

		return trim($name);
	}

	/**
	 * @param $template
	 * @return array
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	protected static function getMask($template)
	{
		$mask = array();
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
     * @throws ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function prepareEmpty(array $params = array())
	{
		$params['elements'] = array();

		return self::prepare($params);
	}
}