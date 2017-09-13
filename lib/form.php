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

class Form extends Core
{
	/**
	 * @var string
	 */
	protected static $moduleName = 'form';

	/**
	 * @param array $params
	 * @return mixed
	 * @throws \Bitrix\Main\SystemException
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getWebForms(array $params = [])
	{
		self::checkModule();

		$rsForms = \CForm::GetList(
			$by = "s_id",
			$order = "ASC",
			[],
			$is_filtered
		);

		$params = self::prepareParams($params);

		$empty  = $params['empty'];
		$result = is_null($empty)
			? []
			: [0 => $empty];

		while ($form = $rsForms->Fetch())
			$result[$form['ID']] = '['.$form['ID'].'] ' . $form['NAME'];

		return $result;
	}

    /**
     * @param       $formId
     * @param array $params
     * @return null
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getQuestions($formId, array $params = [])
	{
		self::checkModule();

        $formId = intval($formId);
        if (!$formId)
            throw new ArgumentNullException('formId');

        if (empty($params['order']))
            $params['order'] = ['sort' => 'asc'];

        if (empty($params['template']))
            $params['template'] = ['{ID}' => '{TITLE} ({SID})'];

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, $formId, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $empty  = $params['empty'];
            $result = is_null($empty)
                ? []
                : [0 => $empty];

            $filter = $params['filter'];
            if (isset($params['add_filter']))
                $filter = array_merge($filter, $params['add_filter']);

            $is_filtered = null;

            $rsQuestions = \CFormField::GetList(
                $formId,
                "ALL",
                $by = key($params['order']),
                $order = $params['order'][$by],
                $filter,
                $is_filtered
            );

            $elements   = [];

            while ($question = $rsQuestions->Fetch())
                $elements[] = $question;

            $result = self::prepareResult($elements, key($params['template']),
                $params['template'][key($params['template'])], $result);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
	}
}