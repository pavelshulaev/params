<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 10.12.2016
 * Time: 23:41
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Core;

class Statistic extends Core
{
	/**
	 * @var string
	 */
	protected static $moduleName = 'statistic';

	/**
	 * @param null $referer1
	 * @param null $referer2
	 * @return array
	 * @throws \Bitrix\Main\SystemException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getAdvCompanies($referer1 = null, $referer2 = null)
	{
		self::checkModule();

		$filter = [];

		if (!is_null($referer1))
			$filter['REFERER1'] = $referer1;

		if (!is_null($referer2))
			$filter['REFERER2'] = $referer2;

		$companies  = \CAdv::GetSimpleList($by = "s_referer1", $order = "desc", $filter, $is_filtered);
		$result     = [];

		while ($company = $companies->Fetch())
			$result[$company['ID']] = $company['DESCRIPTION']
				. ' (referer1=' . $company['REFERER1'] . ', referer2=' . $company['REFERER2'] . ')';

		return $result;
	}
}