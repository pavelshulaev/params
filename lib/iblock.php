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

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\DB\Result;


class Iblock extends Core
{
	/**
	 * @var string
	 */
	protected static $moduleName = 'iblock';

	/**
	 * @param string $empty
	 * @param array  $template
	 * @return array|null
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getTypes($empty = '-', $template = ['{ID}' => '[{ID}] {NAME}'])
	{
		self::checkModule();

		$query = [
			'order'     => ['SORT' => 'ASC'],
			'filter'    => ['LANG_MESSAGE.LANGUAGE_ID' => LANGUAGE_ID],
			'select'    => ['ID', 'NAME' => 'LANG_MESSAGE.NAME'],
		];

		return self::prepare(TypeTable::getList($query), $empty, $template);
	}

	/**
	 * @param        $type
	 * @param null   $siteId
	 * @param string $empty
	 * @return array
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\LoaderException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getByType($type, $siteId = null, $empty = '-')
	{
		self::checkModule();

		if (!$type)
			return self::prepareEmpty($empty);

		$query = [
			'filter'    => [
				"=IBLOCK_TYPE_ID"   => $type,
				'=ACTIVE'           => 'Y'
			],
			'order'     => ['SORT' => 'ASC'],
			'select'    => ['ID', 'NAME']
		];

		if ($siteId)
			$query['filter']['=SITE_ID'] = $siteId;

		return self::prepare(IblockTable::getList($query), $empty);
	}

	/**
	 * @param           $iblockId
	 * @param string    $empty
	 * @param bool|true $withSubsections
	 * @return array
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getSections($iblockId, $empty = '-', $withSubsections = true)
	{
		self::checkModule();

		if (!(intval($iblockId)))
			return self::prepareEmpty($empty);

		$query = [
			'filter' => [
				'=IBLOCK_ID'    => $iblockId,
				'=ACTIVE'       => 'Y'
			],
			'order'     => ['SORT' => 'ASC'],
			'select'    => ['ID', 'NAME', 'IBLOCK_SECTION_ID']
		];

		if (!$withSubsections)
			$query['filter']['IBLOCK_SECTION_ID'] = false;

		$sections = SectionTable::getList($query);

		// without hierarchy
		if (!$withSubsections)
			return self::prepare($sections, $empty);

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

		$result = is_null($empty)
			? []
			: [0 => $empty];

		$template   = '[{ID}] {NAME}';
		$mask       = self::getMask($template);
		$cacheKey   = Cache::getKey(serialize($resultSort) . $empty . serialize($mask));

		if(false === (Cache::check($cacheKey))) {

			foreach ($resultSort as $element){

				$name = self::prepareName($element, $mask, $template);
				$name = str_pad('', $element['DEEP'] * 2, '.') . $name;
				$result[$element['ID']] = $name;
			}

			Cache::set($cacheKey, $result);
		}

		return Cache::get($cacheKey);
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
	 * @param        $iblockId
	 * @param null   $sectionId
	 * @param string $empty
	 * @return array
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\LoaderException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getElements($iblockId, $sectionId = null, $empty = '-')
	{
		self::checkModule();

		if (!(int)$iblockId)
			return self::prepareEmpty($empty);

		$query = [
			'filter'    => [
				'=IBLOCK_ID'    => $iblockId,
				'=ACTIVE'       => 'Y'
			],
			'order'     => ['SORT' => 'ASC'],
			'select'    => ['ID', 'NAME']
		];

		if (intval($sectionId))
			$query['filter']['IBLOCK_SECTION_ID'] = intval($sectionId);

		return self::prepare(ElementTable::getList($query), $empty);
	}

	/**
	 * @param        $iblockId
	 * @param string $empty
	 * @param array  $template
	 * @return array|null
	 * @throws SystemException
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public static function getProps($iblockId, $empty = '-', $template = ['{ID}' => '[{CODE}] {NAME}'])
	{
		self::checkModule();

		if (!intval($iblockId))
			return self::prepareEmpty($empty);

		$query = [
			'order' => ['ID' => 'ASC'],
			'filter' => [
				'=IBLOCK_ID'    => $iblockId,
				'=ACTIVE'       => 'Y'
			],
			'select' => ['ID', 'NAME', 'CODE']
		];

		return self::prepare(PropertyTable::getList($query), $empty, $template);
	}
}