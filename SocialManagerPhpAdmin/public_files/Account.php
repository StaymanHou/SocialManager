<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/Account.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;

if ($method=='toggleactive'&&isset($_GET['pk']))
{
    Account::toggleactive($_GET['pk']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else if ($method=='delete'&&isset($_GET['pk']))
{
    Account::setdelete($_GET['pk']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else if ($method=='create')
{
    $GLOBALS['TEMPLATE']['title'] = 'Create New Account';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-Account.php';

    $acc = new Account();

    include '../view/template-page.php';
}
else if ($method=='get'&&isset($_GET['pk']))
{
    $GLOBALS['TEMPLATE']['title'] = 'Modify Account Setting';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-Account.php';

    $acc = Account::getByPK($_GET['pk']);

    include '../view/template-page.php';
}
else if ($method=='save'&&isset($_POST['submitted']))
{
    if($_POST['PK']==null)
    {
        $acc = new Account();
        $acc->NAME = $_POST['NAME'];
        $acc->RSS_URL = $_POST['RSS_URL'];
        $acc->TAG_LIMIT = $_POST['TAG_LIMIT'];
        $acc->ACTIVE = $_POST['ACTIVE'];
        $acc->save();
        header('Location: accmng.php');
    } else {
        $acc = Account::getByPK($_POST['PK']);
        $acc->NAME = $_POST['NAME'];
        $acc->RSS_URL = $_POST['RSS_URL'];
        $acc->TAG_LIMIT = $_POST['TAG_LIMIT'];
        $acc->ACTIVE = $_POST['ACTIVE'];
        $acc->save();
        header('Location: accmng.php');
    }
}
else
{
   header('HTTP/1.0 403 Forbidden');
}

?>
