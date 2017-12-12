<?php
use Bitrix\Main\Localization\Loc;

global $APPLICATION, $paramsErrors;

if (!$paramsErrors)
{
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_UNINST_OK"));
}
else
{
    $details = implode("<br/>", $paramsErrors);
    echo CAdminMessage::ShowMessage(
        Array(
            "TYPE"=>"ERROR",
            "MESSAGE" =>Loc::getMessage("MOD_UNINST_ERR"),
            "DETAILS"=>$details,
            "HTML"=>true)
    );
}

?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="submit" name="" value="<?echo Loc::getMessage("MOD_BACK")?>">
<form>