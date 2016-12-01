<?php
use Bitrix\Main\Localization\Loc;

global $APPLICATION, $errors;

if (!$errors)
{
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_UNINST_OK"));
}
else
{
    $details = implode("<br/>", $errors);
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