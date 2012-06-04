<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once (W2P_BASE_DIR . "/modules/documentation/lib/htmldoc/htmldoc.class.php");
require_once (W2P_BASE_DIR . "/modules/documentation/lib/wkhtmltopdf/wkpdf.class.php");
require_once (W2P_BASE_DIR . "/modules/documentation/lib/prince/prince.class.php");
require_once (W2P_BASE_DIR . "/modules/documentation/lib/geshi/geshi.php");
require_once (W2P_BASE_DIR . "/modules/documentation/config.php");
ini_set ('max_execution_time', 600); 
$namespace = w2PgetParam($_REQUEST, 'namespace', '');

global $AppUI;
// check permissions for this module
$canView = canView('documentation');
$canAdd = canAdd('documentation');
if (!$canView) {
    $AppUI->redirect('m=public&a=access_denied');
}
$q = new w2p_Database_Query;
$q->addTable("projects");
$q->addQuery("project_name, project_description");
$q->addWhere("project_short_name = '".$namespace."'");
$project = $q->loadHash();
$q->clear();

$wiki = new CWiki($namespace);
$startpage = $wiki->loadStartPage();
$startpage->wikipage_content = "__NOTOC__\n".$startpage->wikipage_content;
$q = new w2p_Database_Query;
$q->addTable("wikipages");
$q->addQuery("*");
$q->addWhere("wikipage_start = 0 and wikipage_namespace = '" . $namespace . "'");
$q->addOrder("wikipage_title");
$pages = $q->loadList();
$geshi_prefont = "monospace";
$geshi_header_type = false;
$geshi_overall_style = false;
$geshi_code_style = false;
$geshi_line_style = false;
$converter = $WIKI_CONFIG['default_pdf_converter'];
if($converter == "wkpdf" && WKPDF::isAvailable()){
    $content = parse_codes($wiki->render($startpage));
    $pdf = new WKPDF('P', 'A4', $startpage->wikipage_lang, true, 'UTF-8', array(5, 8, 5, 8));
    $pdf->setTitle($project['project_name']); 
    $pdf->setSubject($project['project_description']);
    if ($startpage->wikipage_categorylinks) {
        $pdf->setKeywords(str_replace("|", ", ",$startpage->wikipage_categorylinks));
    }
    $pdf->setBase(W2P_BASE_DIR . '/');
    $pdf->setBaseURL(W2P_BASE_URL . '/');
    $pdf->addStyleSheet("modules/documentation/css/documentation-pdf.css");    
    $pdf->addInlineStyles('
        #documentation {
            font-size: 1.3em;
        }
        #documentation pre{
            background-color: lightyellow;
        }
        #documentation pre ol {
            font-size: 0.9em;
        }
    ');
    $pdf->writeHTML($content); 
    foreach($pages as $page) {
        $wikipage = new CDocumentation();
        $wikipage->bind($page);
        $wikipage->wikipage_content = "__NOTOC__\n".$wikipage->wikipage_content;
        $content = parse_codes($wiki->render($wikipage));
        $pdf->writeHTML($content); 
    }
    $pdf->createIndex($AppUI->_('Table of Contents'));
    $pdf->Output($namespace.".pdf", WKPDF::$PDF_EMBEDDED); 
} elseif($converter == "prince" && PRINCE::isAvailable()){
    $content = parse_codes($wiki->render($startpage));
    $pdf = new PRINCE('P', 'A4', $startpage->wikipage_lang, true, 'UTF-8', array(5, 8, 5, 8));
    $pdf->setTitle($project['project_name']); 
    $pdf->SetSubject($project['project_description']);
    if ($startpage->wikipage_categorylinks) {
        $pdf->setKeywords(str_replace("|", ", ",$startpage->wikipage_categorylinks));
    }
    $pdf->setBase(W2P_BASE_DIR . '/');
    $pdf->setBaseURL(W2P_BASE_URL . '/');
    $pdf->addStyleSheet("modules/documentation/css/documentation-pdf.css");    
    $pdf->addInlineStyles('
        #documentation pre{
            background-color: lightyellow;
        }
    ');
    $pdf->writeHTML($content); 
    foreach($pages as $page) {
        $wikipage = new CDocumentation();
        $wikipage->bind($page);
        $wikipage->wikipage_content = "__NOTOC__\n".$wikipage->wikipage_content;
        $content = parse_codes($wiki->render($wikipage));
        $pdf->writeHTML($content); 
    }
    $pdf->createIndex($AppUI->_('Table of Contents'));
    $pdf->Output($namespace.".pdf", WKPDF::$PDF_EMBEDDED); 
} elseif($converter == "htmldoc" && HTMLDOC::isAvailable()){
    $content = parse_codes($wiki->render($startpage));
    $pdf = new HTMLDOC('P', 'A4', $startpage->wikipage_lang, true, 'UTF-8', array(5, 5, 8, 8));
    $pdf->setTitle($project['project_name']); 
    $pdf->setSubject($project['project_description']);
    if ($startpage->wikipage_categorylinks) {
        $pdf->setKeywords(str_replace("|", ", ",$startpage->wikipage_categorylinks));
    }
    $pdf->setBase(W2P_BASE_DIR . '/');
    $pdf->writeHTML($content); 
    foreach($pages as $page) {
        $wikipage = new CDocumentation();
        $wikipage->bind($page);
        $wikipage->wikipage_content = "__NOTOC__\n".$wikipage->wikipage_content;
        $content = parse_codes($wiki->render($wikipage));
        $pdf->writeHTML($content); 
    }
    $pdf->createIndex($AppUI->_('Table of Contents'));
    $pdf->setDefaultFont("Helvetica");
    $pdf->Output($namespace.".pdf", WKPDF::$PDF_EMBEDDED); 
} elseif ($converter == "mpdf"){
    require_once (W2P_BASE_DIR . "/modules/documentation/lib/mpdf/w2pmpdf.class.php");
    $pdf = new W2PMPDF('P', 'A4', $startpage->wikipage_lang, true, 'UTF-8', array(5, 5, 8, 8));
    $pdf->setDefaultFont("Helvetica");
    $geshi_prefont = "courier";
    $geshi_header_type = GESHI_HEADER_PRE_TABLE;
    $geshi_overall_style = 'background: lightyellow;width:100%;margin:0;padding:0;';
    $geshi_code_style = 'background: white;width:100%;border-left:1px solid green;padding-left:5px;white-space: nowrap;';
    $geshi_line_style = 'margin:0;padding:0;width:100%;background: white;';
    $html = parse_codes($wiki->render($startpage));
    $pdf->setTitle($project['project_name']); 
    $pdf->setSubject($project['project_description']);
    if ($startpage->wikipage_categorylinks) {
        $pdf->setKeywords(str_replace("|", ", ",$startpage->wikipage_categorylinks));
    }
    $pdf->setBase(W2P_BASE_DIR . '/');
    $pdf->setBaseURL(W2P_BASE_URL . '/');
    $pdf->addStyleSheet("modules/documentation/css/documentation-pdf.css");    
    $pdf->writeHTML($html);
    foreach($pages as $page) {
        $wikipage = new CDocumentation();
        $wikipage->bind($page);
        $wikipage->wikipage_content = "__NOTOC__\n".$wikipage->wikipage_content;
        $content = parse_codes($wiki->render($wikipage));
        $pdf->writeHTML($content); 
    }
    $pdf->createIndex($AppUI->_('Table of Contents'));
    $pdf->Output($namespace.".pdf", WKPDF::$PDF_EMBEDDED); 
} else {
    $html = preg_replace(
        '/<h([1-6])(\s?[^<>]*)>([^<]+)<\/h([1-6])>/i',
        '<bookmark title="$3" level="$1" ></bookmark><h$1$2>$3</h$4>',
        $wiki->render($startpage)
    );
    $geshi_prefont = "courier";
    $html = parse_codes($html);
    $content =
'<link type="text/css" href="modules/documentation/css/documentation-pdf.css" rel="stylesheet" >
<page backtop="10mm" style="font-size: 10pt">
    <page_header>
        <table style="width: 100%"> 
            <tr> 
                <td style="width: 50%; text-align: left"> 
                    <strong>'.$project['project_name'].'</strong>
                </td> 
                <td style="width: 50%; text-align: right"> 
                </td> 
            </tr> 
        </table> 
    </page_header>
</page>
<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm" style="font-size: 10pt">
    <page_header>
        <table style="width: 100%; border-bottom:1x solid gray"> 
            <tr> 
                <td style="width: 50%; text-align: left"> 
                    <strong>'.$project['project_name'].'</strong>
                </td> 
                <td style="width: 50%; text-align: right"> 
                    '.date('d-m-Y').' 
                </td> 
            </tr> 
        </table> 
    </page_header>
    <page_footer>
        <div style="width: 100%; text-align:center; border-top:1x solid gray">
            '.$AppUI->_('Page').' [[page_cu]]/[[page_nb]]
        </div>
    </page_footer>
    <bookmark title="'.$startpage->wikipage_title.'" level="0" ></bookmark>'.
    $html.'
</page>';
    foreach($pages as $page) {
        $wikipage = new CDocumentation();
        $wikipage->bind($page);
        $wikipage->wikipage_content = "__NOTOC__\n".$wikipage->wikipage_content;
        $html = preg_replace(
            '/<h([1-6])(\s?[^<>]*)>([^<]+)<\/h([1-6])>/i',
            '<bookmark title="$3" level="$1" ></bookmark><h$1$2>$3</h$4>',
            $wiki->render($wikipage)
        );
        $html = parse_codes($html);
        $content .= 
'<page pageset="old">
    <bookmark title="'.$wikipage->wikipage_title.'" level="0" ></bookmark>'.
    $html.'
</page>';
    }
    require_once (W2P_BASE_DIR . "/modules/documentation/lib/html2pdf/html2pdf.class.php");
    $pdf = new HTML2PDF('P', 'A4', $startpage->wikipage_lang, true, 'UTF-8', array(5, 5, 8, 8));
    $pdf->pdf->SetTitle($project['project_name']);
    $pdf->pdf->SetSubject($project['project_description']);
    $pdf->writeHTML($content); 
    $pdf->createIndex($AppUI->_('Table of Contents'), 25, 12, false, true, 1);
    $pdf->Output($namespace.".pdf", "I"); 
}

function highlight_code($language, $code)
{
    global $geshi_prefont, $geshi_header_type, $geshi_overall_style, $geshi_code_style, $geshi_line_style;
    if (! $language) {
        return $code;
    }
    $code = preg_replace("/^\r?\n/", "", $code);
    $code = preg_replace("/\r?\n$/", "", $code);
    $geshi = new GeSHi($code, $language);
    if ($geshi_header_type !== false) {
        $geshi->set_header_type($geshi_header_type);
    }
    if ($geshi_overall_style !== false) {
        $geshi->set_overall_style($geshi_overall_style);
    }
    if ($geshi_code_style !== false) {
        $geshi->set_code_style($geshi_code_style);
    }
    if ($geshi_line_style !== false) {
        $geshi->set_line_style($geshi_line_style);
    }
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $code = $geshi->parse_code();
    if ($geshi_prefont != "monospace") {
        $code = str_replace("font-family:monospace", "font-family:".$geshi_prefont, $code);
    }
    return $code;
}

function parse_codes($input)
{
    $regex = '#<pre(\s*class="brush:([^>]+)")?>((?:[^<]|<(?!/?pre(\s*class=([^>]+))?>)|(?R))+)</pre>#';
    if (is_array($input)) {
        $input = highlight_code(trim($input[2]), $input[3]);
    }
    return preg_replace_callback($regex, 'parse_codes', $input);
}
