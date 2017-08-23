<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 23.08.2017
 * Time: 16:31
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Rover\Params\Engine\Cache;
use Rover\Params\Engine\Core;

/**
 * Class Blog
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Blog extends Core
{
    /**
     * @var string
     */
    protected static $moduleName = 'blog';

    /**
     * @param array $params
     * @return null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getBlogs(array $params = [])
    {
        self::checkModule();

        if (empty($params['order']))
            $params['order'] = ['URL' => 'ASC'];

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $empty  = $params['empty'];
            $result = is_null($empty)
                ? []
                : [0 => $empty];

            $filter = $params['filter'];
            if (isset($params['add_filter']))
                $filter = array_merge($filter, $params['add_filter']);

            $blogs      = \CBlog::GetList($params['order'], $filter, false, false, $params['select']);
            $elements   = [];

            while ($blog = $blogs->Fetch())
                $elements[] = $blog;

            $result = self::prepareResult($elements, key($params['template']),
                $params['template'][key($params['template'])], $result);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
    }
}