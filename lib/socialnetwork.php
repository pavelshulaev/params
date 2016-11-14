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

/**
 * Class Socialnetwork
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (http://rover-it.me)
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
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getWorkGroups(array $params = [])
	{
		self::checkModule();

		$query = [
			'order'     => ['ID' => 'ASC'],
			'select'    => ['ID', 'NAME']
		];

		$params['class']    = '\Bitrix\Socialnetwork\WorkgroupTable';
		$params['method']   = 'getList';
		$params['query']    = $query;

		return self::prepare($params);
	}
}