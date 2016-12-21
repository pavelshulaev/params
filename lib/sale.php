<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 21.12.2016
 * Time: 16:43
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Core;

/**
 * Class Sale
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Sale extends Core
{
	/**
	 * @var string
	 */
	protected static $moduleName = 'sale';

	/**
	 * @param array $params
	 * @return array|null
	 * @throws \Bitrix\Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getOrderStatuses(array $params = [])
	{
		self::checkModule();

		$lid = $params['LID'] ?: LANGUAGE_ID;

		$query = [
			'order'     => ['STATUS.SORT' => 'ASC'],
			'select'    => ['ID' => 'STATUS_ID', 'NAME'],
			'filter'    => ['=LID' => $lid]
		];

		$params['class']    = 'Bitrix\Sale\Internals\StatusLangTable';
		$params['method']   = 'getList';
		$params['query']    = $query;

		return self::prepare($params);
	}
}