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

use \Bitrix\Main\GroupTable;

class Main
{
	/**
	 * @param bool|false $hideAdmin
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getSysGroups($hideAdmin = false)
	{
		$query = [
			'order' => ['ID' => 'ASC'],
			'select' => ['ID', 'NAME']
		];

		$sysGroups  = GroupTable::getList($query);
		$result     = [];

		while($sysGroup = $sysGroups->fetch()){
			if ($hideAdmin && $sysGroup['ID'] == 2)
				continue;

			$result[$sysGroup['ID']] = $sysGroup['NAME'] . ' [' . $sysGroup['ID'] . ']';
		}

		return $result;
	}
}