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
class Workflow extends Core
{
    /**
     * @var string
     */
	protected static $moduleName = 'workflow';

    /**
     * @param array $params
     * @return null
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getStatuses(array $params = array())
	{
		self::checkModule();

        if (empty($params['order']))
            $params['order'] = array('c_sort' => 'asc');

        if (empty($params['filter']))
            $params['filter'] = array("ACTIVE" => "Y");

        if (empty($params['template']))
            $params['template'] = array("{ID}" => "[{ID}] {TITLE}");

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if ((false === (Cache::check($cacheKey))) || $params['reload']) {

            $is_filtered = null;

            /** @var \CDBResult $rsWFStatus */
            $rsWFStatus = \CWorkflowStatus::GetList(
                $by = key($params['order']),
                $order = $params['order'][$by],
                $params['filter'],
                $is_filtered);
            $result = self::prepareDBResult($rsWFStatus, $params['template'], $params['empty']);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
	}
}