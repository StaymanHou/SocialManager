<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/MainConf.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;

if ($method=='get')
{
    $GLOBALS['TEMPLATE']['title'] = 'Modify Main Config';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-MainConf.php';

    $GLOBALS['TEMPLATE']['Content']['MainConf'] = MainConf::get();

    include '../view/template-page.php';
}
else if ($method=='save'&&isset($_POST['submitted']))
{
    $mc = MainConf::get();
    $mc->TITLE = $_POST['TITLE'];
    $mc->CACHING_TIME = $_POST['CACHING_TIME'];
    $mc->IMAGE_FILE_DIR = $_POST['IMAGE_FILE_DIR'];
    $mc->LOAD_ITERATION = $_POST['LOAD_ITERATION'];
    $mc->PULLER_ITERATION = $_POST['PULLER_ITERATION'];
    $mc->POSTER_ITERATION = $_POST['POSTER_ITERATION'];
    $mc->save();
    header('Location: mainstatus.php');
}
else
{
   header('HTTP/1.0 403 Forbidden');
}

?>
