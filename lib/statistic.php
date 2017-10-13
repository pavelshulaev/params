<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 10.12.2016
 * Time: 23:41
 *
 * @author Pavel Shulaev (https://rover-it.me)
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
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getAdvCompanies($referer1 = null, $referer2 = null)
	{
		self::checkModule();

		$filter = array();

		if (!is_null($referer1))
			$filter['REFERER1'] = $referer1;

		if (!is_null($referer2))
			$filter['REFERER2'] = $referer2;

        $is_filtered= null;
		$companies  = \CAdv::GetSimpleList($by = "s_referer1", $order = "desc", $filter, $is_filtered);
		$result     = array();

		while ($company = $companies->Fetch()){

			$name = '[referer1="' . $company['REFERER1'] . '"';

			if (strlen($company['REFERER2']))
				$name .= ', referer2="' . $company['REFERER2'] . '"';

			$name .= ']';

			if (strlen($company['DESCRIPTION']))
				$name .= ' ' . $company['DESCRIPTION'];

			$result[$company['ID']] = $name;
		}

		return $result;
	}
}