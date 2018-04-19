<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 31.10.2017
 * Time: 20:25
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Bitrix\Main\ArgumentNullException;
use Rover\Params\Engine\Cache;
use Rover\Params\Engine\Core;

/**
 * Class Support
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Subscribe extends Core
{
    /**
     * @var string
     */
    protected static $moduleName = 'subscribe';
    /**
     * @param array $params
     * @return null
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getRubrics(array $params = array())
    {
        self::checkModule();

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $dbelements = \CRubric::GetList($params['order'], $params['filter']);
            $result     = self::prepareDBResult($dbelements, $params['template'], $params['empty']);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
    }

}