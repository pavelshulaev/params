<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::LoadMessages(__FILE__);

/**
 * Class rover_params
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
class rover_params extends CModule
{
    var $MODULE_ID	= "rover.params";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    /**
     * rover_params constructor.
     */
    function __construct()
    {
        global $paramsErrors;

		$arModuleVersion    = array();
        $paramsErrors       = array();

        require dirname(__FILE__) . "/version.php";

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION		= $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE	= $arModuleVersion["VERSION_DATE"];
        } else
            $paramsErrors[] = Loc::getMessage('rover_params__version_info_error');

        $this->MODULE_NAME			= Loc::getMessage('rover_params__name');
        $this->MODULE_DESCRIPTION	= Loc::getMessage('rover_params__descr');
        $this->PARTNER_NAME         = GetMessage('rover_params__partner_name');
        $this->PARTNER_URI          = GetMessage('rover_params__partner_uri');
	}

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    function DoInstall()
    {
        global $APPLICATION;
        $rights = $APPLICATION->GetGroupRight($this->MODULE_ID);

        if ($rights == "W")
            $this->ProcessInstall();
	}

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    function DoUninstall()
    {
        global $APPLICATION;
        $rights = $APPLICATION->GetGroupRight($this->MODULE_ID);

        if ($rights == "W")
            $this->ProcessUninstall();
    }

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    function GetModuleRightsList()
    {
        return array(
            "reference_id" => array("D", "R", "W"),
            "reference" => array(
                Loc::getMessage('rover_params__reference_deny'),
                Loc::getMessage('rover_params__reference_read'),
                Loc::getMessage('rover_params__reference_write')
            )
        );
    }

	/**
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	private function ProcessInstall()
    {
        global $APPLICATION, $paramsErrors;

        if (PHP_VERSION_ID < 50306)
            $paramsErrors[] = Loc::getMessage('rover_params__php_version_error');

        if (empty($paramsErrors))
            ModuleManager::registerModule($this->MODULE_ID);

	    $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_params__install_title"),
            dirname(__FILE__) . '/message.php');
    }

	/**
	 * @author Pavel Shulaev (https://rover-it.me)
	 */
	private function ProcessUninstall()
	{
	    ModuleManager::unRegisterModule($this->MODULE_ID);

        global $APPLICATION;
        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_params__uninstall_title"),
            dirname(__FILE__) . '/unMessage.php');
	}
}
