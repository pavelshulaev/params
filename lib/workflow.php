<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 19.12.2016
 * Time: 16:33
 *
 * @author Pavel Shulaev (http://rover-it.me)
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
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getStatuses(array $params = [])
	{
		self::checkModule();

		if (is_null(self::$statuses) || (isset($params['reload']) && $params['reload'])) {
			$rsWFStatus = \CWorkflowStatus::GetList($by = "c_sort", $order = "asc", ["ACTIVE" => "Y"], $is_filtered);
			self::$statuses = [];

			while ($arWFS = $rsWFStatus->Fetch())
				self::$statuses[$arWFS["ID"]] = $arWFS["TITLE"];
		}

		return self::$statuses;
	}
}