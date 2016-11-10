<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 25.10.2016
 * Time: 20:31
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Params;

use Bitrix\Main\SystemException;
use \Bitrix\Socialnetwork\WorkgroupTable;

class Socialnetwork extends Core
{
	protected static $moduleName = 'socialnetwork';

	/**
	 * @return array
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\LoaderException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getWorkGroups()
	{
		self::checkModule();

		$query = [
			'order'     => ['ID' => 'ASC'],
			'select'    => ['ID', 'NAME']
		];

		$workGroups = WorkgroupTable::getList($query);
		$result     = [];

		while($workGroup = $workGroups->fetch())
			$result[$workGroup['ID']] = $workGroup['NAME'] . ' [' . $workGroup['ID'] . ']';

		return $result;
	}
}