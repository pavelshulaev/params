<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 25.10.2016
 * Time: 20:35
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Core;

/**
 * Class Main
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Main extends Core
{
	/**
	 * @var
	 */
	protected static $currentSiteId;

	/**
	 * @param bool|false $hideAdmin
	 * @param array      $params
	 * @return array|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getSysGroups($hideAdmin = false, array $params = [])
	{
		$params['class']    = '\Bitrix\Main\GroupTable';
		$params['method']   = 'getList';

		if (!isset($params['filter']) && $hideAdmin)
			$params['filter'] = ['!ID' => 2];

		if (!isset($params['order']))
			$params['order'] = ['ID' => 'ASC'];

		return self::prepare($params);
	}

	/**
	 * @param string $lid
	 * @param array  $params
	 * @return array|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getEventTypes($lid = 'ru', array $params = [])
	{
		$params['class']    = '\Bitrix\Main\Mail\Internal\EventTypeTable';
		$params['method']   = 'getList';

		if (!isset($params['filter']))
			$params['filter'] = ['=LID' => $lid];

		if (!isset($params['template']))
			$params['template'] = ['{ID}' => '{NAME} [{EVENT_NAME}]'];

		return self::prepare($params);
	}

	/**
	 * @param $params
	 * @return array|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getSites(array $params = [])
	{
		$params['class']    = '\Bitrix\Main\SiteTable';
		$params['method']   = 'getList';

		if (!isset($params['template']))
			$params['template'] = ['{LID}' => '[{LID}] {NAME}'];

		return self::prepare($params);
	}

	/**
	 * @param array $params
	 * @return array|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getUsers(array $params = [])
	{
		$params['class']    = '\Bitrix\Main\UserTable';
		$params['method']   = 'getList';

		if (!isset($params['template']))
			$params['template'] = ['{ID}' => '{NAME} {LAST_NAME} ({LOGIN})'];

		if (!isset($params['order']))
			$params['order'] = ['ID' => 'ASC'];

		return self::prepare($params);
	}
}