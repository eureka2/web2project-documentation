<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

$ttl = 'Documentation';
$titleBlock = new CTitleBlock($ttl, 'documentation.png', 'documentation', 'documentation' . '.' . $a);
$titleBlock->addCell();
if ($canDeleteWiki) {
    $titleBlock->addCrumbDelete('Delete page', $canDelete, 'delete this page');
}
$redirect = "m=".$m;
if ($m == 'documentation') {
    if ($wikipage_namespace != 'Category') {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('Go to start page') . '">', '', '<form action="?m=documentation&amp;page='.$wikipage_namespace.':'.'" method="post">', '</form>');
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('Pages index') . '">', '', '<form action="?m=documentation&amp;page=Special:Allpages" method="post">', '</form>');
    }
    if ($canEditWiki) {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('edit this page') . '">', '', '<form action="?m=documentation&amp;a=addedit&amp;page='.$wikipage_namespace.':'.$page.'&wikipage_id='.$wikipage_id.'" method="post">', '</form>');
    } else {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('View source') . '">', '', '<form action="?m=documentation&amp;a=viewsource&amp;page='.$wikipage_namespace.':'.$page.'&wikipage_id='.$wikipage_id.'" method="post">', '</form>');
    }
    if ($canAdd) {
        if ($wikipage_namespace != 'Category') {
            $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('new page') . '">', '', '<form action="?m=documentation&amp;a=addedit&amp;page='.$wikipage_namespace.'" method="post">', '</form>');
        }
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('Upload file') . '">', '', '<form action="?m=documentation&amp;page=Special:Upload" method="post">', '</form>');
    }
    if ($wikipage_namespace != 'Category') {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('Show project documentation as PDF') . '">', '', '<form action="?m=documentation&amp;a=makepdf&amp;suppressHeaders=1&amp;namespace='.$wikipage_namespace.'" method="post">', '</form>');
    }
    $redirect .= "&page=".$wikipage_namespace.":";
} else {
    $tab = $AppUI->processIntState('ProjVwTab', $_GET, 'tab', 0);
    if ($wikipage_namespace != 'Category') {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('Go to start page') . '">', '', '<form action="?m='.$m.'&amp;a='.$a.'&amp;project_id='.$_GET['project_id'].'&amp;tab='.$tab.'" method="post">', '</form>');
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('Pages index') . '">', '', '<form action="?m='.$m.'&amp;a='.$a.'&amp;project_id='.$_GET['project_id'].'&amp;tab='.$tab.'&amp;page=Special:Allpages" method="post">', '</form>');
    }
    if ($canEditWiki) {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('edit this page') . '">', '', '<form action="?m='.$m.'&amp;a='.$a.'&amp;project_id='.$_GET['project_id'].'&amp;tab='.$tab.'&amp;suba=addedit&page='.$wikipage_namespace.':'.$page.'&wikipage_id='.$wikipage_id.'" method="post">', '</form>');
    } else {
        $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('View source') . '">', '', '<form action="?m='.$m.'&amp;a='.$a.'&amp;project_id='.$_GET['project_id'].'&amp;tab='.$tab.'&amp;suba=viewsource&page='.$wikipage_namespace.':'.$page.'&wikipage_id='.$wikipage_id.'" method="post">', '</form>');
    }
    if ($canAdd) {
        if ($wikipage_namespace != 'Category') {
            $titleBlock->addCell('<input type="submit" class="button" value="' . $AppUI->_('new page') . '">', '', '<form action="?m='.$m.'&amp;a='.$a.'&amp;project_id='.$_GET['project_id'].'&amp;tab='.$tab.'&amp;suba=addedit&page='.$wikipage_namespace.'" method="post">', '</form>');
        }
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
?>
<style type="text/css">
@import "modules/documentation/css/documentation.css";
@import "modules/documentation/js/syntaxhighlighter/styles/shCore.css";
@import "modules/documentation/js/syntaxhighlighter/styles/shCoreDefault.css";
</style>
<table class="std" style="width:100%">
    <tr>
        <th style="width: 50%; text-align: left"> 
            <strong><?php echo $wikipage_namespace.':'.$wikipage->wikipage_title; ?></strong>
        </th>
        <th style="width: 50%; text-align: right">
            <a href="index.php?m=documentation&amp;suppressHeaders=1&amp;page=<?php echo $wikipage_namespace.':'.$wikipage->wikipage_name.'/PDF'; ?>"
               title="<?php echo $AppUI->_('Show this page as PDF'); ?>">
            <img border="0" src="<?php echo w2PfindImage('pdficon_small.gif', 'documentation'); ?>" alt="PDF"/>
            </a>
        </th>
    </tr>
    <tr>
        <td colspan="2" style="background: white"><?php echo $wiki->render($wikipage); ?></td>
    </tr>
</table>
<script language="javascript">
function delIt() {
    if (confirm('<?php echo $AppUI->_('Are you sure you want to delete this page ?', UI_OUTPUT_JS); ?>')) {
        document.editFrm.del.value = '1';
        document.editFrm.submit();
    }
}  
$.getScript(
    "modules/documentation/js/syntaxhighlighter/scripts/shCore.js", 
    function () {
        $.getScript(
            "modules/documentation/js/syntaxhighlighter/scripts/shAutoloader.js",
            function () {
                jQuery(document).ready(function() {
                    function path(){  
                        var args = arguments, result = [];
                        for(var i = 0; i < args.length; i++)
                            result.push(args[i].replace(
                                '@',
                                'modules/documentation/js/syntaxhighlighter/scripts/'
                            ));
                        return result
                    }; 
                    SyntaxHighlighter.autoloader.apply(null, path(
                        'applescript            @shBrushAppleScript.js',
                        'actionscript3 as3      @shBrushAS3.js',
                        'bash shell             @shBrushBash.js',
                        'coldfusion cf          @shBrushColdFusion.js',
                        'cpp c                  @shBrushCpp.js',
                        'c# c-sharp csharp      @shBrushCSharp.js',
                        'css                    @shBrushCss.js',
                        'delphi pascal          @shBrushDelphi.js',
                        'diff patch pas         @shBrushDiff.js',
                        'erl erlang             @shBrushErlang.js',
                        'groovy                 @shBrushGroovy.js',
                        'java                   @shBrushJava.js',
                        'jfx javafx             @shBrushJavaFX.js',
                        'js jscript javascript  @shBrushJScript.js',
                        'perl pl                @shBrushPerl.js',
                        'php                    @shBrushPhp.js',
                        'text plain             @shBrushPlain.js',
                        'py python              @shBrushPython.js',
                        'ruby rails ror rb      @shBrushRuby.js',
                        'sass scss              @shBrushSass.js',
                        'scala                  @shBrushScala.js',
                        'sql                    @shBrushSql.js',
                        'vb vbnet               @shBrushVb.js',
                        'xml xhtml xslt html    @shBrushXml.js'
                    ));
                    SyntaxHighlighter.config.stripBrs = true; 
                    SyntaxHighlighter.all(); 
                });
            }
        );
    }
);
</script>
<form name="editFrm" action="?m=documentation" method="post">
  <input type="hidden" name="dosql" value="do_documentation_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
  <input type="hidden" name="wikipage_namespace" value="<?php echo $wikipage_namespace; ?>" />
  <input type="hidden" name="wikipage_name" value="<?php echo $wikipage->wikipage_title; ?>" />
  <input type="hidden" name="wikipage_id" value="<?php echo $wikipage_id; ?>" />
</form>

