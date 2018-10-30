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

use Rover\Params\Engine\Cache;
use Rover\Params\Engine\Core;

/**
 * Class Workflow
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Learning extends Core
{
    /** @var string */
	protected static $moduleName = 'learning';

    /**
     * @param array $params
     * @return null
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getTests(array $params = array())
	{
		self::checkModule();

        if (empty($params['order']))
            $params['order'] = array('ID' => 'ASC');

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $groups = \CTest::GetList($params['order'], $params['filter']);
            $result = self::prepareDBResult($groups, $params['template'], $params['empty']);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
	}
}