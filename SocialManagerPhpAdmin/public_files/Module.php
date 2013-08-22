<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/Module.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;

if ($method=='delete'&&isset($_GET['pk']))
{
    Module::delete($_GET['pk']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else if ($method=='create')
{
    $GLOBALS['TEMPLATE']['title'] = 'Create New Module';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-Module.php';

    $mod = new Module();

    include '../view/template-page.php';
}
else if ($method=='get'&&isset($_GET['pk']))
{
    $GLOBALS['TEMPLATE']['title'] = 'Modify Module';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-Module.php';

    $mod = Module::getByPK($_GET['pk']);

    include '../view/template-page.php';
}
else if ($method=='save'&&isset($_POST['submitted']))
{
    if($_POST['PK']==null)
    {
        $mod = new Module();
        $mod->NAME = $_POST['NAME'];
        $mod->save();
        header('Location: modmng.php');
    } else {
        $mod = Module::getByPK($_POST['PK']);
        $mod->NAME = $_POST['NAME'];
        $mod->save();
        header('Location: modmng.php');
    }
}
else
{
   header('HTTP/1.0 403 Forbidden');
}

?>
