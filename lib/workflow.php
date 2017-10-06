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

class Workflow extends Core
{
    /**
     * @var string
     */
	protected static $moduleName = 'workflow';

	/**
	 * @param array $params
	 * @return array
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getStatuses(array $params = array())
	{
		self::checkModule();

        if (empty($params['order']))
            $params['order'] = array('c_sort' => 'asc');

        if (empty($params['filter']))
            $params['filter'] = array("ACTIVE" => "Y");

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if ((false === (Cache::check($cacheKey))) || $params['reload']) {

            $result = self::getStartResult($params['empty']);

            $rsWFStatus = \CWorkflowStatus::GetList(
                $by = key($params['order']),
                $order = $params['order'][$by],
                $params['filter'],
                $is_filtered);

            $elements   = array();

            while ($question = $rsWFStatus->Fetch())
                $elements[] = $question;

            $result = self::prepareResult($elements, key($params['template']),
                $params['template'][key($params['template'])], $result);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
	}
}