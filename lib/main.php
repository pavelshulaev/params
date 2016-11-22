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
	protected static $currentSiteId;
	/**
	 * @param bool|false $hideAdmin
	 * @param array      $params
	 * @return array|null
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getSysGroups($hideAdmin = false, array $params = [])
	{
		$query = [
			'order'     => ['ID' => 'ASC'],
			'select'    => ['ID', 'NAME']
		];

		if ($hideAdmin)
			$query['filter'] = ['!ID' => 2];

		$params['class']    = '\Bitrix\Main\GroupTable';
		$params['method']   = 'getList';
		$params['query']    = $query;

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
		$query = [
			'order'     => ['ID' => 'ASC'],
			'filter'    => ['=LID' => $lid],
			'select'    => ['ID', 'NAME', 'EVENT_NAME']
		];

		$params['class']    = '\Bitrix\Main\Mail\Internal\EventTypeTable';
		$params['method']   = 'getList';
		$params['query']    = $query;

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
		$query = [
			'order'     => ['SORT' => 'desc'],
			'select'    => ['LID', 'NAME']
		];

		$params['class']    = '\Bitrix\Main\SiteTable';
		$params['method']   = 'getList';
		$params['query']    = $query;

		if (!isset($params['template']))
			$params['template'] = ['{LID}' => '[{LID}] {NAME}'];

		return self::prepare($params);
	}
}