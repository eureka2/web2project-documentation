<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

require_once (W2P_BASE_DIR . '/modules/documentation/documentation.class.php');

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
            <strong><?php echo $AppUI->_('Special pages'); ?></strong>
        </th>
    </tr>
    <tr>
        <td style="background: white">
            <table border="0" style="width:100%">
                <tr>
                    <td>
                        <h2><?php echo $AppUI->_('Special pages for all users'); ?></h2>
                        <ul>
                            <?php if (isset($namespace)) { ?>
                            <li>
                                <a href="index.php?m=documentation&page=Special:Allpages" title="Special:Allpages">
                                    <?php echo $AppUI->_('Pages index'); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <li>
                                <a href="index.php?m=documentation&page=Special:Categories" title="Special:Categories">
                                    <?php echo $AppUI->_('Categories'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?m=documentation&page=Special:Upload" title="Special:Upload">
                                    <?php echo $AppUI->_('Upload file'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?m=documentation&page=Special:ImageList" title="Special:ImageList">
                                    <?php echo $AppUI->_('File list'); ?>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="160">
            <input type="button" value="<?php echo $AppUI->_('back'); ?>" class="button" onclick="javascript:history.back(-1);" />
        </td>
    </tr>
</table>
 