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
class Vote extends Core
{
    /**
     * @var string
     */
	protected static $moduleName = 'vote';

    /**
     * @param array $params
     * @return null
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getVotes(array $params = array())
	{
		self::checkModule();

        $params['class']    = '\Bitrix\Vote\VoteTable';
        $params['method']   = 'getList';

        if (empty($params['order']))
            $params['order'] = array('C_SORT' => 'asc');

        if (empty($params['filter']))
            $params['filter'] = array("ACTIVE" => "Y");

        if (empty($params['template']))
            $params['template'] = array("{ID}" => "[{ID}] {TITLE}");

        return self::prepare($params);
	}
}