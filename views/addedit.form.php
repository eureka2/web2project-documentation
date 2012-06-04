<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}
$titleBlock = new CTitleBlock($wikipage_id ? 'Edit page' : 'New page', 'documentation.png', $m, $m.'.' . $a);
if ($canDelete && $wikipage_id > 0) {
    $titleBlock->addCrumbDelete('Delete page', $canDelete, 'delete this page');
}
$titleBlock->show();
$redirect = "m=".$m;
if ($m != 'documentation') {
    if ($a) {
        $redirect .= "&a=".$a;
    }
    if (isset($_GET['project_id'])) {
        $redirect .= "&project_id=".$_GET['project_id'];
    }
    $tab = $AppUI->processIntState('ProjVwTab', $_GET, 'tab', 0);
    $redirect .= "&tab=".$tab;
}
$AppUI->setState("projectpage", $wikipage['wikipage_namespace'].':'.$wikipage['wikipage_title']);
$wiki = new CWiki();
$wikipages = $wiki->loadIndex($wikipage['wikipage_namespace']);
$images = $wiki->loadIndex('Image');
?>
<style type="text/css">
    @import "modules/documentation/js/markitup/skins/simple/style.css";
    @import "modules/documentation/js/markitup/sets/<?php echo strtolower($wikipage['wikipage_parser']); ?>/style.css";
    @import "modules/documentation/js/autocomplete/jquery.autocomplete.css";
</style>
<script type="text/javascript">
var pages = [
<?php foreach ($wikipages as $wpage) { ?>
    "<?php echo $wpage->wikipage_title; ?>",
<?php } ?>
];
var images = [
<?php foreach ($images as $image) { ?>
    "<?php echo $image->wikipage_name; ?>",
<?php } ?>
];
var languages = [
'actionscript3',
'applescript',
'as3',
'bash',
'c',
'c#',
'c-sharp',
'cf',
'coldfusion',
'cpp',
'csharp',
'css',
'delphi',
'diff',
'erl',
'erlang',
'groovy',
'java',
'javafx',
'javascript',
'jfx',
'js',
'jscript',
'html',
'patch',
'pas',
'pascal',
'perl',
'php',
'pl',
'plain',
'py',
'python',
'rails',
'rb',
'ror',
'ruby',
'sass',
'scala',
'scss',
'shell',
'sql',
'text',
'vb',
'vbnet',
'xhtml',
'xml',
'xslt'
];
var MarkupHelper = {
    linkEventsAttached: false,
    codeEventsAttached: false,
    imageEventsAttached: false,
    linkPromptText: "<?php echo $AppUI->_('Your text to link here...', UI_OUTPUT_JS); ?>",
    imagePromptText: "<?php echo $AppUI->_('Your text to link here...', UI_OUTPUT_JS); ?>",
    askLink: function (markItUp) {
        this.bindLinkEvents(markItUp);
        $("input[name=internal-link]").val("");
        if (markItUp.selection)
            $("input[name=internal-link-text]").val(markItUp.selection);
        else
            $("input[name=internal-link-text]").val(this.linkPromptText);
        $('#internal-link-dialog').modal({
            opacity:70,
            persist:true,
            overlayCss: {
                backgroundColor:'gray'
            }
        });
    },       
    askCode: function (markItUp) {
        this.bindCodeEvents(markItUp);
        $("input[name=code]").val("");
        $('#code-dialog').modal({
            opacity:70,
            persist:true,
            overlayCss: {
                backgroundColor:'gray'
            }
        });
    },       
    askImage: function (markItUp) {
        this.bindImageEvents(markItUp);
        $("input[name=image]").val("");
        if (markItUp.selection)
            $("input[name=image-text]").val(markItUp.selection);
        else
            $("input[name=image-text]").val(this.imagePromptText);
        $('#image-dialog').modal({
            opacity:70,
            persist:true,
            overlayCss: {
                backgroundColor:'gray'
            }
        });
    },       
    bindLinkEvents: function (markItUp) {
        if (this.linkEventsAttached) {
            return;
        }
        this.linkEventsAttached = true;
        var self = this;
        $("input[name=internal-link-ok]").bind('click', function (event) {
            event.stopPropagation();
            event.preventDefault();
            var url = $("input[name=internal-link]").val();
            var text = $("input[name=internal-link-text]").val();
            var linkElement = '[[' + url;
            if (text != self.linkPromptText && text != "")
                linkElement += '|' + text;
            linkElement += ']]';
            $(markItUp.textarea).trigger(
                'insertion',
                [{replaceWith: linkElement}]
            );
            $.modal.close();
        });
        $("input[name=internal-link-cancel]").bind('click', function (event) {
            event.preventDefault();
            $.modal.close();
        });
        $("input[name=internal-link-text]").bind('focus', function (event) {
            if ($("input[name=internal-link-text]").val() == self.linkPromptText)
                $("input[name=internal-link-text]").val("");
        });
        $("input[name=internal-link-text]").bind('blur', function (event) {
            if ($("input[name=internal-link-text]").val() == "")
                $("input[name=internal-link-text]").val(self.linkPromptText);
        });
    },
    bindCodeEvents: function (markItUp) {
        if (this.codeEventsAttached) {
            return;
        }
        this.codeEventsAttached = true;
        var self = this;
        $("input[name=code-ok]").bind('click', function (event) {
            event.stopPropagation();
            event.preventDefault();
            var code = $("input[name=code]").val();
            var codeElement = '<pre class="brush: ' + code + '">' + "\n";
            if (markItUp.selection)
                codeElement += markItUp.selection;
            codeElement += "\n" + '</pre>';
            $(markItUp.textarea).trigger(
                'insertion', 
                [{
                    replaceWith: codeElement
                }]
            );
            $.modal.close();
        });
        $("input[name=code-cancel]").bind('click', function (event) {
            event.preventDefault();
            $.modal.close();
        });
    },
    bindImageEvents: function (markItUp) {
        if (this.imageEventsAttached) {
            return;
        }
        this.imageEventsAttached = true;
        var self = this;
        $("input[name=image-ok]").bind('click', function (event) {
            event.stopPropagation();
            event.preventDefault();
            var image = $("input[name=image]").val();
            var text = $("input[name=image-text]").val();
            var imageElement = '[[<?php echo $AppUI->_('Image'); ?>:' + image;
            if (text != self.linkPromptText && text != "")
                imageElement += '|' + text;
            imageElement += ']]';
            $(markItUp.textarea).trigger(
                'insertion',
                [{replaceWith: imageElement}]
            );
            $.modal.close();
        });
        $("input[name=image-cancel]").bind('click', function (event) {
            event.preventDefault();
            $.modal.close();
        });
        $("input[name=image-text]").bind('focus', function (event) {
            if ($("input[name=image-text]").val() == self.imagePromptText)
                $("input[name=image-text]").val("");
        });
        $("input[name=image-text]").bind('blur', function (event) {
            if ($("input[name=image-text]").val() == "")
                $("input[name=image-text]").val(self.imagePromptText);
        });
    }

};
<?php include_once (W2P_BASE_DIR."/modules/documentation/js/markitup/sets/".strtolower($wikipage['wikipage_parser'])."/set.php");?>
function delIt() {
    if (confirm('<?php echo $AppUI->_('Are you sure you want to delete this page ?', UI_OUTPUT_JS); ?>')) {
        document.editFrm.del.value = '1';
        document.editFrm.submit();
    }
}  
function submitIt() {
    var form = document.editFrm;
    if (form.wikipage_namespace.value == '') {
        alert( '<?php echo $AppUI->_('wikipageValidProject', UI_OUTPUT_JS); ?>' );
        form.wikipage_namespace.focus();
    } else if (form.wikipage_title.value == '') {
        alert( '<?php echo $AppUI->_('wikipageValidTitle', UI_OUTPUT_JS); ?>' );
        form.wikipage_title.focus();
    } else {
        if (form.wikipage_content.length < 2) {
            alert( '<?php echo $AppUI->_('wikipageValidContent', UI_OUTPUT_JS); ?>' );
            form.wikipage_content.focus();
        } else {
            form.submit();
        }
    }
}
$.getScript ("modules/documentation/js/markitup/jquery.markitup.js", function () {
    $.getScript ("modules/documentation/js/simplemodal/jquery.simplemodal.js", function () {
        $.getScript ("modules/documentation/js/autocomplete/jquery.autocomplete.js", function () {
            jQuery(document).ready(function() {
                jQuery("#wikipage_content").markItUp(mySettings);
                jQuery("input[name=internal-link]").autocomplete(
                    pages, { 
                        minChars: 0,
                        max: 100
                    }
                );
                jQuery("input[name=code]").autocomplete(
                    languages, {
                        minChars: 0,
                        max: 100
                    }
                );
                jQuery("input[name=image]").autocomplete(
                    images, {
                        minChars: 0,
                        max: 100
                    }
                );
            });
        });
    });
});
</script>
<form name="editFrm" action="?m=documentation" method="post">
<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">
    <input type="hidden" name="dosql" value="do_documentation_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
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
        <td width="160">
            <input type="button" value="<?php echo $AppUI->_('back'); ?>" class="button" onclick="javascript:history.back(-1);" />
        </td>
        <td align="right">
            <input type="button" value="<?php echo $AppUI->_('submit'); ?>" class="button" onclick="submitIt()" />
        </td>
    </tr>
</table>
</form>
<div id="internal-link-dialog" style="display:none; background:#FFF; border: 1px black solid; padding:0px 10px 10px 10px;">
<h2><?php echo $AppUI->_('Insert an internal link'); ?></h2>
<form>
<span style="width:20%; text-align:right;"><?php echo $AppUI->_('link'); ?> : </span><input type="text" name="internal-link" value="" style="width:80%" /><br />
<span style="width:20%; text-align:right;"><?php echo $AppUI->_('text'); ?> : </span><input type="text" name="internal-link-text" value="" style="width:80%" /><br />
<input type="button" name="internal-link-ok" value="<?php echo $AppUI->_('Ok'); ?>" class="button" />
<input type="button" name="internal-link-cancel" value="<?php echo $AppUI->_('cancel'); ?>" class="button" />
</form>
</div>
<div id="code-dialog" style="display:none; background:#FFF; border: 1px black solid; padding:0px 10px 10px 10px;">
<h2><?php echo $AppUI->_('Choose a language'); ?></h2>
<form>
<span style="width:20%; text-align:right;"><?php echo $AppUI->_('language'); ?> : </span><input type="text" name="code" value="" style="width:80%" /><br />
<input type="button" name="code-ok" value="<?php echo $AppUI->_('Ok'); ?>" class="button" />
<input type="button" name="code-cancel" value="<?php echo $AppUI->_('cancel'); ?>" class="button" />
</form>
</div>
<div id="image-dialog" style="display:none; background:#FFF; border: 1px black solid; padding:0px 10px 10px 10px;">
<h2><?php echo $AppUI->_('Choose a picture'); ?></h2>
<form>
<span style="width:20%; text-align:right;"><?php echo $AppUI->_('Image'); ?> : </span><input type="text" name="image" value="" style="width:80%" /><br />
<span style="width:20%; text-align:right;"><?php echo $AppUI->_('text'); ?> : </span><input type="text" name="image-text" value="" style="width:80%" /><br />
<input type="button" name="image-ok" value="<?php echo $AppUI->_('Ok'); ?>" class="button" />
<input type="button" name="image-cancel" value="<?php echo $AppUI->_('cancel'); ?>" class="button" />
</form>
</div>
