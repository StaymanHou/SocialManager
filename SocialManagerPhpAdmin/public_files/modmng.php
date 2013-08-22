<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/Module.php';
include '../model/AutoMode.php';

$GLOBALS['TEMPLATE']['title'] = 'Module Management';
$GLOBALS['TEMPLATE']['curnav'] = 'ModMng';
$GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-modmng.php';

$modlst = $GLOBALS['TEMPLATE']['Content']['ModuleList'] = Module::getlist();

$GLOBALS['TEMPLATE']['Content']['AutoMode'] = array();
foreach ($modlst as $mod)
{
    $key = $mod['NAME'];
    $value = $mod['PK'];
    $GLOBALS['TEMPLATE']['Content']['AutoMode'][$key] = AutoMode::getlist($value);
}

include '../view/template-page.php';
?>
