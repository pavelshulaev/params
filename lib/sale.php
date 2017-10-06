<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 21.12.2016
 * Time: 16:43
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Core;

/**
 * Class Sale
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
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
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getOrderStatuses(array $params = array())
	{
		self::checkModule();

		$lid = $params['LID'] ?: LANGUAGE_ID;

		$params['class']    = 'Bitrix\Sale\Internals\StatusLangTable';
		$params['method']   = 'getList';

		if (!isset($params['filter']))
			$params['filter'] = array('=LID' => $lid);

		if (!isset($params['order']))
			$params['order'] = array('STATUS.SORT' => 'ASC');

		if (!isset($params['select']))
			$params['select'] = array('ID' => 'STATUS_ID', 'NAME');

		return self::prepare($params);
	}

    /**
     * @param array $params
     * @return array|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getPaySystems(array $params = array())
    {
        self::checkModule();

        $params['class']    = 'Bitrix\Sale\Internals\PaySystemActionTable';
        $params['method']   = 'getList';

        if (!isset($params['order']))
            $params['order'] = array('NAME' => 'ASC');

        if (!isset($params['template']))
            $params['template'] = array('{PAY_SYSTEM_ID}' => '{NAME} [{PAY_SYSTEM_ID}]');

        return self::prepare($params);
    }
}