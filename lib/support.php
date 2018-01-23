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
class Support extends Core
{
    /**
     * @var string
     */
    protected static $moduleName = 'support';

    /**
     * @param array $params
     * @return null
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getCategory(array $params = array())
    {
        return self::getDictionaryByType('C', $params);
    }

    /**
     * @param array $params
     * @return null
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getCriticality(array $params = array())
    {
        return self::getDictionaryByType('K', $params);
    }

    /**
     * @param       $type
     * @param array $params
     * @return null
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getDictionaryByType($type, array $params = array())
    {
        self::checkModule();

        $type = trim($type);
        if (!strlen($type))
            throw new ArgumentNullException('dictionary type');

        $params['filter']['TYPE'] = $type;

        if (empty($params['order']))
            $params['order'] = array("s_c_sort" => 'asc');

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $result     = self::getStartResult($params['empty']);

            $dbelements = \CTicketDictionary::GetList(
                $by = key($params['order']),
                $order = $params['order'][$by],
                $params['filter'],
                $is_filtered
            );

            $elements   = array();

            while ($group = $dbelements->Fetch())
                $elements[] = $group;

            $result = self::prepareResult($elements, $params['template'], $result);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
    }

    /**
     * @param array $params
     * @return null
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getSla(array $params = array())
    {
        self::checkModule();

        if (empty($params['order']))
            $params['order'] = array("PRIORITY" => 'DESC');

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $result     = self::getStartResult($params['empty']);
            $dbelements = \CTicketSLA::GetList($params['order'], $params['filter'], $is_filtered);
            $elements   = array();

            while ($group = $dbelements->Fetch())
                $elements[] = $group;

            $result = self::prepareResult($elements, $params['template'], $result);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
    }
}