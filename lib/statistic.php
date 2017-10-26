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

use Rover\Params\Engine\Cache;
use Rover\Params\Engine\Core;

/**
 * Class Statistic
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Statistic extends Core
{
	/**
	 * @var string
	 */
	protected static $moduleName = 'statistic';

    /**
     * @param array $params
     * @return null
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getAdvCampaigns(array $params = array())
    {
        self::checkModule();

        if (empty($params['order']))
            $params['order'] = array("s_referer1" => 'desc');

        if (empty($params['template']))
            $params['template'] = array(
                '{ID}' => '[referer1="{REFERER1}", referer2="{REFERER2}"] {DESCRIPTION}'
            );

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $result     = self::getStartResult($params['empty']);

            $groups     = $companies  = \CAdv::GetSimpleList(
                $by = key($params['order']),
                $order = $params['order'][$by],
                $params['filter'],
                $is_filtered
            );
            $elements   = array();

            while ($group = $groups->Fetch())
                $elements[] = $group;

            $result = self::prepareResult($elements, $params['template'], $result);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
    }

    /**
     * @param array $params
     * @return null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getEventTypes(array $params = array())
    {
        if (empty($params['order']))
            $params['order'] = array("s_id" => 'asc');

        if (empty($params['template']))
            $params['template'] = array(
                '{ID}' => '[event1="{EVENT1}", event2="{EVENT2}"] {NAME}'
            );

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $result     = self::getStartResult($params['empty']);
            $groups     = $companies  = \CStatEventType::GetList(
                $by = key($params['order']),
                $order = $params['order'][$by],
                $params['filter'],
                $is_filtered
            );
            $elements   = array();

            while ($group = $groups->Fetch())
                $elements[] = $group;

            $result = self::prepareResult($elements, $params['template'], $result);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
    }

    /**
     * @param string $referer1
     * @param string $referer2
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getAdvCompanies($referer1 = '', $referer2 = '')
	{
	    $referer1 = trim($referer1);
	    $referer2 = trim($referer2);

	    $filter = array();
	    if (strlen($referer1))
	        $filter['REFERER1'] = $referer1;

	    if (strlen($referer2))
	        $filter['REFERER2'] = $referer2;

        return self::getAdvCampaigns(array('filter' => $filter));
	}
}