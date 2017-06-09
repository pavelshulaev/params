<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 09.10.2016
 * Time: 14:58
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Params;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\DB\Result;
use Rover\Params\Engine\Cache;
use Rover\Params\Engine\Core;
/**
 * Class Iblock
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Iblock extends Core
{
	/**
	 * @var string
	 */
	protected static $moduleName = 'iblock';

	/**
	 * @param array $params
	 * @return array|null
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getTypes(array $params = [])
	{
		self::checkModule();

		$params['class']    = '\Bitrix\Iblock\TypeTable';
		$params['method']   = 'getList';

		if (!isset($params['filter']))
			$params['filter'] = ['=LANG_MESSAGE.LANGUAGE_ID' => LANGUAGE_ID];

		if (!isset($params['select']))
			$params['select'] = ['ID', 'NAME' => 'LANG_MESSAGE.NAME'];

		return self::prepare($params);
	}

	/**
	 * @param      $type
	 * @param null $siteId
	 * @param      $params
	 * @return array|null
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getByType($type, $siteId = null, array $params = [])
	{
		self::checkModule();

		if (!$type)
			return self::prepareEmpty($params);

		if (!isset($params['filter'])) {

			$filter = [
				"=IBLOCK_TYPE_ID"   => $type,
				'=ACTIVE'           => 'Y'
			];

			if ($siteId)
				$filter['=SITE_ID'] = $siteId;
			$params['filter']   = $filter;
		}

		$params['class']    = '\Bitrix\Iblock\IblockTable';
		$params['method']   = 'getList';

		return self::prepare($params);
	}

	/**
	 * @param           $iblockId
	 * @param bool|true $withSubsections
	 * @param array     $params
	 * @return array|null
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getSections($iblockId, $withSubsections = true, array $params = [])
	{
		self::checkModule();

		if (!(intval($iblockId)))
			return self::prepareEmpty($params);

		$params['class']    = '\Bitrix\Iblock\SectionTable';
		$params['method']   = 'getList';

		if (!isset($params['filter']))
			$params['filter']= [
				'=IBLOCK_ID'            => $iblockId,
				//'=ACTIVE'               => 'Y',
				'=IBLOCK_SECTION_ID'    => null,
			];

		if (!$withSubsections)
			return self::prepare($params);

		if (!isset($params['template']))
			$params['template'] = ['{ID}' => '{NAME} [{ID}]'];

		$params = self::prepareParams($params);

		$params['select'] = array_merge(['ID', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL'],
			$params['select']);

		$cacheKey = Cache::getKey(serialize($params));

		if((false === (Cache::check($cacheKey))) || $params['reload']) {

			$empty  = $params['empty'];
			$result = is_null($empty)
				? []
				: [0 => $empty];

			$query = [
				'filter'    => $params['filter'],
				'order'     => $params['order'],
				'select'    => $params['select']
			];

			$parentSections = SectionTable::getList($query);
			$preResult = [];

			while ($parentSection = $parentSections->fetch())
			{
				$preResult[] = $parentSection;

				$subQuery = [
					'filter' => [
						'IBLOCK_ID'     => $iblockId,
						'>LEFT_MARGIN'  => $parentSection['LEFT_MARGIN'],
						'<RIGHT_MARGIN' => $parentSection['RIGHT_MARGIN'],
					],
					'select'    => $params['select'],
					'order'     => ['LEFT_MARGIN' => 'asc']
				];

				$childSections = SectionTable::getList($subQuery);

				while ($childSection = $childSections->fetch()){
					$childSection['NAME'] = str_pad('', $childSection['DEPTH_LEVEL'] - 1, '.')
						. $childSection['NAME'];

					$preResult[] = $childSection;
				}

				$result = self::prepareResult($preResult, key($params['template']),
					$params['template'][key($params['template'])], $result);
			}

			Cache::set($cacheKey, $result);
		}

		return Cache::get($cacheKey);
	}
	
	/**
	 * @param      $iblockId
	 * @param null $sectionId
	 * @param      $params
	 * @return array|null
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getElements($iblockId, $sectionId = null, array $params = [])
	{
		self::checkModule();

		if (!(int)$iblockId)
			return self::prepareEmpty($params);

		$params['class']    = '\Bitrix\Iblock\ElementTable';
		$params['method']   = 'getList';

		if (!isset($params['filter'])) {
			$filter = [
				'=IBLOCK_ID'    => $iblockId,
				'=ACTIVE'       => 'Y'
			];

			if (intval($sectionId))
				$filter['=IBLOCK_SECTION_ID'] = intval($sectionId);

			$params['filter']   = $filter;
		}

		return self::prepare($params);
	}

	/**
	 * @param $iblockId
	 * @param $params
	 * @return array|null
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getProps($iblockId, array $params = [])
	{
		self::checkModule();

		if (!intval($iblockId))
			return self::prepareEmpty($params);

		$params['class']    = '\Bitrix\Iblock\PropertyTable';
		$params['method']   = 'getList';

		if (!isset($params['filter']))
			$params['filter']   = [
				'=IBLOCK_ID'    => $iblockId,
				'=ACTIVE'       => 'Y',
			];

		return self::prepare($params);
	}
}