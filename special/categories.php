<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

require_once (W2P_BASE_DIR . '/modules/documentation/documentation.class.php');

$offset = (int) w2PgetParam($_REQUEST, 'offset', 0);
$limit = (int) w2PgetParam($_REQUEST, 'limit', 20);
$dir = w2PgetParam($_REQUEST, 'dir', "");

global $AppUI;
$wiki = new CWiki();
$wikipages = $wiki->loadIndex("Category", $dir, $limit, $offset);

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
        <th>
            <strong><?php echo $AppUI->_('Special').':'.$AppUI->_('Categories'); ?></strong>
            </th>
    </tr>
    <tr>
        <td style="background: white">
            <div id="documentation">
                <p><?php echo $AppUI->_('The following categories exist in the documentation.'); ?></p>
                (<a href="?m=documentation&amp;page=Special:Categories&amp;limit=<?php echo $limit; ?>&amp;offset=0" title="Special:Categories"><?php echo sprintf($AppUI->_('first %d'), $limit); ?></a> | 
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=<?php echo $limit; ?>&amp;dir=prev" title="Special:Categories"><?php echo sprintf($AppUI->_('last %d'), $limit); ?></a>) 
                <?php echo $AppUI->_('View'); ?> 
                (<a href="?m=documentation&amp;page=Special:Categories&amp;limit=<?php echo $limit; ?>&amp;dir=prev&amp;offset=<?php echo $offset; ?>" title="Special:Categories"><?php echo sprintf($AppUI->_('previous %d'), $limit); ?></a>) 
                (<a href="?m=documentation&amp;page=Special:Categories&amp;limit=<?php echo $limit; ?>&amp;offset=<?php echo $offset + $limit; ?>" title="Special:Categories"><?php echo sprintf($AppUI->_('next %d'), $limit); ?></a>) 
                (
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=20" title="Special:Categories">20</a> |
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=50" title="Special:Categories">50</a> |
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=100" title="Special:Categories">100</a> |
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=250" title="Special:Categories">250</a> |
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=500" title="Special:Categories">500</a>
                )
                <ul>
                <?php
                $n = 0;
                foreach ($wikipages as $wikipage) { 
                    $members = $wikipage->wikipage_categorylinks ? sizeof(explode("|", $wikipage->wikipage_categorylinks)) : 0;
                ?>
                    <li>
                        <a href="?m=documentation&page=Category:<?php echo $wikipage->wikipage_name ?>&limit=<?php echo $limit; ?>" title="Category:<?php echo $wikipage->wikipage_title ?>">
                            <?php echo $wikipage->wikipage_title ?>
                        </a>
                        (<?php echo $members." ".$AppUI->_('pages with this category'); ?>)
                    </li>
                <?php 
                    if ($n++ >= $limit) {
                        break;
                    }
                } ?>
                </ul>
                (<a href="?m=documentation&amp;page=Special:Categories&amp;limit=<?php echo $limit; ?>&amp;offset=0" title="Special:Categories"><?php echo sprintf($AppUI->_('first %d'), $limit); ?></a> | 
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=<?php echo $limit; ?>&amp;dir=prev" title="Special:Categories"><?php echo sprintf($AppUI->_('last %d'), $limit); ?></a>) 
                <?php echo $AppUI->_('View'); ?> 
                (<a href="?m=documentation&amp;page=Special:Categories&amp;limit=<?php echo $limit; ?>&amp;dir=prev&amp;offset=<?php echo $offset; ?>" title="Special:Categories"><?php echo sprintf($AppUI->_('previous %d'), $limit); ?></a>) 
                (<a href="?m=documentation&amp;page=Special:Categories&amp;limit=<?php echo $limit; ?>&amp;offset=<?php echo $offset + $limit; ?>" title="Special:Categories"><?php echo sprintf($AppUI->_('next %d'), $limit); ?></a>) 
                (
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=20" title="Special:Categories">20</a> |
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=50" title="Special:Categories">50</a> |
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=100" title="Special:Categories">100</a> |
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=250" title="Special:Categories">250</a> |
                <a href="?m=documentation&amp;page=Special:Categories&amp;limit=500" title="Special:Categories">500</a>
                )
            </div>
        </td>
    </tr>
</table>
 