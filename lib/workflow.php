<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 19.12.2016
 * Time: 16:33
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Core;

class Workflow extends Core
{
	protected static $moduleName = 'workflow';

	protected static $statuses;

	/**
	 * @param array $params
	 * @return array
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getStatuses(array $params = [])
	{
		self::checkModule();

		if (is_null(self::$statuses) || (isset($params['reload']) && $params['reload'])) {

			self::$statuses = [];

			if (array_key_exists('empty', $params) && $params['empty'] !== null)
			{
				if (!$params['empty'])
					$params['empty'] = '-';

				self::$statuses[0] = $params['empty'];
			}

			$rsWFStatus = \CWorkflowStatus::GetList($by = "c_sort", $order = "asc", ["ACTIVE" => "Y"], $is_filtered);

			while ($arWFS = $rsWFStatus->Fetch())
				self::$statuses[$arWFS["ID"]] = $arWFS["TITLE"];
		}

		return self::$statuses;
	}
}