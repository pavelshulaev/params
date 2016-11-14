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

class Main extends Core
{
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
}