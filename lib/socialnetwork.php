<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 25.10.2016
 * Time: 20:31
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Bitrix\Main\SystemException;
use Rover\Params\Engine\Core;
/**
 * Class Socialnetwork
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Socialnetwork extends Core
{
	/**
	 * @var string
	 */
	protected static $moduleName = 'socialnetwork';

	/**
	 * @param array $params
	 * @return array|null
	 * @throws SystemException
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getWorkGroups(array $params = [])
	{
		self::checkModule();

		$params['class']    = '\Bitrix\Socialnetwork\WorkgroupTable';
		$params['method']   = 'getList';

		if (!isset($params['order']))
			$params['order'] = ['ID' => 'asc'];

		return self::prepare($params);
	}
}