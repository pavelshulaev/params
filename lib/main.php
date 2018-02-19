<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 25.10.2016
 * Time: 20:35
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Params;

use Bitrix\Main\ArgumentNullException;
use Rover\Params\Engine\Cache;
use Rover\Params\Engine\Core;

/**
 * Class Main
 *
 * @package Rover\Params
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Main extends Core
{
	/**
	 * @var
	 */
	protected static $currentSiteId;

    /**
     * @param bool  $hideAdmin
     * @param array $params
     * @return array|null
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getSysGroups($hideAdmin = false, array $params = array())
	{
		$params['class']    = '\Bitrix\Main\GroupTable';
		$params['method']   = 'getList';

		if (!isset($params['filter']) && $hideAdmin)
			$params['filter'] = array('!ID' => 1);

		if (!isset($params['order']))
			$params['order'] = array('ID' => 'ASC');

		return self::prepare($params);
	}

    /**
     * @param       $entityId
     * @param array $params
     * @return null
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getUserFields($entityId, array $params = array())
    {
        $entityId = trim($entityId);
        if (!strlen($entityId))
            throw new ArgumentNullException('entityId');

        if (!isset($params['template']))
            $params['template'] = array('{ID}' => '[{ID}] {EDIT_FORM_LABEL}');

        $params     = self::prepareParams($params);
        $cacheKey   = Cache::getKey(__METHOD__, serialize($params));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $result = self::getStartResult($params['empty']);

            $itemId = isset($params['filter']['ITEM_ID'])
                ? $params['filter']['ITEM_ID']
                : 0;

            $langId = isset($params['filter']['LANG_ID'])
                ? $params['filter']['LANG_ID']
                : LANGUAGE_ID;

            $userId = isset($params['filter']['USER_ID'])
                ? $params['filter']['USER_ID']
                : false;

            global $USER_FIELD_MANAGER;
            $arrUF = $USER_FIELD_MANAGER->GetUserFields($entityId, $itemId, $langId, $userId);

            $result = self::prepareResult($arrUF, $params['template'], $result);

            Cache::set($cacheKey, $result);
        }

        return Cache::get($cacheKey);
    }

    /**
     * @param       $userId
     * @param bool  $hideAdmin
     * @param array $params
     * @return array|null
     * @throws ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getUserSysGroups($userId, $hideAdmin = false, array $params = array())
	{
		$userId = intval($userId);
		if (!$userId)
			throw new ArgumentNullException('userId');

		if (!isset($params['add_filter']))
			$params['add_filter'] = array();

		$params['add_filter']['=ID'] = \CUser::GetUserGroup($userId);

		return self::getSysGroups($hideAdmin, $params);
	}

    /**
     * @param string $lid
     * @param array  $params
     * @return array|null
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getEventTypes($lid = 'ru', array $params = array())
	{
		$params['class']    = '\Bitrix\Main\Mail\Internal\EventTypeTable';
		$params['method']   = 'getList';

		if (!isset($params['filter']))
			$params['filter'] = array('=LID' => $lid);

		if (!isset($params['template']))
			$params['template'] = array('{ID}' => '{NAME} [{EVENT_NAME}]');

		return self::prepare($params);
	}

    /**
     * @param string $siteId
     * @param null   $eventName
     * @param array  $params
     * @return array|null
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getEventMessages($siteId = '', $eventName = null, array $params = array())
	{
        $params['class']    = '\Bitrix\Main\Mail\Internal\EventMessageTable';
        $params['method']   = 'getList';

        if (!isset($params['filter']))
            $params['filter'] = array();

        if (!empty($eventName))
            $params['filter']['=EVENT_NAME'] = $eventName;

        $siteId = trim($siteId);
        if ($siteId)
            $params['filter']['=LID'] = $siteId;

        if (!isset($params['template']))
            $params['template'] = array('{ID}' => '[{EVENT_NAME}] {SUBJECT}');

        if (!isset($params['sort']))
            $params['order'] = array('EVENT_NAME' => 'asc');

        return self::prepare($params);
	}


    /**
     * @param array $params
     * @return array|null
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getSites(array $params = array())
	{
		$params['class']    = '\Bitrix\Main\SiteTable';
		$params['method']   = 'getList';

		if (!isset($params['template']))
			$params['template'] = array('{LID}' => '[{LID}] {NAME}');

		return self::prepare($params);
	}

    /**
     * @param array $params
     * @return array|null
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getUsers(array $params = array())
	{
		$params['class']    = '\Bitrix\Main\UserTable';
		$params['method']   = 'getList';

		if (!isset($params['template']))
			$params['template'] = array('{ID}' => '{NAME} {LAST_NAME} ({LOGIN})');

		if (!isset($params['order']))
			$params['order'] = array('ID' => 'ASC');

		return self::prepare($params);
	}

    /**
     * @param       $object
     * @param array $params
     * @return array|null
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public static function getUserType($object, array $params = array())
    {
        $params['class']    = '\Bitrix\Main\UserFieldTable';
        $params['method']   = 'getList';

        if (!isset($params['template']))
            $params['template'] = array('{ID}' => '{FIELD_NAME} [{ID}]');

        if (!isset($params['order']))
            $params['order'] = array('ID' => 'ASC');

        if (!isset($params['filter']))
            $params['filter'] = array('=ENTITY_ID' => $object);

        return self::prepare($params);
    }

    /**
     * @param       $groupsIds
     * @param array $params
     * @return array|null
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getGroupUsers($groupsIds, array $params)
    {
        if (empty($groupsIds))
            return array();

        if (!is_array($groupsIds))
            $groupsIds = array($groupsIds);

        $cacheKey = Cache::getKey(__METHOD__, serialize($groupsIds));

        if((false === (Cache::check($cacheKey))) || $params['reload']) {

            $usersIds = array();
            foreach ($groupsIds as $groupId)
                $usersIds = array_merge($usersIds, \CGroup::GetGroupUser($groupId));

            $usersIds = array_unique($usersIds);

            Cache::set($cacheKey, $usersIds);
        }

        $usersIds               = Cache::get($cacheKey);
        $params['add_filter']   = array('=ID' => $usersIds);

        return self::getUsers($params);
    }
}