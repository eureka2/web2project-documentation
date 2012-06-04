<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta name="Description" content="web2Project WebbPlatsen Redmond Style">
<meta name="Version" content="2.3.0">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
<title>Gestion des projets du pôle Diffusion/Internet</title>
<link rel="stylesheet" type="text/css" href="./style/common.css" media="all" charset="utf-8">
<link rel="stylesheet" type="text/css" href="./style/wps-redmond/main.css" media="all" charset="utf-8">
<style type="text/css" media="all">@import "./style/wps-redmond/main.css";</style>
<link rel="shortcut icon" href="./style/wps-redmond/favicon.ico" type="image/ico">
<script type="text/javascript" src="js/base.js"></script>
<script type="text/javascript" src="lib/jquery/jquery.js"></script>
</head>
<body>
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
               title="<?php echo $AppUI->_('Show as PDF'); ?>">
            <img border="0" src="<?php echo w2PfindImage('pdficon_small.gif', 'documentation'); ?>" alt="PDF"/>
            </a>
        </th>
    </tr>
    <tr>
        <td colspan="2" style="background: white"><?php echo $wiki->render($wikipage); ?></td>
    </tr>
</table>
<script language="javascript">
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
</body>
</html>
