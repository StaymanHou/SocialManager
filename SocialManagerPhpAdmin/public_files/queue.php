<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/Queue.php';
include '../model/Account.php';
include '../model/Module.php';
include '../model/Status.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;

if ($method=='get'&&isset($_GET['pk']))
{
    $GLOBALS['TEMPLATE']['title'] = 'Modify Queue Item';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-queue.php';

    $acclst = Account::getlist();
    $GLOBALS['TEMPLATE']['Content']['AccountListPtN'] = array();
    foreach ($acclst as $acc)
    {
        $GLOBALS['TEMPLATE']['Content']['AccountListPtN'][$acc['PK']] = $acc['NAME'];
    }
    
    $modlst = Module::getlist();
    $GLOBALS['TEMPLATE']['Content']['ModuleListPtN'] = array();
    foreach ($modlst as $mod)
    {
        $GLOBALS['TEMPLATE']['Content']['ModuleListPtN'][$mod['PK']] = $mod['NAME'];
    }
    
    $sttlst = Status::getlist();
    $GLOBALS['TEMPLATE']['Content']['StatusListPtN'] = array();
    foreach ($sttlst as $stt)
    {
        $GLOBALS['TEMPLATE']['Content']['StatusListPtN'][$stt['PK']] = $stt['TITLE'];
    }

    $qi = Queue::getByPK($_GET['pk']);
    
    $redirect = $_SERVER['HTTP_REFERER'];

    include '../view/template-page.php';
}
else if ($method=='reset'&&isset($_GET['pk']))
{
    $qi = Queue::getByPK($_GET['pk']);
    $qi->STATUS = 1;
    $qi->SCHEDULE_TIME = '0000-00-00 00:00:00';
    $qi->save();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else if ($method=='save'&&isset($_POST['submitted'])&&isset($_POST['PK']))
{
    $qi = Queue::getByPK($_POST['PK']);
    $qi->STATUS = $_POST['STATUS'];
    $qi->ACCOUNT = $_POST['ACCOUNT'];
    $qi->MODULE = $_POST['MODULE'];
    $qi->TYPE = $_POST['TYPE'];
    $qi->TITLE = $_POST['TITLE'];
    $qi->CONTENT = $_POST['CONTENT'];
    $qi->EXTRA_CONTENT = $_POST['EXTRA_CONTENT'];
    $qi->TAG = $_POST['TAG'];
    $qi->IMAGE_FILE = $_POST['IMAGE_FILE'];
    $qi->LINK = $_POST['LINK'];
    $qi->OTHER_FIELD = $_POST['OTHER_FIELD'];
    $qi->SCHEDULE_TIME = $_POST['SCHEDULE_TIME'];
    $qi->save();
    header('Location: ' . $_POST['redirect']);
}
else if ($method=='delete'&&isset($_GET['pk']))
{
    $qi = Queue::getByPK($_GET['pk']);
    $qi->delete();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else if ($method=='deletelist'&&isset($_POST['pk_list']))
{
    Queue::deletelist($_POST['pk_list']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else
{
   header('HTTP/1.0 403 Forbidden');
}
?>
