<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

class WKPDF_Exception extends Exception {}
      
class WKPDF {
    private static $cmd = 'wkhtmltopdf';
    private static $cpu=''; 
    private $base = false;
    private $baseurl = false;
    private $pagefiles = array();
    private $tocxslfile;
    private $orientation = 'Portrait';
    private $margins = false;
    private $lang = 'en';
    private $size = 'A4';
    private $encoding = false;
    private $toc = false;
    private $copies = 1;
    private $grayscale = false;
    private $title = '';
    private $subject = false;
    private $author = false;
    private $keywords = false;
    private $permissions = false;
    private $userpass = false;
    private $ownerpass = false;
    private $defaultfont = false;
    private $stylesheets = array();
    private $scripts = array();
    private $inlinescripts = array();
    private $inlinestyles = array();
    private static $available = false;
    private static $availabilitytested = false;

    public static $PDF_DOWNLOAD = 'D';

    public static $PDF_ASSTRING = 'S';

    public static $PDF_EMBEDDED = 'I';

    public static $PDF_SAVEFILE = 'F';

    public static $PDF_PORTRAIT = 'Portrait';

    public static $PDF_LANDSCAPE = 'Landscape';

    public function __construct(
        $orientation = 'Portrait', 
        $size = "A4", 
        $lang = "en", 
        $unicode = true, 
        $encoding = 'UTF-8', 
        $margins = false
    ){
        if (!self::isAvailable())
            throw new WKPDF_Exception('WKPDF static executable "'.htmlspecialchars($this->cmd,ENT_QUOTES).'" was not found.');
        $this->orientation = 
            $orientation == 'P' ? 
                self::$PDF_PORTRAIT :
                $orientation == 'L' ?
                self::$PDF_LANDSCAPE :
                $orientation;
        $this->size = $size;
        $this->lang = $lang;
        $this->encoding = $encoding;
        $this->margins = $margins;
    }

    public static function isAvailable ()
    {
        if (! self::$availabilitytested) {
            $version = array();
            exec(self::$cmd.self::getCPU()." --version", $version, $wkpdf);
            self::$available = $wkpdf == 0;
            self::$availabilitytested = true;
        }
        return self::$available;
    }
    
    private static function getCPU(){
        $os = getenv ("SERVER_SOFTWARE");
        $iswindows = strstr ($os, "Win32");
        if(!$iswindows && self::$cpu==''){
            if(`grep -i amd /proc/cpuinfo`!='')
                self::$cpu='-amd64';
            elseif(`grep -i intel /proc/cpuinfo`!='')
                self::$cpu='-i386';
            else 
                throw new WKPDF_Exception('WKPDF couldn\'t determine CPU ("'.`grep -i vendor_id /proc/cpuinfo`.'").');
        }
        return self::$cpu;
    }     

    public function setBase($base = false)    
    {
        $this->base = $base;
    }
    
    public function setBaseURL($baseurl = false)    
    {
        $this->baseurl = $baseurl;
    }

    public function setOrientation($orientation){
        $this->orientation = $orientation;
    }

    public function setSize($size){
        $this->size = $size;
    }
    
    public function createIndex($indextitle = 'Index', $index_levels = 6)
    {
        $this->toc = $indextitle;
        $this->tocxslfile = tempnam(sys_get_temp_dir(), 'PdfExport').".xsl";
        file_put_contents($this->tocxslfile,
'<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:outline="http://code.google.com/p/wkhtmltopdf/outline"
                xmlns="http://www.w3.org/1999/xhtml">
  <xsl:output doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
              doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
              indent="yes" />
  <xsl:template match="outline:outline">
    <html>
      <head>
        <title>'.$this->toc.'</title>
        <style>
          p.toctitle {
            text-align: center;
            font-size: 20px;
            font-family: arial;
          }
          div {border-bottom: 1px dashed rgb(200,200,200);}
          span {float: right;}
          li {list-style: none;}
          ul {
            font-size: 20px;
            font-family: arial;
          }
          ul {padding-left: 0em;}
          ul ul {padding-left: 1em;}
          a {text-decoration:none; color: black;}
        </style>
      </head>
      <body>
        <p class="toctitle">'.$this->toc.'</p>
        <ul><xsl:apply-templates select="outline:item/outline:item"/></ul>
      </body>
    </html>
  </xsl:template>
  <xsl:template match="outline:item">
    <li>
      <xsl:if test="@title!=\'\'">
        <div>
          <a>
            <xsl:if test="@link">
              <xsl:attribute name="href"><xsl:value-of select="@link"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="@backLink">
              <xsl:attribute name="name"><xsl:value-of select="@backLink"/></xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@title" />
          </a>
          <span> <xsl:value-of select="@page" /> </span>
        </div>
      </xsl:if>
      <ul>
        <xsl:apply-templates select="outline:item"/>
      </ul>
    </li>
  </xsl:template>
</xsl:stylesheet>'
        );
   }

    public function setCopies($count){
        $this->copies = $count;
    }

    public function setGrayscale($mode){
        $this->grayscale = $mode;
    }

    public function setTitle($text){
        $this->title = $text;
    }
 
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
 
    public function setAuthor($author)
    {
        $this->author = $author;
    }
 
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function setProtection($permissions = false, $userpass = false, $ownerpass = false)
    {
        $this->permissions = $permissions;
        $this->user_pass = $userpass;
        $this->owner_pass = $ownerpass;
    }
    
    public function setDefaultFont($defaultfont = false)    
    {
        $this->defaultfont = $defaultfont;
    }
    
    public function addStyleSheet($stylesheet = false)    
    {
        $this->stylesheets[] = $stylesheet;
    }
    
    public function addScript($script = false)    
    {
        $this->scripts[] = $script;
    }
    
    public function addInlineScript($script = false)    
    {
        $this->inlinescripts[] = $script;
    }
    
    public function addInlineStyles($styles = false)    
    {
        $this->inlinestyles[] = $styles;
    }

    public function writeHTML($html){
        $pagefile = tempnam(sys_get_temp_dir(), 'PdfExport').".html";
        if ($this->baseurl !== false) {
            $html = preg_replace_callback('/href="([^"]+)"/', array($this, 'absolutize'), $html);
        }
        $phtml = '<html><head><title>'.$this->title.'</title>';
        if ($this->base !== false) {
            $phtml .= '<base href="'.$this->base.'" />';
        }
        $phtml .= '<meta http-equiv="Content-Type" content="text/html;charset='.$this->encoding.'" />';
        $phtml .= '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">';
        if ($this->author !== false) {
            $phtml .= '<meta name="Author" content="'.$this->author.'">';
        }
        if ($this->keywords !== false) {
            $phtml .= '<meta name="Keywords" content="'.$this->keywords.'">';
        }
        if ($this->lang !== false) {
            $phtml .= '<meta name="Language" content="'.$this->lang.'">';
        }
        if ($this->subject !== false) {
            $phtml .= '<meta name="Description" content="'.$this->subject.'">';
        }
        foreach($this->stylesheets as $stylesheet) {
            $phtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" media="all" charset="utf-8" />';
        }
        foreach($this->scripts as $script) {
            $phtml .= '<script type="text/javascript" src="'.$script.'"></script>';
        }
        if (!empty($this->inlinescripts)) {
            $phtml .= '<script type="text/javascript">'."\n";
            foreach($this->inlinescripts as $script) {
                $phtml .= $script."\n";
            }
            $phtml .= '</script>';
        }
        if (!empty($this->inlinestyles)) {
            $phtml .= '<style type="text/css">'."\n";
            foreach($this->inlinestyles as $styles) {
                $phtml .= $styles."\n";
            }
            $phtml .= '</style>';
        }
        $phtml .= '</head><body>'.$html.'</body></html>';
        if (file_put_contents($pagefile, $phtml)) {
            $this->pagefiles[] = $pagefile;
        } else {
            throw new WKPDF_Exception("Generating PDF failed. Unable to use temporary space", 0);
        }
    }

    public function Output($file, $mode){
        $pdfname = tempnam(sys_get_temp_dir(), 'PdfExport').".pdf";
        $globaloptions = array(
            '--quiet', 
            '--orientation '.$this->orientation,
            '--page-size '.$this->size
        );
        if ($this->copies>1) {
            $globaloptions[] = '--copies '.$this->copies;
        }
        if ($this->grayscale !== false) {
            $globaloptions[] = '--grayscale';
        }
        if ($this->title != '') {
            $globaloptions[] = '--title "'.$this->title.'"';
        }
        if ($this->margins !== false) {
            $globaloptions[] = '-L "'.$this->margins[0].'mm"';
            $globaloptions[] = '-T "'.$this->margins[1].'mm"';
            $globaloptions[] = '-R "'.$this->margins[2].'mm"';
            $globaloptions[] = '-B "'.$this->margins[3].'mm"';
        }
        $tocoptions = array();
        if ($this->toc !== false) {
            $tocoptions[] = 'toc';
            $tocoptions[] = '--xsl-style-sheet "'.$this->tocxslfile.'"';
            $tocoptions[] = '--toc-header-text "'.$this->toc.'"';
        }
        $options = array(
            '--encoding "'.$this->encoding.'"',
            '--header-line',
            '--header-font-size 10',
            '--header-left "[doctitle]"',
            '--header-right "[date]"',
            '--footer-line',
            '--footer-font-size 10',
            '--footer-spacing 0',
            '--footer-center "Page [page]/[topage]"'
        );
        $pageslist = 'file:///'.implode('" "file:///', $this->pagefiles);
        $returnStatus = 0;
        system(
            self::$cmd.self::getCPU()." ".implode(" ", $globaloptions)." ".implode(" ", $options).' '.implode(" ", $tocoptions).' "'.$pageslist.'" "'.$pdfname.'"', 
            $returnStatus
        );
        if ($returnStatus == 1) {
            throw new WKPDF_Exception("Generating PDF failed. Return status was:" . $returnStatus, 0);
        }
        $pdf = file_get_contents($pdfname);
        switch($mode){
            case self::$PDF_DOWNLOAD:
                header('Content-Description: File Transfer');
                header('Cache-Control: public, must-revalidate, max-age=0');
                header('Pragma: public');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
                header('Content-Type: application/force-download');
                header('Content-Type: application/octet-stream', false);
                header('Content-Type: application/download', false);
                header('Content-Type: application/pdf', false);
                header('Content-Disposition: attachment; filename="'.basename($file).'";');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: '.strlen($pdf));
                echo $pdf;
                break;
            case self::$PDF_EMBEDDED:
                header('Content-Type: application/pdf');
                header('Cache-Control: public, must-revalidate, max-age=0');
                header('Pragma: public');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
                header('Content-Length: '.strlen($pdf));
                header('Content-Disposition: inline; filename="'.basename($file).'";');
                echo $pdf;
                break;
            case self::$PDF_ASSTRING:
                return $pdf;
                break;
            case self::$PDF_SAVEFILE:
                return file_put_contents($file, $pdf);
                break;
            default:
                throw new WKPDF_Exception('WKPDF invalid mode "'.htmlspecialchars($mode, ENT_QUOTES).'".');
        }
        // flush();
        unlink($pdfname);
        if ($this->toc !== false) {
            unlink($this->tocxslfile);
        }
        foreach($this->pagefiles as $pagefile) {
            unlink($pagefile);
        }
    }
    
    private function absolutize($matches) {
        $url = $matches[1];
        if (!preg_match("|^https?://|", $url)) {
          $url = $this->baseurl.$url;
        }
        return 'href="'.$url.'"';
    }
} 

?>