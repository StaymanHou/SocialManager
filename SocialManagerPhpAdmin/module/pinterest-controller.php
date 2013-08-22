<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/RssPost.php';
include '../model/Queue.php';
include '../model/Account.php';
include '../model/AccSetting.php';
include '../model/Tags.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;
$action = (isset($_GET['action'])) ? $_GET['action'] : null;

if ($method=='pin'&&$action=='Pin(1-click)'&&isset($_GET['submitted'])&&isset($_GET['RSSPK'])&&isset($_GET['MODPK']))
{
    $rsspost = RssPost::getByPK($_GET['RSSPK']);
    $accset = AccSetting::getByAccMod($rsspost->ACCOUNT, $_GET['MODPK']);
    $tempothersetting = $accset->OTHER_SETTING;
    $tempjson = json_decode($tempothersetting);
    $boardname = $tempjson->{'board_name'};
    $temptag = $rsspost->TAG;
    if (!empty($temptag)) {
        $temptag = explode(",", $temptag);
        $temptag = Tags::getmaptaglist($temptag);
        if (!empty($temptag)) {$boardname = $temptag[0];}
    }
    $queue = new Queue();
    
    $queue->STATUS = 1;
    $queue->ACCOUNT = $rsspost->ACCOUNT;
    $queue->MODULE = $_GET['MODPK'];
    $queue->TYPE = 1;
    $queue->TITLE = $rsspost->TITLE;
    $queue->CONTENT = $rsspost->DESCRIPTION;
    $queue->TAG = $rsspost->TAG;
    $queue->IMAGE_FILE = $rsspost->IMAGE_FILE;
    $queue->LINK = $rsspost->LINK;
    $queue->RSS_SOURCE_PK = $rsspost->PK;
    $queue->OTHER_FIELD = '{"image_link":"' . $rsspost->IMAGE_LINK . '","board_name":"' . $boardname . '"}';
    $queue->save();
    echo 'Pin(1-click) succeeded!';
}
else if ($method=='pin'&&$action=='Manual Pin'&&isset($_GET['submitted'])&&isset($_GET['RSSPK'])&&isset($_GET['MODPK']))
{
    $GLOBALS['TEMPLATE']['Content']['MODULE'] = $_GET['MODPK'];
    
    $acclst = Account::getlist();
    $GLOBALS['TEMPLATE']['Content']['AccountListPtN'] = array();
    foreach ($acclst as $acc)
    {
        $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$acc['PK']] = $acc['NAME'];
    }

    $rsspost = RssPost::getByPK($_GET['RSSPK']);
    $accset = AccSetting::getByAccMod($rsspost->ACCOUNT, $_GET['MODPK']);

    include 'template-manualpin.php';
}
else if ($method=='save'&&isset($_POST['submitted']))
{
    $queue = new Queue();
    
    $queue->STATUS = 1;
    $queue->ACCOUNT = $_POST['ACCOUNT'];
    $queue->MODULE = $_POST['MODULE'];
    $queue->TYPE = $_POST['TYPE'];
    $queue->TITLE = $_POST['TITLE'];
    $queue->CONTENT = $_POST['CONTENT'];
    $queue->TAG = $_POST['TAG'];
    $queue->IMAGE_FILE = $_POST['IMAGE_FILE'];
    $queue->LINK = $_POST['LINK'];
    $queue->RSS_SOURCE_PK = $_POST['RSS_SOURCE_PK'];
    $queue->OTHER_FIELD = $_POST['OTHER_FIELD'];
    
    $queue->save();
    echo 'Pin succeeded!';
}
else
{
   header('HTTP/1.0 403 Forbidden');
}
?>
