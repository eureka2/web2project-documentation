<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly');
}

require_once (W2P_BASE_DIR . '/modules/documentation/documentation.class.php');
$suba = defVal(w2PgetParam($_REQUEST, 'suba', null), '');
switch ($suba) {
    case 'addedit':
        include (W2P_BASE_DIR . '/modules/documentation/addedit.php');
        break;
    case 'viewsource':
        include (W2P_BASE_DIR . '/modules/documentation/viewsource.php');
        break;
    default:
        include (W2P_BASE_DIR . '/modules/documentation/index.php');
}
