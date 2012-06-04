<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}
require_once (W2P_BASE_DIR . "/modules/documentation/lib/htmldoc/htmldoc.class.php");
require_once (W2P_BASE_DIR . "/modules/documentation/lib/wkhtmltopdf/wkpdf.class.php");
require_once (W2P_BASE_DIR . "/modules/documentation/lib/prince/prince.class.php");

/* This file will write a php config file to be included during execution of
* all documentation files which require the configuration options. */
global $m;

// Deny all but system admins
if (!canEdit('system')) {
  $AppUI->redirect('m=public&a=access_denied');
}

function disabled ($value) 
{
    switch ($value) {
        case 'wkpdf':
            if (WKPDF::isAvailable()){
                return '';
            }
            break;
        case 'prince':
            if (PRINCE::isAvailable()){
                return '';
            }
            break;
        case 'htmldoc':
            if (HTMLDOC::isAvailable()){
                return '';
            }
            break;
        default:
            return '';
    }
    return 'disabled="disabled"';
}

$CONFIG_FILE = W2P_BASE_DIR . '/modules/documentation/config.php';

$AppUI->savePlace();

$config_options = array(
    'heading1' => $AppUI->_('General Options'), 
    'default_parser' => array(
        'description' => $AppUI->_('Wiki text parser'), 
        'value' => 'MediaWiki', 
        'type' => 'select', 
        'list' => array(
            'Textile' => 'Textile', 
            'Creole' => 'Creole', 
            'MediaWiki' => 'MediaWiki', 
            'Markdown' => 'Markdown', 
            'BBCode' => 'BBCode'
        )
    ),
    'default_lang' => array(
        'description' => $AppUI->_('Wiki default language'), 
        'value' => 'en', 
        'type' => 'select', 
        'list' => array(
            'de' => 'Deutch', 
            'en' => 'English', 
            'es' => 'Español', 
            'fr' => 'Français', 
            'pt' => 'Portuguès'
        )
    ),
    'default_pdf_converter' => array(
        'description' => $AppUI->_('PDF converter'), 
        'value' => 'wkhtmltopdf', 
        'type' => 'radio', 
        'buttons' => array(
            'wkpdf' => 'wkhtmltopdf', 
            'prince' => 'Prince XML', 
            'htmldoc' => htmlspecialchars('<HTML>DOC'), 
            'mpdf' => 'mPDF',
            'html2pdf' => 'HTML2PDF (TCPDF)'
        )
    ),
);

//if this is a submitted page, overwrite the config file.
if (w2PgetParam($_POST, 'Save', '') != '') {

    if (is_writable($CONFIG_FILE)) {
        if (!$handle = fopen($CONFIG_FILE, 'w')) {
            $AppUI->setMsg($CONFIG_FILE . ' ' . $AppUI->_('cannot be opened'), UI_MSG_ERROR);
            exit;
        }

        if (fwrite($handle, "<?php //Do not edit this file by hand, it will be overwritten by the configuration utility. \n") === false) {
            $AppUI->setMsg($CONFIG_FILE . ' ' . $AppUI->_('cannot be written to'), UI_MSG_ERROR);
            exit;
        } else {
            foreach ($config_options as $key => $value) {
                if (substr($key, 0, 7) == 'heading')
                    continue;

                $val = '';
                switch ($value['type']) {
                    case 'checkbox':
                        $val = isset($_POST[$key]) ? '1': '0';
                        break;
                    case 'text':
                        $val = isset($_POST[$key]) ? $_POST[$key]: '';
                        break;
                    case 'longtext':
                        $val = isset($_POST[$key]) ? $_POST[$key]: '';
                        break;
                    case 'select':
                        $val = isset($_POST[$key]) ? $_POST[$key]: '0';
                        break;
                    case 'radio':
                        $val = $_POST[$key];
                        break;
                    default:
                        break;
                }

                fwrite($handle, "\$WIKI_CONFIG['" . $key . "'] = '" . $val . "';\n");
            }

            fwrite($handle, "?>\n");
            $AppUI->setMsg($CONFIG_FILE . ' ' . $AppUI->_('has been successfully updated'), UI_MSG_OK);
            fclose($handle);
            require ($CONFIG_FILE);
        }
    } else {
        $AppUI->setMsg($CONFIG_FILE . ' ' . $AppUI->_('is not writable'), UI_MSG_ERROR);
    }
} elseif (w2PgetParam($_POST, $AppUI->_('Cancel'), '') != '') {
    $AppUI->redirect('m=system&a=viewmods');
}

include ($CONFIG_FILE);

//Read the current config values from the config file and update the array.
foreach ($config_options as $key => $value) {
    if (isset($WIKI_CONFIG[$key])) {
        $config_options[$key]['value'] = $WIKI_CONFIG[$key];
    }
}

// setup the title block
$titleBlock = new CTitleBlock('Documentation Module Configuration', 'documentation.png', $m,  $m . '.' . $a);
$titleBlock->addCrumb('?m=system', 'System Admin');
$titleBlock->addCrumb('?m=system&a=viewmods', 'Modules');
$titleBlock->show();

?>

<form method="post">
<table class="std" style="width:100%">
<?php
foreach ($config_options as $key => $value) {
?>
  <tr>
    <?php
  // the key starts with hr, then just display the value
  if (substr($key, 0, 7) == 'heading') { ?>
      <th align="center" colspan="2"><?php echo $value?></th>
    <?php } else { ?>
    <td align="right" style="width:30%"><?php echo $value['description']?></td>
    <td align="left" style="width:70%"><?php
    switch ($value['type']) {
      case 'checkbox': ?>
          <input type="checkbox" name="<?=$key?>" <?php echo $value['value']?"checked=\"checked\"":""?> />
          <?php
        break;
      case 'text': ?>
          <input type="text" name="<?=$key?>" style="<?php echo $value['style']?>" value="<?php echo $value['value']?>" />
          <?php
        break;
      case 'longtext': ?>
          <input type="text" size="70" name="<?=$key?>" style="<?php echo $value['style']?>" value="<?php echo $value['value']?>" />
          <?php
        break;
      case 'select':
        print arraySelect($value["list"], $key, 'class="text" size="1" id="' . $key . '" ' . $value["events"], $value["value"]);
        break;
      case 'radio':
        foreach ($value['buttons'] as $v => $n) { ?>
            <br /><label><input type="radio" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $v; ?>" <?php echo (($value['value'] == $v) ? 'checked="checked"' : ''); ?> <?php echo disabled($v); ?> <?php echo $value['events']; ?> /> <?php echo $n; ?></label>
          <?php }
        break;
      default:
        break;
    }
?></td>
    <?php
  }
?>
  </tr>
<?php
}
?>
  <tr>
    <td colspan="2" align="right"><input type="Submit" name="Cancel" class="button" value="<?php echo $AppUI->_('back')?>" /><input type="Submit" class="button" name="Save" value="<?php echo $AppUI->_('save')?>" /></td>
  </tr>
</table>
</form>