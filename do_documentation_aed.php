<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$delete = (int) w2PgetParam($_POST, 'del', 0);
$redirect = w2PgetParam($_POST, 'redirect', "m=documentation");
$wikipage_id = (int)(w2PgetParam($_POST, 'wikipage_id', 0));
$obj = new CDocumentation();
$controller = 
    new w2p_Controllers_Base(
        $obj,
        $delete, 
        'Documentation', 
        $redirect, 
        'm=documentation&a=addedit&wikipage_id='.$wikipage_id
    );

$AppUI = $controller->process($AppUI, $_POST);
if ((!$delete || $obj->wikipage_namespace == 'Category') &&
    !is_array($controller->success) && $controller->success) {
    $AppUI->redirect($controller->resultPath.'&wikipage_id='.$obj->wikipage_id);
}
else {
    $AppUI->redirect($controller->resultPath);
}
