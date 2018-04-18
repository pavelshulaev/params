<?php
namespace Rover\Params\Engine;
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 16.12.2016
 * Time: 18:30
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
/**
 * Class Dependence
 *
 * @package Rover\AmoCRM\Config
 * @author  Pavel Shulaev (https://rover-it.me)
 */

class Dependence
{
    const CAN_USE_CACHE_MAIN_VER = '16.5.9';

	/**
	 * @return $this
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function d7CacheAvailable()
	{
		return CheckVersion(self::getVersion('main'), self::CAN_USE_CACHE_MAIN_VER);
	}

	/**
	 * @param $moduleName
	 * @return bool|string
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	public static function getVersion($moduleName)
	{
		$moduleName = preg_replace("/[^a-zA-Z0-9_.]+/i", "", trim($moduleName));
		if ($moduleName == '')
			return false;

		if (!ModuleManager::isModuleInstalled($moduleName))
			return false;

		if ($moduleName == 'main')
		{
			if (!defined("SM_VERSION"))
				include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/version.php");

			return SM_VERSION;
		}

		$modulePath = getLocalPath("modules/".$moduleName."/install/version.php");
		if ($modulePath === false)
			return false;

		$arModuleVersion = array();
		include($_SERVER["DOCUMENT_ROOT"] . $modulePath);

		return array_key_exists("VERSION", $arModuleVersion)
			? $arModuleVersion["VERSION"]
			: false;
	}
}