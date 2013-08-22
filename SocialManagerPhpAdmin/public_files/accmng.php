<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/Account.php';
include '../model/AccSetting.php';
include '../model/Module.php';
include '../model/AutoMode.php';

$GLOBALS['TEMPLATE']['title'] = 'Account Management';
$GLOBALS['TEMPLATE']['curnav'] = 'AccMng';
$GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-accmng.php';

$acclst = $GLOBALS['TEMPLATE']['Content']['AccountList'] = Account::getlist();

$GLOBALS['TEMPLATE']['Content']['AccSetting'] = array();
foreach ($acclst as $acc)
{
    $key = $acc['NAME'];
    $value = $acc['PK'];
    $GLOBALS['TEMPLATE']['Content']['AccSetting'][$key] = AccSetting::getlist($value);
}

$modlst = Module::getlist();
$GLOBALS['TEMPLATE']['Content']['ModuleList'] = array();
foreach ($modlst as $mod)
{
    $GLOBALS['TEMPLATE']['Content']['ModuleList'][$mod['PK']] = $mod['NAME'];
}

$GLOBALS['TEMPLATE']['Content']['AutoMode'] = array();
foreach ($modlst as $mod)
{
    $key = $mod['NAME'];
    $value = $mod['PK'];
    $amlst = AutoMode::getlist($value);
    $GLOBALS['TEMPLATE']['Content']['AutoMode'][$value] = array();
    foreach ($amlst as $am)
    {
        $GLOBALS['TEMPLATE']['Content']['AutoMode'][$value][$am['PK']] = $am['TITLE'];
    }
}

include '../view/template-page.php';
?>
