<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/AccSetting.php';
include '../model/Account.php';
include '../model/Module.php';
include '../model/AutoMode.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;

if ($method=='toggleactive'&&isset($_GET['pk']))
{
    AccSetting::toggleactive($_GET['pk']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else if ($method=='get'&&isset($_GET['pk']))
{
    $GLOBALS['TEMPLATE']['title'] = 'Modify Account Setting';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-AccSetting.php';
    
    $acclst = $GLOBALS['TEMPLATE']['Content']['AccountList'] = Account::getlist();
    $GLOBALS['TEMPLATE']['Content']['AccountList'] = array();
    foreach ($acclst as $acc)
    {
        $GLOBALS['TEMPLATE']['Content']['AccountList'][$acc['PK']] = $acc['NAME'];
    }
    
    $modlst = Module::getlist();
    $GLOBALS['TEMPLATE']['Content']['ModuleList'] = array();
    foreach ($modlst as $mod)
    {
        $GLOBALS['TEMPLATE']['Content']['ModuleList'][$mod['PK']] = $mod['NAME'];
    }

    $accset = AccSetting::getByPK($_GET['pk']);
    $accset->PSWD = 'N/A';

    $GLOBALS['TEMPLATE']['Content']['AMList'] = AutoMode::getlist($accset->MODULE);

    include '../view/template-page.php';
}
else if ($method=='save'&&isset($_POST['submitted']))
{
    $accset = AccSetting::getByPK($_POST['PK']);
    if($_POST['PSWD']=='N/A')
    {
        $accset->USERNAME = $_POST['USERNAME'];
        $accset->OTHER_SETTING = $_POST['OTHER_SETTING'];
        $accset->EXTRA_CONTENT = $_POST['EXTRA_CONTENT'];
        $accset->ACTIVE = $_POST['ACTIVE'];
        $accset->AUTO_MODE = $_POST['AUTO_MODE'];
        $accset->TIME_START = $_POST['TIME_START'];
        $accset->TIME_END = $_POST['TIME_END'];
        $accset->NUM_PER_DAY = $_POST['NUM_PER_DAY'];
        $accset->MIN_POST_INTERVAL = $_POST['MIN_POST_INTERVAL'];
        $accset->QUEUE_SIZE = $_POST['QUEUE_SIZE'];
        $accset->save(false);
    } else {
        $accset->USERNAME = $_POST['USERNAME'];
        $accset->OTHER_SETTING = $_POST['OTHER_SETTING'];
        $accset->EXTRA_CONTENT = $_POST['EXTRA_CONTENT'];
        $accset->PSWD = $_POST['PSWD'];
        $accset->ACTIVE = $_POST['ACTIVE'];
        $accset->AUTO_MODE = $_POST['AUTO_MODE'];
        $accset->TIME_START = $_POST['TIME_START'];
        $accset->TIME_END = $_POST['TIME_END'];
        $accset->NUM_PER_DAY = $_POST['NUM_PER_DAY'];
        $accset->MIN_POST_INTERVAL = $_POST['MIN_POST_INTERVAL'];
        $accset->QUEUE_SIZE = $_POST['QUEUE_SIZE'];
        $accset->save(true);
    }
    header('Location: accmng.php');
}
else
{
   header('HTTP/1.0 403 Forbidden');
}

?>
