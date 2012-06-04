<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$ttl = 'Documentation';
$titleBlock = new CTitleBlock(
    $ttl,
    'documentation.png',
    'documentation',
    'documentation' . '.' . $a
);
$titleBlock->addCell();
$titleBlock->show();
?>

<script language="javascript">
function submitIt() {
  var f = document.prjFrm;
  var msg ='';
  if (f.page.value == 0) {
    msg += '<?php echo $AppUI->_('You must select a project first', UI_OUTPUT_JS); ?>';
    f.page.focus();
  }
    
  if (msg.length < 1) {
    f.submit();
  } else {
    alert(msg);
  }
}
</script>
<?php
if (function_exists('styleRenderBoxTop')) {
    echo styleRenderBoxTop();
}
?>
<table border="1" cellpadding="4" cellspacing="0" width="100%" class="std">
<form name="prjFrm" action="?m=documentation" method="post">
<tr>
  <td nowrap="nowrap" style="border: outset #eeeeee 1px;background-color:#fffff" >
    <font color="<?php echo bestColor('#ffffff'); ?>">
      <strong><?php echo $AppUI->_('Project'); ?>: <?php echo arraySelect($projects, 'page', 'onchange="submitIt()" class="text" style="width:500px"', 0); ?></strong>
    </font>
  </td>
</tr>            
</form>
</table>
