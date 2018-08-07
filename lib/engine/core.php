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
use \Bitrix\Main\Entity\DataManager;
/**
 * Class Core
 *
 * @package Rover\Params\Engine
 * @author  Pavel Shulaev (https://rover-it.me)
 */
abstract class Core
{
    /** @var string|null */
    protected static $moduleName;

	/** @var array */
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
	public static function getEmptyResult($empty = null)
    {
        return is_null($empty) ? array() : array(0 => $empty);
    }

    /**
     * @param array $params
     * @return array|null
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function prepare(array $params = array())
	{
		$params = self::prepareParams($params);
		if (!isset($params['class'])
            || !isset($params['method']))
		    return self::getEmptyResult($params['empty']);

		$cacheKey = Cache::getKey(serialize($params));

		if((false === (Cache::check($cacheKey))) || $params['reload'])  {
            /** @var DataManager $class */
			$class  = $params['class'];
			$method = $params['method'];

			if (!method_exists($class, $method))
				return self::getEmptyResult($params['empty']);

			$query = array(
				'filter'    => $params['filter'],
				'select'    => $params['select'],
				'order'     => $params['order'],
            );

			if (Dependence::d7CacheAvailable()) {

                if ($params['reload'])
                    $class::getEntity()->cleanCache();

                $query['cache'] = array('ttl' => 3600);
            }

			/** @var Result $rcElements */
			$rcElements = $class::$method($query);
			$result     = self::prepareDBResult($rcElements, $params['template'], $params['empty']);

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
     * @param null  $empty
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function prepareArrayResult(array $elements, $template, $empty = null)
	{
	    $keyTemplate    = key($template);
        $nameTemplate   = $template[$keyTemplate];

		$nameMask   = self::getMask($nameTemplate);
		$keyMask    = self::getMask($keyTemplate);

        $result = self::getEmptyResult($empty);

		foreach ($elements as $element)
		{
			$key    = self::prepareName($element, $keyMask, $keyTemplate);
			$name   = self::prepareName($element, $nameMask, $nameTemplate);

			$result[$key] = $name;
		}

		return $result;
	}

    /**
     * @param      $dbResult
     * @param      $template
     * @param null $empty
     * @return array
     * @throws ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function prepareDBResult($dbResult, $template, $empty = null)
    {
        if ($dbResult instanceof \CDBResult) {
            $elements = array();
            while ($row = $dbResult->Fetch())
                $elements[] = $row;
        } elseif ($dbResult instanceof Result) {
            $elements = $dbResult->fetchAll();
        } else {
            throw new ArgumentOutOfRangeException('dbResult');
        }

        return self::prepareArrayResult($elements, $template, $empty);
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
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	protected static function prepareEmpty(array $params = array())
	{
        $params = self::prepareParams($params);

		return self::getEmptyResult($params['empty']);
	}
}