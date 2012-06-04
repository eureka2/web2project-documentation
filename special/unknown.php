<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

require_once (W2P_BASE_DIR . '/modules/documentation/documentation.class.php');

global $AppUI;
$projectpage = $AppUI->getState("projectpage");
if (!is_null($projectpage)) {
    list($namespace, $title) = explode(":", $projectpage);
}
$ttl = 'Documentation';
$titleBlock = new CTitleBlock($ttl, 'documentation.png', 'documentation', 'documentation' . '.' . $a);
$titleBlock->addCell();
$titleBlock->show();
if (function_exists('styleRenderBoxTop') && !isset($_GET['tab'])) {
    echo styleRenderBoxTop();
}

?>
<style type="text/css">
@import "modules/documentation/css/documentation.css";
</style>
<table class="std" style="width:100%">
    <tr>
        <th style="text-align: center;">
            <strong><?php echo $AppUI->_('No such special page'); ?></strong>
        </th>
    </tr>
    <tr>
        <td style="background: white">
            <table border="0" style="width:100%">
                <tr>
                    <td>
                        <?php echo sprintf($AppUI->_('A list of valid special pages can be found at <a href="%s" title="%s">%s</a>.', UI_OUTPUT_RAW), "index.php?m=documentation&page=Special:Specialpages", "Special:Specialpages", "Special:Specialpages"); ?>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <?php if (isset($namespace)) { ?>
                <tr>
                    <td>
                        <?php echo sprintf($AppUI->_('Return to <a href="%s" title="%s">%s</a>.', UI_OUTPUT_RAW), "index.php?m=documentation&page=$namespace", $title, $title); ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
</table>
 