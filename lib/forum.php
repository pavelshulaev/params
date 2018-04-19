<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 19.12.2016
 * Time: 17:33
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Cache;
use Rover\Params\Engine\Core;

/**
 * Class Forum
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Forum extends Core
{
    /**
     * @var string
     */
	protected static $moduleName = 'forum';

	/**
	 * @param array $params
	 * @return array|null
	 * @throws \Bitrix\Main\SystemException
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getGroups(array $params = array())
	{
		self::checkModule();

		// forum d7 is not ready...
		/*$query = [
			'order'     => ['SORT' => 'ASC'],
			'select'    => ['ID', 'NAME'],
		];

		$params['class']    = '\Bitrix\Forum\GroupTable';
		$params['method']   = 'getList';
		$params['query']    = $query;

		return self::prepare($params);*/

        if (empty($params['order']))
            $params['order'] = array('ID' => 'ASC');

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

			$groups = \CForumGroup::GetList($params['order'], $params['filter']);
            $result = self::prepareDBResult($groups, $params['template'], $params['empty']);

            Cache::set($cacheKey, $result);
		}

        return Cache::get($cacheKey);
	}
}