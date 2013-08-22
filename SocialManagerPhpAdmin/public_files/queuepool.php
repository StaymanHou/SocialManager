<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/Account.php';
include '../model/AccSetting.php';
include '../model/Module.php';
include '../model/Queue.php';

$GLOBALS['TEMPLATE']['exheader'] = '<link type="text/css" href="css/basic.css" rel="stylesheet" media="screen" />';

$GLOBALS['TEMPLATE']['jsheader'] = '<script type="text/javascript" src="js/jquery.js"></script><script type="text/javascript" src="js/jquery.simplemodal.js"></script><script type="text/javascript" src="js/queuepool.js"></script>';

$GLOBALS['TEMPLATE']['title'] = 'Social Post Queue';
$GLOBALS['TEMPLATE']['curnav'] = 'Queue';
$GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-queuepool.php';

$GLOBALS['TEMPLATE']['curacc'] = $account = (isset($_GET['account'])&&$_GET['account']!='null') ? $_GET['account'] : null;
$GLOBALS['TEMPLATE']['curnum'] = $queuenum = (isset($_GET['queuenum'])) ? $_GET['queuenum'] : 30;
$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
if (isset($_GET['action'])&&$_GET['action']==">") {$offset += $queuenum;}
if (isset($_GET['action'])&&$_GET['action']=="<") {$offset -= $queuenum;}
if ($offset<0) {$offset=0;}
$GLOBALS['TEMPLATE']['curoffset'] = $offset;

$acclst = $GLOBALS['TEMPLATE']['Content']['AccountList'] = Account::getlist();
$GLOBALS['TEMPLATE']['Content']['AccountListPtN'] = array();
$GLOBALS['TEMPLATE']['Content']['AccSetting'] = array();
foreach ($acclst as $acc)
{
    $key = $acc['NAME'];
    $value = $acc['PK'];
    $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$value] = $key;
    $GLOBALS['TEMPLATE']['Content']['AccSetting'][$key] = AccSetting::getlist($value);
}

$modlst = Module::getlist();
$GLOBALS['TEMPLATE']['Content']['ModuleListPtN'] = array();
$GLOBALS['TEMPLATE']['Content']['QueueList'] = array();
foreach ($modlst as $mod)
{
    $GLOBALS['TEMPLATE']['Content']['ModuleListPtN'][$mod['PK']] = $mod['NAME'];
    $GLOBALS['TEMPLATE']['Content']['QueueList'][$mod['PK']] = Queue::getlist($account, $mod['PK'], $queuenum, $offset);
}

$GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'] = array();
$GLOBALS['TEMPLATE']['Content']['QueueSizeTable'] = array();
foreach ($modlst as $mod)
{
    $GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'][$mod['PK']] = array();
    $GLOBALS['TEMPLATE']['Content']['QueueSizeTable'][$mod['PK']] = array();
    foreach ($acclst as $acc)
    {
        $GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'][$mod['PK']][$acc['PK']] = Queue::getpendingnum($acc['PK'], $mod['PK']);
        $GLOBALS['TEMPLATE']['Content']['QueueSizeTable'][$mod['PK']][$acc['PK']] = AccSetting::getsize($acc['PK'], $mod['PK']);
    }
}

include '../view/template-page.php';
?>
