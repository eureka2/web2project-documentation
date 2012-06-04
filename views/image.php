<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$ttl = 'Documentation';
$titleBlock = new CTitleBlock($ttl, 'documentation.png', 'documentation', 'documentation' . '.' . $a);
$titleBlock->addCell();
$redirect = "m=".$m;
if ($m == 'documentation') {
    if ($canEditWiki) {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('edit this page') . '">', '', '<form action="?m=documentation&amp;a=addedit&amp;page='.$wikipage_namespace.':'.$page.'&wikipage_id='.$wikipage_id.'" method="post">', '</form>');
    }
    if ($canAdd) {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('Upload file') . '">', '', '<form action="?m=documentation&amp;page=Special:Upload" method="post">', '</form>');
    }
    $redirect .= "&page=".$wikipage_namespace.":";
} else {
    $tab = $AppUI->processIntState('ProjVwTab', $_GET, 'tab', 0);
    if ($canEditWiki) {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('edit this page') . '">', '', '<form action="?m='.$m.'&amp;a='.$a.'&amp;project_id='.$_GET['project_id'].'&amp;tab='.$tab.'&amp;suba=addedit&page='.$wikipage_namespace.':'.$page.'&wikipage_id='.$wikipage_id.'" method="post">', '</form>');
    }
    if ($canAdd) {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('Upload file') . '">', '', '<form action="?m='.$m.'&amp;a='.$a.'&amp;project_id='.$_GET['project_id'].'&amp;tab='.$tab.'&amp;page=Special:Upload" method="post">', '</form>');
    }
    if ($a) {
        $redirect .= "&a=".$a;
    }
    if (isset($_GET['project_id'])) {
        $redirect .= "&project_id=".$_GET['project_id'];
    }
    $redirect .= "&tab=".$tab;
}
$titleBlock->show();
if (function_exists('styleRenderBoxTop') && !isset($_GET['tab'])) {
    echo styleRenderBoxTop();
}
$AppUI->setState("projectpage", $wikipage_namespace.':'.$wikipage->wikipage_title);

$imagefile = W2P_BASE_DIR . '/modules/documentation/images/upload/'.$wikipage->wikipage_name;;
$size = round(filesize($imagefile) / 1000);
list($width, $height, $type, $attr) = getimagesize($imagefile);

?>
<style type="text/css">
@import "modules/documentation/css/documentation.css";
</style>
<table class="std" style="width:100%">
    <tr>
        <th><strong><?php echo $wikipage_namespace.':'.$wikipage->wikipage_title; ?></strong></th>
    </tr>
    <tr>
        <td style="background: white">
             <table border="0" style="width:100%">
                <tr>
                    <td>
                        <?php  echo $wiki->imageTag($wikipage->wikipage_name, ""); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php 
                            echo "(".$width." x ".$height." ".$AppUI->_('pixel').", ".
                                $AppUI->_('file size').": ".$size." ".$AppUI->_('KB').", ".
                                $AppUI->_('MIME type').": ".image_type_to_mime_type($type).
                                ")"; 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <?php  echo $wiki->render($wikipage); ?>
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

