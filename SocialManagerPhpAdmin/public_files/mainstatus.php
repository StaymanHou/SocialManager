<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/MainConf.php';
include '../model/Account.php';
include '../model/Module.php';
include '../model/RssPost.php';
include '../model/Queue.php';

$GLOBALS['TEMPLATE']['title'] = 'Main Status';
$GLOBALS['TEMPLATE']['curnav'] = 'MainStatus';
$GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-msts.php';

$GLOBALS['TEMPLATE']['Content']['MainConf'] = MainConf::get();

$GLOBALS['TEMPLATE']['Content']['ActAccNum'] = Account::getactivenum();
$GLOBALS['TEMPLATE']['Content']['TotAccNum'] = Account::gettotalnum();

$GLOBALS['TEMPLATE']['Content']['ModuleList'] = Module::getlist();

$GLOBALS['TEMPLATE']['Content']['RssTotalNum'] = RssPost::gettotalnum();
$GLOBALS['TEMPLATE']['Content']['RssNum'] = array();
$acclst = Account::getlist();
foreach ($acclst as $acc) {
    $key = $acc['NAME'];
    $value = $acc['PK'];
    $GLOBALS['TEMPLATE']['Content']['RssNum'][$key] = RssPost::gettotalnum($value);
}

$GLOBALS['TEMPLATE']['Content']['QueueTotalNum'] = Queue::gettotalnum();
$GLOBALS['TEMPLATE']['Content']['QueueNum'] = array();
$acclst = Account::getlist();
$modlst = $GLOBALS['TEMPLATE']['Content']['ModuleList'];
foreach ($acclst as $acc) {
    $acckey = $acc['NAME'];
    $accvalue = $acc['PK'];
    $GLOBALS['TEMPLATE']['Content']['QueueNum'][$acckey] = array();
    foreach ($modlst as $mod)
    {
        $modkey = $mod['NAME'];
        $modvalue = $mod['PK'];
        $GLOBALS['TEMPLATE']['Content']['QueueNum'][$acckey][$modkey] = Queue::getpendingnum($accvalue,$modvalue);        
    }
}

include '../view/template-page.php';
?>
