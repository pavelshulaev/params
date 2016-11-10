<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::LoadMessages(__FILE__);

class rover_params extends CModule
{
    public $MODULE_ID	= "rover.params";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;

    protected $errors = [];
	
    function __construct()
    {
		$arModuleVersion = [];

        require(__DIR__ . "/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION		= $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE	= $arModuleVersion["VERSION_DATE"];
			$this->MODULE_NAME			= Loc::getMessage('rover_params__name');
			$this->MODULE_DESCRIPTION	= Loc::getMessage('rover_params__descr');
	        $this->PARTNER_NAME         = Loc::getMessage('rover_params__partner_name');
	        $this->PARTNER_URI          = Loc::getMessage('rover_params__partner_uri');
        } else {
            $this->errors[] = Loc::getMessage('rover_params__version_info_error');
		}
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
            "reference_id" => ["D", "R", "W"],
            "reference" => [
                Loc::getMessage('rover_params__reference_deny'),
                Loc::getMessage('rover_params__reference_read'),
                Loc::getMessage('rover_params__reference_write')
            ]
        );
    }

	/**
	 * Инсталляция файлов и зависимотей, регистрация модуля
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	private function ProcessInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        global $APPLICATION;
	    $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_params__install_title"), $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/message.php"));
    }

	/**
	 * Удаление файлов и зависимостей. Снятие модуля с регистрации
	 * @author Shulaev (pavel.shulaev@gmail.com)
	 */
	private function ProcessUninstall()
	{
	    ModuleManager::unRegisterModule($this->MODULE_ID);

        global $APPLICATION;
        $APPLICATION->IncludeAdminFile(Loc::getMessage("rover_params__uninstall_title"), $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/". $this->MODULE_ID ."/install/unMessage.php"));
	}
}
