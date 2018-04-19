<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 17.11.2016
 * Time: 17:46
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Bitrix\Main\ArgumentNullException;
use Rover\Params\Engine\Cache;
use Rover\Params\Engine\Core;

/**
 * Class Form
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Form extends Core
{
	/**
	 * @var string
	 */
	protected static $moduleName = 'form';

    /**
     * @param array $params
     * @return null
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getWebForms(array $params = array())
	{
		self::checkModule();

        if (empty($params['order']))
            $params['order'] = array('s_id' => 'ASC');

        if (empty($params['template']))
            $params['template'] = array('{ID}' => '[{ID}] {NAME}');

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $rsForms = \CForm::GetList(
                $by = key($params['order']),
                $order = $params['order'][$by],
                $params['filter'],
                $is_filtered
            );

            $result = self::prepareDBResult($rsForms, $params['template'], $params['empty']);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
	}

    /**
     * @param       $formId
     * @param array $params
     * @return null
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getQuestions($formId, array $params = array())
	{
		self::checkModule();

        $formId = intval($formId);
        if (!$formId)
            throw new ArgumentNullException('formId');

        if (empty($params['order']))
            $params['order'] = array('sort' => 'asc');

        if (empty($params['template']))
            $params['template'] = array('{ID}' => '{TITLE} ({SID})');

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, $formId, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $is_filtered = null;

            $rsQuestions = \CFormField::GetList(
                $formId,
                "ALL",
                $by = key($params['order']),
                $order = $params['order'][$by],
                $params['filter'],
                $is_filtered
            );

            $result = self::prepareDBResult($rsQuestions, $params['template'], $params['empty']);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
	}
}