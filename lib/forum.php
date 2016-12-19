<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 19.12.2016
 * Time: 17:33
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Core;

class Forum extends Core
{
	protected static $moduleName = 'forum';

	protected static $groups;
	/**
	 * @param array $params
	 * @return array|null
	 * @throws \Bitrix\Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getGroups(array $params = [])
	{
		self::checkModule();

		// forum d7 is not ready...
		/*$query = [
			'order'     => ['SORT' => 'ASC'],
			'select'    => ['ID', 'NAME'],
		];

		$params['class']    = '\Bitrix\Forum\GroupTable';
		$params['method']   = 'getList';
		$params['query']    = $query;

		return self::prepare($params);*/

		if (is_null(self::$groups) || (isset($params['reload']) && $params['reload'])) {

			$order  = ['ID' => 'ASC'];
			$groups = \CForumGroup::GetList($order, []);
			self::$groups = [];

			while ($group = $groups->Fetch()){
				$groupFull = \CForumGroup::GetByIDEx($group['ID'], 'ru');

				self::$groups[$group['ID']] = $groupFull['NAME'] . ' [' . $group['ID'] . ']';
			}
		}

		return self::$groups;


	}
}