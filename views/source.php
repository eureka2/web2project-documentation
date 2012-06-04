<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$titleBlock = new CTitleBlock($wikipage_id ? 'Edit page' : 'New page', 'documentation.png', 'documentation', 'documentation'.'.' . $a);
if ($canDelete) {
    $titleBlock->addCrumbDelete('Delete page', $canDelete, 'delete this page');
}
$titleBlock->show();
?>

<script type="text/javascript">
  function delIt() {
    document.editFrm.del.value = '1';
    document.editFrm.submit();
  }  
</script>

<form name="editFrm" action="?m=documentation" method="post">
<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">
  <input type="hidden" name="dosql" value="do_documentation_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="wikipage_namespace" value="<?php echo $wikipage['wikipage_namespace']; ?>" />
  <input type="hidden" name="wikipage_name" value="<?php echo $wikipage['wikipage_name']; ?>" />
  <input type="hidden" name="wikipage_id" value="<?php echo $wikipage_id; ?>" />
  <?php
  ?>
  <tr>
    <td align="left"><strong><?php echo $wikipage_namespace.':'.$page; ?></strong></td>
    <td align="right"><?php echo $AppUI->_('Syntax') .": ".$wikipage['wikipage_parser']; ?></td>
  </tr>
  <tr>
    <td align="left" colspan="2">
      <?php echo $AppUI->_('Page title'); ?>:<input type="text" class="text" name="wikipage_title" value="<?php echo $wikipage['wikipage_title']; ?>" size="80" maxlength="255" /><span class="smallNorm">(<?php echo $AppUI->_('required'); ?>)</span>
      <?php if ($wikipage_namespace == 'Category' || $wikipage_namespace == 'Template') { ?>
      <input type="hidden" name="wikipage_start" value="0" />
      <?php } else { ?> 
      <input type="checkbox" name="wikipage_start" value="1" <?php if ($wikipage['wikipage_start'] > 0) echo 'checked="checked"'; ?>/><?php echo $AppUI->_("Start page ?"); ?>
      <?php } ?> 
    </td>
  </tr>
  <tr>
    <td align="left" colspan="2">
      <textarea cols="120" rows="16" class="textarea" style="width:100%" id="wikipage_content" name="wikipage_content"><?php echo $wikipage['wikipage_content']; ?></textarea>
    </td>
  </tr>
  <tr>
    <td align="left" colspan="2">
      <?php
      $wiki = new CWiki($wikipage['wikipage_namespace']);
      $page = $wiki->loadPage($wikipage['wikipage_namespace'].':'.$wikipage['wikipage_name']);
      $templates = $wiki->getTemplates($page);
      ?>
      <div class="templatesUsed">
        <div class="mw-templatesUsedExplanation"><p><?php echo $AppUI->_('Templates used by this page'); ?>&nbsp;:</p></div>
        <ul>
          <?php
          foreach ($templates as $template) {
          ?>
          <li><?php echo $wiki->internalLinkTag('Template:'.$template[0], $template[1]); ?> <?php echo $wiki->internalLinkEditTag('Template:'.$template[0]); ?> </li>
          <?php
          }
          ?>
        <ul>
      </div>
    </td>
  </tr>
  <tr>
    <td width="160">
      <input type="button" value="<?php echo $AppUI->_('back'); ?>" class="button" onclick="javascript:history.back(-1);" />
    </td>
  </tr>
</table>
</form>
