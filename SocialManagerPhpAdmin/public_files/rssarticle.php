<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/RssPost.php';
include '../model/Account.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;

if ($method=='get'&&isset($_GET['pk']))
{
    $acclst = Account::getlist();
    $GLOBALS['TEMPLATE']['Content']['AccountListPtN'] = array();
    foreach ($acclst as $acc)
    {
        $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$acc['PK']] = $acc['NAME'];
    }

    $rsspost = RssPost::getByPK($_GET['pk']);

    include '../view/template-rssarticle.php';
}
else if ($method=='create')
{
    $acclst = Account::getlist();
    $GLOBALS['TEMPLATE']['Content']['AccountListPtN'] = array();
    foreach ($acclst as $acc)
    {
        $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$acc['PK']] = $acc['NAME'];
    }

    $rsspost = new RssPost();

    include '../view/template-rssarticle.php';
}
else if ($method=='save'&&isset($_POST['submitted']))
{
    if ($_POST['PK']==null)
    {
        $rsspost = new RssPost();
        $rsspost->ACCOUNT = $_POST['ACCOUNT'];
        $rsspost->TITLE = $_POST['TITLE'];
        $rsspost->DESCRIPTION = $_POST['DESCRIPTION'];
        $rsspost->CONTENT = $_POST['CONTENT'];
        $rsspost->TAG = $_POST['TAG'];
        $rsspost->IMAGE_FILE = $_POST['IMAGE_FILE'];
        $rsspost->IMAGE_LINK = $_POST['IMAGE_LINK'];
        $rsspost->LINK = $_POST['LINK'];
        $rsspost->OTHER_FIELD = $_POST['OTHER_FIELD'];
        $rsspost->SOCIAL_SCORE = $_POST['SOCIAL_SCORE'];
        $rsspost->CREATE_TIME = $_POST['CREATE_TIME'];
        $rsspost->save();
        header('Location: rsspool.php');
    } else {
        $rsspost = RssPost::getByPK($_POST['PK']);
        $rsspost->TITLE = $_POST['TITLE'];
        $rsspost->DESCRIPTION = $_POST['DESCRIPTION'];
        $rsspost->CONTENT = $_POST['CONTENT'];
        $rsspost->TAG = $_POST['TAG'];
        $rsspost->IMAGE_FILE = $_POST['IMAGE_FILE'];
        $rsspost->IMAGE_LINK = $_POST['IMAGE_LINK'];
        $rsspost->LINK = $_POST['LINK'];
        $rsspost->OTHER_FIELD = $_POST['OTHER_FIELD'];
        $rsspost->SOCIAL_SCORE = $_POST['SOCIAL_SCORE'];
        $rsspost->save();
        echo 'rss is modified successfully!';
    }
}
else
{
   header('HTTP/1.0 403 Forbidden');
}
?>
