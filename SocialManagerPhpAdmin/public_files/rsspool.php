<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/Account.php';
include '../model/AccSetting.php';
include '../model/Module.php';
include '../model/RssPost.php';
include '../model/Queue.php';

$GLOBALS['TEMPLATE']['exheader'] = '<link type="text/css" href="css/basic.css" rel="stylesheet" media="screen" />';

$GLOBALS['TEMPLATE']['jsheader'] = '<script type="text/javascript" src="js/jquery.js"></script><script type="text/javascript" src="js/jquery.simplemodal.js"></script><script type="text/javascript" src="js/rssarticle.js"></script><script type="text/javascript" src="js/rsspool.js"></script>';

$GLOBALS['TEMPLATE']['title'] = 'RSS Article Pool';
$GLOBALS['TEMPLATE']['curnav'] = 'RssPool';
$GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-rsspool.php';

$GLOBALS['TEMPLATE']['curacc'] = $account = (isset($_GET['account'])&&$_GET['account']!='null') ? $_GET['account'] : null;
$GLOBALS['TEMPLATE']['curnum'] = $rssnum = (isset($_GET['rssnum'])) ? $_GET['rssnum'] : 30;
$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
if (isset($_GET['action'])&&$_GET['action']==">") {$offset += $rssnum;}
if (isset($_GET['action'])&&$_GET['action']=="<") {$offset -= $rssnum;}
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
foreach ($modlst as $mod)
{
    $GLOBALS['TEMPLATE']['Content']['ModuleListPtN'][$mod['PK']] = $mod['NAME'];
}

$GLOBALS['TEMPLATE']['Content']['RssList'] = RssPost::getlist($account, $rssnum, $offset);

$GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'] = array();
$GLOBALS['TEMPLATE']['Content']['QueueSizeTable'] = array();
$GLOBALS['TEMPLATE']['Content']['QueueRSSPKTable'] = array();
foreach ($modlst as $mod)
{
    $GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'][$mod['PK']] = array();
    $GLOBALS['TEMPLATE']['Content']['QueueSizeTable'][$mod['PK']] = array();
    $GLOBALS['TEMPLATE']['Content']['QueueRSSPKTable'][$mod['PK']] = Queue::getrsspklist(null, $mod['PK']);
    foreach ($acclst as $acc)
    {
        $GLOBALS['TEMPLATE']['Content']['QueuePendingNumTable'][$mod['PK']][$acc['PK']] = Queue::getpendingnum($acc['PK'], $mod['PK']);
        $GLOBALS['TEMPLATE']['Content']['QueueSizeTable'][$mod['PK']][$acc['PK']] = AccSetting::getsize($acc['PK'], $mod['PK']);
    }
}

include '../view/template-page.php';
?>
