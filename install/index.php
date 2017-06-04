<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::LoadMessages(__FILE__);

class rover_params extends CModule
{
    var $MODULE_ID	= "rover.params";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
	
    function __construct()
    {
        global $errors;

		$arModuleVersion = array();

        require(__DIR__ . "/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION		= $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE	= $arModuleVersion["VERSION_DATE"];
        } else
            $errors[] = Loc::getMessage('rover_params__version_info_error');

        $this->MODULE_NAME			= Loc::getMessage('rover_params__name');
        $this->MODULE_DESCRIPTION	= Loc::getMessage('rover_params__descr');
        $this->PARTNER_NAME         = GetMessage('rover_params__partner_name');
        $this->PARTNER_URI          = GetMessage('rover_params__partner_uri');
	}

    /**
     * @author Shulaev (pavel.shulaev@gmail.com)
     */
    function DoInstall()
    {
        global $APPLICATION;
        $rights = $APPLICATION->GetGroupRight($this->MODULE_ID);

        if ($rights == "W")
            $this->ProcessInstall();
	}

    /**
     * @author Shulaev (pavel.shulaev@gmail.com)
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
     * @author Shulaev (pavel.shulaev@gmail.com)
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
	 * »нсталл€ци€ файлов и зависимотей, регистраци€ модул€
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	private function ProcessInstall()
    {
        global $APPLICATION, $errors;

        if (PHP_VERSION_ID < 50400)
            $errors[] = Loc::getMessage('rover_params__php_version_error');

        if (empty($errors))
            ModuleManager::registerModule($this->MODULE_ID);

	    $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_params__install_title"),
            $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/message.php"));
    }

	/**
	 * ”даление файлов и зависимостей. —н€тие модул€ с регистрации
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	private function ProcessUninstall()
	{
	    ModuleManager::unRegisterModule($this->MODULE_ID);

        global $APPLICATION;
        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_params__uninstall_title"),
            $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/unMessage.php"));
	}
}
