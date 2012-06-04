<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once ("config.php");

$wikipage_id = (int) w2PgetParam($_GET, 'wikipage_id', 0);
$page = w2PgetParam($_GET, 'page', '');
if (!$page) {
    $AppUI->redirect('m=public&a=access_denied');
}

// check permissions for this record
$canAuthor = canAdd('documentation');
$canEdit = $wikipage_id > 0 && canEdit('documentation', $wikipage_id);
$canDelete = $wikipage_id > 0 && canDelete('documentation', $wikipage_id);
if (!$canAuthor && !$wikipage_id) {
    $AppUI->redirect('m=public&a=access_denied');
}
if (!$canEdit && $wikipage_id > 0) {
    $AppUI->redirect('m=public&a=access_denied');
}

$q = new w2p_Database_Query;
if ($wikipage_id > 0) {
    // pull the page
    $q->addTable('wikipages');
    $q->addQuery('*');
    $q->addWhere('wikipage_id =' . $wikipage_id);
    $wikipage = $q->loadHash();
    $q->clear();
} else {
    list($wikipage_namespace, $page) = explode(":", $page);
    $wikipage = array(
        'wikipage_parser' => $WIKI_CONFIG['default_parser'],
        'wikipage_lang' => $WIKI_CONFIG['default_lang'],
        'wikipage_start' => 0,
        'wikipage_namespace' => $wikipage_namespace,
        'wikipage_name' => $page,
        'wikipage_title' => str_replace("_", " ", $page),
        'wikipage_content' => "",
    );
}
include_once (W2P_BASE_DIR."/modules/documentation/views/addedit.form.php");