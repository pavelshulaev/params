<?php
use Bitrix\Main\Localization\Loc;

global $APPLICATION, $paramsErrors;

if (empty($paramsErrors))
    echo \CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
else
    echo \CAdminMessage::ShowMessage(
      array(
           "TYPE"       => "ERROR",
           "MESSAGE"    => Loc::getMessage("MOD_INST_ERR"),
           "DETAILS"    => implode("<br/>", $paramsErrors),
           "HTML"       => true
      ));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=Loc::getMessage("MOD_BACK")?>">
<form>