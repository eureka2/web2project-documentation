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
$wiki = new CWiki();
$images = $wiki->loadIndex('Image');
?>
<style type="text/css">
@import "modules/documentation/css/documentation.css";
</style>
<table class="std" style="width:100%">
    <tr>
        <th style="text-align: center;">
            <strong><?php echo $AppUI->_('File list'); ?></strong>
        </th>
    </tr>
    <tr>
        <td style="background: white">
            <table border='1' class="imagelist" style="width:100%">
                <thead>
                    <tr>
                        <th><?php echo $AppUI->_('Date'); ?></th>
                        <th><?php echo $AppUI->_('Name'); ?></th>
                        <th><?php echo $AppUI->_('User'); ?></th>
                        <th><?php echo $AppUI->_('Size'); ?></th>
                        <th><?php echo $AppUI->_('Description'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($images as $image) {
                    $imagefile = W2P_BASE_DIR . '/modules/documentation/images/upload/'.$image->wikipage_name;;
                    $size = round(filesize($imagefile) / 1000);
                    list($width, $height, $type, $attr) = getimagesize($imagefile);
                    ?>
                    <tr>
                        <td class="TablePager_col_img_timestamp"><?php echo $image->wikipage_date; ?></td>
                        <td class="TablePager_col_img_name"><a href="index.php?m=documentation&amp;page=Image:<?php echo $image->wikipage_name; ?>" title="Image:<?php echo $image->wikipage_name; ?>"><?php echo $image->wikipage_title; ?></a></td>
                        <td class="TablePager_col_img_user_text"><?php echo $image->wikipage_user; ?></td>
                        <td class="TablePager_col_img_size"><?php echo $size." ".$AppUI->_('KB'); ?></td>
                        <td class="TablePager_col_img_description"> <span class="comment">(<?php echo $image->wikipage_content; ?>)</span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
 