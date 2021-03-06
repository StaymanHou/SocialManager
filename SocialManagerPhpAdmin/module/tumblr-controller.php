<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/RssPost.php';
include '../model/Queue.php';
include '../model/Account.php';
include '../model/AccSetting.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;
$action = (isset($_GET['action'])) ? $_GET['action'] : null;

if ($method=='tblog'&&$action=='Link Tblog(1-click)'&&isset($_GET['submitted'])&&isset($_GET['RSSPK'])&&isset($_GET['MODPK']))
{
    $rsspost = RssPost::getByPK($_GET['RSSPK']);
    $accset = AccSetting::getByAccMod($rsspost->ACCOUNT, $_GET['MODPK']);
    $tempothersetting = $accset->OTHER_SETTING;
    $tempjson = json_decode($tempothersetting);
    $blogname = $tempjson->{'blog_name'};
    $link_anchor_text = $tempjson->{'link_anchor_text'};
    $tempextracontent = $accset->EXTRA_CONTENT;
    $queue = new Queue();
    
    $queue->STATUS = 1;
    $queue->ACCOUNT = $rsspost->ACCOUNT;
    $queue->MODULE = $_GET['MODPK'];
    $queue->TYPE = 1;
    $queue->TITLE = $rsspost->TITLE;
    $queue->CONTENT = $rsspost->DESCRIPTION;
    $queue->EXTRA_CONTENT = $tempextracontent;
    $queue->TAG = $rsspost->TAG;
    $queue->IMAGE_FILE = null;
    $queue->LINK = $rsspost->LINK;
    $queue->OTHER_FIELD = '{"image_link": "';
    $stil = $rsspost->IMAGE_LINK; // have to assign here for check whether it's empty or not
    if (!empty($stil)) {$queue->OTHER_FIELD .= $rsspost->IMAGE_LINK;}
    $queue->OTHER_FIELD .= '","blog_name":"' . $blogname . '",';
    $queue->OTHER_FIELD .= '"link_anchor_text":"' . $link_anchor_text . '"}';
    $queue->RSS_SOURCE_PK = $rsspost->PK;
    
    $queue->save();
    echo 'Link Tblog(1-click) succeeded!';
}
else if ($method=='tblog'&&$action=='Image Tblog(1-click)'&&isset($_GET['submitted'])&&isset($_GET['RSSPK'])&&isset($_GET['MODPK']))
{
    $rsspost = RssPost::getByPK($_GET['RSSPK']);
    $accset = AccSetting::getByAccMod($rsspost->ACCOUNT, $_GET['MODPK']);
    $tempothersetting = $accset->OTHER_SETTING;
    $tempjson = json_decode($tempothersetting);
    $blogname = $tempjson->{'blog_name'};
    $link_anchor_text = $tempjson->{'link_anchor_text'};
    $tempextracontent = $accset->EXTRA_CONTENT;
    $queue = new Queue();
    
    $queue->STATUS = 1;
    $queue->ACCOUNT = $rsspost->ACCOUNT;
    $queue->MODULE = $_GET['MODPK'];
    $queue->TYPE = 2;
    $tempimagefile = $rsspost->IMAGE_FILE;
    if (empty($tempimagefile)) {$queue->TYPE = 1;}
    $queue->TITLE = $rsspost->TITLE;
    $queue->CONTENT = $rsspost->DESCRIPTION;
    $queue->EXTRA_CONTENT = $tempextracontent;
    $queue->TAG = $rsspost->TAG;
    $queue->IMAGE_FILE = $rsspost->IMAGE_FILE;
    $queue->LINK = $rsspost->LINK;
    $queue->OTHER_FIELD = '{"image_link": "';
    $stil = $rsspost->IMAGE_LINK; // have to assign here for check whether it's empty or not
    if (!empty($stil)) {$queue->OTHER_FIELD .= $rsspost->IMAGE_LINK;}
    $queue->OTHER_FIELD .= '","blog_name":"' . $blogname . '",';
    $queue->OTHER_FIELD .= '"link_anchor_text":"' . $link_anchor_text . '"}';
    $queue->RSS_SOURCE_PK = $rsspost->PK;
    
    $queue->save();
    if ($queue->TYPE==1) {
        echo 'Can\'t find image! The tblog is automatically converted to a Link Tblog!';
    } else if ($queue->TYPE==2) {
        echo 'Image Tblog(1-click) succeeded!';
    }
}
else if ($method=='tblog'&&$action=='Manual Tblog'&&isset($_GET['submitted'])&&isset($_GET['RSSPK'])&&isset($_GET['MODPK']))
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

    include 'template-manualtblog.php';
}
else if ($method=='save'&&isset($_POST['submitted']))
{
    $queue = new Queue();
    
    $queue->STATUS = 1;
    $queue->ACCOUNT = $_POST['ACCOUNT'];
    $queue->MODULE = $_POST['MODULE'];
    $queue->TYPE = $_POST['TYPE'];
    if (empty($_POST['IMAGE_FILE'])) {$queue->TYPE = 1;}
    $queue->TITLE = $_POST['TITLE'];
    $queue->CONTENT = $_POST['CONTENT'];
    $queue->EXTRA_CONTENT = $_POST['EXTRA_CONTENT'];
    $queue->TAG = $_POST['TAG'];
    $queue->IMAGE_FILE = $_POST['IMAGE_FILE'];
    $queue->LINK = $_POST['LINK'];
    $queue->OTHER_FIELD = $_POST['OTHER_FIELD'];
    $queue->RSS_SOURCE_PK = $_POST['RSS_SOURCE_PK'];
    
    $queue->save();
    if ($queue->TYPE==1) {
        echo 'Link Tblog succeeded!';
    } else if ($queue->TYPE==2) {
        echo 'Image Tblog succeeded!';
    }
}
else
{
   header('HTTP/1.0 403 Forbidden');
}
?>
