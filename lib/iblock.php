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
	 * @param            $iblockId
	 * @param bool|false $iblockSectionId
	 * @param array      $params
	 * @return array|null
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getSections($iblockId, $iblockSectionId = false, array $params = [])
	{
		self::checkModule();

		if (!(intval($iblockId)))
			return self::prepareEmpty($params);

		$params['class']    = '\Bitrix\Iblock\SectionTable';
		$params['method']   = 'getList';

		if (!isset($params['filter'])) {
			$filter = [
				'=IBLOCK_ID'    => $iblockId,
				'=ACTIVE'       => 'Y'
			];

			if (!is_null($iblockSectionId))
				$filter['=IBLOCK_SECTION_ID'] = $iblockSectionId;

			$params['filter']    = $filter;
		}

		if (!isset($params['select']))
			$params['select'] = self::getSelectFromTemplate($params['template']);

		// with hierarchy
		if (is_null($iblockSectionId)){

			$query = [
				'filter' => $params['filter'],
				'order'  => ['ID' => 'ASC'],
				'select' => $params['select']
			];

			$sections   = SectionTable::getList($query);
			$resultRaw  = self::addChildInfo($sections);
			$resultSort = [];

			// filter by hierarchy
			foreach ($resultRaw as $section)
			{
				if (!empty($section['IBLOCK_SECTION_ID']))
					continue;

				$resultSort = array_merge($resultSort,
					self::getWithChild($section['ID'], $resultRaw));
			}

			$params['elements'] = $resultSort;
		}

		return self::prepare($params);
	}

	/**
	 * @param Result $sections
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function addChildInfo(Result $sections)
	{
		$resultRaw = [];

		while ($section = $sections->fetch())
			$resultRaw[$section['ID']] = $section;

		//add info about childs
		foreach ($resultRaw as $sectionId => &$sectionRaw) {
			if (empty($sectionRaw['IBLOCK_SECTION_ID']))
				continue;

			if (!isset($resultRaw[$sectionRaw['IBLOCK_SECTION_ID']]))
				continue;

			$resultRaw[$sectionRaw['IBLOCK_SECTION_ID']]['CHILDS'][]
				= $sectionRaw['ID'];
		}

		return $resultRaw;
	}

	/**
	 * @param     $sectionId
	 * @param     $sections
	 * @param int $deep
	 * @return array
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	protected static function getWithChild($sectionId, $sections, $deep = 0)
	{
		$result = [];

		if (!isset($sections[$sectionId]))
			return $result;

		$sections[$sectionId]['DEEP'] = $deep;
		$sections[$sectionId]['NAME'] = str_pad('', $deep * 1, '.')
			. $sections[$sectionId]['NAME'];

		$result[] = $sections[$sectionId];

		// if section has no childs
		if (!isset($sections[$sectionId]['CHILDS'])
			|| empty($sections[$sectionId]['CHILDS']))
			return $result;

		foreach ($sections[$sectionId]['CHILDS'] as $childId)
			$result = array_merge($result, self::getWithChild($childId, $sections, $deep + 1));

		return $result;
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