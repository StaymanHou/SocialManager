<?php

include '../lib/common.php';
include '../lib/db.php';
include '../model/Module.php';
include '../model/AutoMode.php';

$method = (isset($_GET['method'])) ? $_GET['method'] : null;

if ($method=='create'&&isset($_GET['modpk']))
{
    $GLOBALS['TEMPLATE']['title'] = 'Create New AutoMode';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-AutoMode.php';

    $modlst = Module::getlist();
    $GLOBALS['TEMPLATE']['Content']['ModuleList'] = array();
    foreach ($modlst as $mod)
    {
        $GLOBALS['TEMPLATE']['Content']['ModuleList'][$mod['PK']] = $mod['NAME'];
    }
    
    $am = new AutoMode();
    $am->MODULE = $_GET['modpk'];

    include '../view/template-page.php';
}
else if ($method=='get'&&isset($_GET['pk']))
{
    $GLOBALS['TEMPLATE']['title'] = 'Modify AutoMode';
    $GLOBALS['TEMPLATE']['ContentViewFile'] = 'template-AutoMode.php';

    $modlst = Module::getlist();
    $GLOBALS['TEMPLATE']['Content']['ModuleList'] = array();
    foreach ($modlst as $mod)
    {
        $GLOBALS['TEMPLATE']['Content']['ModuleList'][$mod['PK']] = $mod['NAME'];
    }
    
    $am = AutoMode::getByPK($_GET['pk']);

    include '../view/template-page.php';
}
else if ($method=='save'&&isset($_POST['submitted']))
{
    if($_POST['PK']==null)
    {
        $am = new AutoMode();
        $am->MODULE = $_POST['MODULE'];
        $am->CODE = $_POST['CODE'];
        $am->TITLE = $_POST['TITLE'];
        $am->OTHER_SETTING = $_POST['OTHER_SETTING'];
        $am->save();
        header('Location: modmng.php');
    } else {
        $am = AutoMode::getByPK($_POST['PK']);
        $am->CODE = $_POST['CODE'];
        $am->TITLE = $_POST['TITLE'];
        $am->OTHER_SETTING = $_POST['OTHER_SETTING'];
        $am->save();
        header('Location: modmng.php');
    }
}
else if ($method=='delete'&&isset($_GET['modpk'])&&isset($_GET['pk']))
{
    AutoMode::delete($_GET['modpk'], $_GET['pk']);
    header('Location: modmng.php');
}
else
{
   header('HTTP/1.0 403 Forbidden');
}

?>
