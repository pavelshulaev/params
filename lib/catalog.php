<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 25.10.2016
 * Time: 20:35
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Core;

/**
 * Class Main
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Catalog extends Core
{
	/**
	 * @var
	 */
	protected static $moduleName = 'catalog';

	/**
	 * @param array $params
	 * @return array|null
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getPriceGroups(array $params = [])
	{
		self::checkModule();

		$params['class']    = '\Bitrix\Catalog\GroupTable';
		$params['method']   = 'getList';

		return self::prepare($params);
	}
}