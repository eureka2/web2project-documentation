<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

require_once (W2P_BASE_DIR . '/modules/documentation/documentation.class.php');

$projectpage = $AppUI->getState("projectpage");
if (!is_null($projectpage)) {
    list($namespace, $title) = explode(":", $projectpage);
}

global $AppUI;
$wiki = new CWiki();
$wikipages = $wiki->loadIndex($namespace);

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
            <strong><?php echo $AppUI->_('Pages index'); ?></strong>
        </th>
    </tr>
    <tr>
        <td style="background: white">
            <table border="0" style="width:100%">
                <?php
                $n = 0;
                foreach ($wikipages as $wikipage) {
                    if ($n++ % 3 == 0) {
                        if ($n > 1) { ?>
                </tr>
                <?php } ?>
                <tr>
                <?php } ?>
                    <td>
                        <a href="?m=documentation&page=<?php echo $wikipage->wikipage_namespace.":".$wikipage->wikipage_name ?>" title="<?php echo $wikipage->wikipage_title ?>">
                            <?php echo $wikipage->wikipage_title ?>
                        </a>
                    </td>
                <?php }
                while ($n++ % 3 != 0) { ?>
                    <td>&nbsp;</td>
                <?php } ?>
                </tr>
            </table>
        </td>
    </tr>
</table>
 