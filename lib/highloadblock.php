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
class HighloadBlock extends Core
{
	/**
	 * @var
	 */
	protected static $moduleName = 'highloadblock';

	/**
	 * @param array $params
	 * @return array|null
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getBlocks(array $params = array())
	{
		self::checkModule();

		$params['class']    = '\Bitrix\Highloadblock\HighloadBlockTable';
		$params['method']   = 'getList';

		if (!isset($params['order']))
			$params['order'] = array('ID' => 'asc');

		return self::prepare($params);
	}
}