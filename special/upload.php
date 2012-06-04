<?php /* $Id$ $URL$ */

if (!defined("W2P_BASE_DIR")) {
  die("You should not access this file directly.");
}
global $AppUI;

$ttl = "Documentation";
$titleBlock = new CTitleBlock($ttl, "documentation.png", "documentation", "documentation" . "." . $a);
$titleBlock->addCell();
$titleBlock->show();
if (function_exists("styleRenderBoxTop") && !isset($_GET["tab"])) {
    echo styleRenderBoxTop();
}

?>
<style type="text/css">
@import "modules/documentation/css/documentation.css";
</style>
<form method="post" enctype="multipart/form-data" action="?m=documentation&amp;page=Special:Upload">
<table id="upload" class="std" style="width:100%">
    <tr>
        <th colspan="2" style="text-align: center;"><strong><?php echo $AppUI->_("Upload file"); ?></strong></th>
    </tr>
    <tr>
        <td colspan="2" style="background: white">
            <div id="uploadtext">
                <p><?php echo sprintf($AppUI->_('Use the form below to upload files, to view or search previously uploaded images go to the <a href="%s" title="%s">list of uploaded files</a>, uploads and deletions are also logged in the <a href="%s" title="%s">upload log</a>.', UI_OUTPUT_RAW), "?m=documentation&amp;page=Special:Imagelist", "Special:Imagelist", "?m=documentation&amp;page=Special:Log/upload", "Special:Log/upload"); ?></p>
                <p><?php echo $AppUI->_('To include the image in a page, use a link in the form <b>[[Image:File.jpg]]</b>, <b>[[Image:File.png|alt text]]</b> or <b>[[Media:File.ogg]]</b> for directly linking to the file.', UI_OUTPUT_RAW); ?></p>
            </div>
            <table border="0" style="width:100%">
                <tr>
                    <td align="right" valign="top"><label for="wpUploadFile"><?php echo $AppUI->_("Source filename"); ?>:</label></td>
                    <td align="left">
                        <input tabindex="1" type="file" name="wpUploadFile" id="wpUploadFile" size="40" />
                    </td>
                </tr>
                <tr>
                    <td align="right"><label for="wpDestFile"><?php echo $AppUI->_("Destination filename"); ?>:</label></td>
                    <td align="left">
                        <input tabindex="2" type="text" name="wpDestFile" id="wpDestFile" size="40" value="" />
                    </td>
                </tr>
                <tr>
                    <td align="right"><label for="wpUploadDescription"><?php echo $AppUI->_("Summary"); ?>:</label></td>
                    <td align="left">
                        <textarea tabindex="3" name="wpUploadDescription" id="wpUploadDescription" rows="6"  cols="80"></textarea>
                    </td>
                </tr>
                <tr><td colspan="2" id="wpDestFile-warning">&nbsp;</td></tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="160">
            <input type="button" value="<?php echo $AppUI->_('back'); ?>" class="button" onclick="javascript:history.back(-1);" />
        </td>
        <td align="right">
            <input tabindex="9" type="button" name="wpUpload" id="wpUpload" value="<?php echo $AppUI->_('Upload the file'); ?>" class="button" title="<?php echo $AppUI->_("Start upload [s]"); ?>" accesskey="s" />
        </td>
    </tr>
</table>
<table id="upload-result" class="std" style="width:100%; display:none">
    <tr>
        <th colspan="2"><strong><?php echo $AppUI->_("Upload file"); ?></strong></th>
    </tr>
    <tr>
        <td colspan="2" style="background: white">
            <table border="0">
                <tr>
                    <td align="right" valign="top"><?php echo $AppUI->_("File Name"); ?>:</td>
                    <td align="left" id="result-filename"></td>
                </tr>
                <tr>
                    <td align="right" valign="top"><?php echo $AppUI->_("File Type"); ?>:</td>
                    <td align="left" id="result-type"></td>
                </tr>
                <tr>
                    <td align="right"><?php echo $AppUI->_("Dimension"); ?>:</td>
                    <td align="left" id="result-dimension"></td>
                </tr>
                <tr>
                    <td align="right"><?php echo $AppUI->_("Size"); ?>:</td>
                    <td align="left" id="result-size"></td>
                </tr>
                <tr>
                    <td align="right"><?php echo $AppUI->_("Summary"); ?>:</td>
                    <td align="left" id="result-summary"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="160">
            <input type="button" value="<?php echo $AppUI->_('back'); ?>" class="button" onclick="javascript:history.back(-1);" />
        </td>
        <td align="right">
            <input tabindex="9" id="new-upload" type="button" value="<?php echo $AppUI->_('Upload a new file'); ?>" class="button" />
        </td>
    </tr>
</table>
</form>
<script type="text/javascript">
$.getScript ("modules/documentation/js/upload/jquery.upload-1.0.2.js", function () {
    $("#wpUploadFile").change(function () {
        var name = $(this).val(), p;
        if ((p = name.lastIndexOf(".")) >= 0)
            name = name.substring(0, p);
        $("#wpDestFile").val(name);
        $("#wpUploadDescription").focus();
    });
    $("#new-upload").click(function() {
        $("#wpUploadFile").val("");
        $("#wpDestFile").val("");
        $("#wpUploadDescription").val("");
        $("#upload-result").hide();
        $("#upload").show();
    });
    $("#wpUpload").click(function() {
        $("#loadingMessage").show();
        $("#upload").upload("index.php?m=documentation&a=upload&suppressHeaders=1", function(res) {
            $("#loadingMessage").hide();
            if (res.error) {
                $("#wpDestFile-warning").html(res.error);
            }
            else {
                $("#result-filename").html(res.filename);
                $("#result-type").html(res.type);
                $("#result-dimension").html(res.width + "x" + res.height);
                $("#result-size").html(res.size);
                $("#result-summary").html(res.summary);
                $("#upload").hide();
                $("#upload-result").show();
            }
        }, "json");
    });
});
</script>