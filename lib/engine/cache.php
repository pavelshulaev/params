<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.11.2016
 * Time: 21:52
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params\Engine;

class Cache
{
	/**
	 * @var array
	 */
	protected static $cache;

	/**
	 * @param $key
	 * @param $value
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function set($key, $value)
	{
		self::$cache[$key] = $value;
	}

	/**
	 * @param $key
	 * @return bool
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function check($key)
	{
		return isset(self::$cache[$key]);
	}

	/**
	 * @param $key
	 * @return null
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function get($key)
	{
		if (!self::check($key))
			return false;

		return self::$cache[$key];
	}

	/**
	 * @return string
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getKey()
	{
		return md5(implode('', func_get_args()));
	}
}