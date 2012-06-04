<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

class PRINCE_Exception extends Exception {}
      
class PRINCE {
    private static $cmd = 'prince';
    private $base = false;
    private $baseurl = false;
    private $pages = array();
    private $orientation = 'portrait';
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
    private $sections = array();
    private $sectionnumber = 0;
    private $mintoclevel = 6;
    
    public static $PDF_DOWNLOAD = 'D';

    public static $PDF_ASSTRING = 'S';

    public static $PDF_EMBEDDED = 'I';

    public static $PDF_SAVEFILE = 'F';

    public static $PDF_PORTRAIT = 'portrait';

    public static $PDF_LANDSCAPE = 'landscape';

    public function __construct(
        $orientation = 'portrait', 
        $size = "A4", 
        $lang = "en", 
        $unicode = true, 
        $encoding = 'UTF-8', 
        $margins = false
    ){
        if (!self::isAvailable())
            throw new PRINCE_Exception('prince static executable "'.htmlspecialchars($this->cmd,ENT_QUOTES).'" was not found.');
        if ($orientation == 'P')  
            $this->orientation = self::$PDF_PORTRAIT;
        elseif ($orientation == 'L')
            $this->orientation = self::$PDF_LANDSCAPE;
        else
            $this->orientation = $orientation;
        $this->size = $size;
        $this->lang = $lang;
        $this->encoding = $encoding;
        $this->margins = $margins;
    }

    public static function isAvailable ()
    {
        if (! self::$availabilitytested) {
            $version = array();
            exec(self::$cmd." --version", $version, $prince);
            self::$available = $prince == 0;
            self::$availabilitytested = true;
        }
        return self::$available;
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

    public function writeHTML($html)
    {
        $this->pages[] = $html;
    }

    private function writeFile($html)
    {
        $pagefile = tempnam(sys_get_temp_dir(), 'PdfExport').".html";
        $phtml = '<html><head><title>'.$this->title.'</title>';
        if ($this->base !== false) {
            $phtml .= '<base href="'.$this->base.'" />';
        }
        $phtml .= '<meta http-equiv="Content-Type" content="text/html;charset='.$this->encoding.'" />';
        $phtml .= '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">';
        if ($this->author !== false) {
            $phtml .= '<meta name="Author" content="'.$this->author.'">';
        }
        if ($this->setKeywords !== false) {
            $phtml .= '<meta name="Keywords" content="'.$this->setKeywords.'">';
        }
        if ($this->lang !== false) {
            $phtml .= '<meta name="Language" content="'.$this->lang.'">';
        }
        if ($this->subject !== false) {
            $phtml .= '<meta name="Description" content="'.$this->subject.'">';
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
        if (!file_put_contents($pagefile, $phtml)) {
            throw new PRINCE_Exception("Generating PDF failed. Unable to use temporary space", 0);
        }
        return $pagefile;
    }

    public function Output($file, $mode)
    {
        list($toc, $html) = $this->createTOC(
            '<div class="prince-page">'.implode(
                "</div>\n".'<div class="prince-page">',
                $this->pages
            )."</div>"
        );
        if ($this->baseurl !== false) {
            $html = preg_replace_callback('/href="([^"]+)"/', array($this, 'absolutizeHref'), $html);
            $html = preg_replace_callback('/src="([^"]+)"/', array($this, 'absolutizeSrc'), $html);
        }
        $style = array(
            '@page { size: '.$this->size.' '.$this->orientation.' }',
            '.prince-page { page-break-before: always; padding: 0; margin: 0; }'
        );
        if ($this->margins !== false) {
            $style[] = '@page { margin: '.$this->margins[1].'mm '.$this->margins[2].'mm '.$this->margins[3].'mm '.$this->margins[0].'mm }';
        }
        if ($this->toc !== false) {
            $style[] = 'h1 { prince-bookmark-level: 1 }';
            $style[] = 'h2 { prince-bookmark-level: 2 }';
            $style[] = 'h3 { prince-bookmark-level: 3 }';
            $style[] = 'h4 { prince-bookmark-level: 4 }';
            $style[] = 'h5 { prince-bookmark-level: 5 }';
            $style[] = 'h6 { prince-bookmark-level: 6 }';
            $style[] = '@page table-of-contents { @top { content: "'.$this->toc.'" } }';
            $style[] = '#toc::before { content: "'.$this->toc.'"; }';
            $html = $toc.$html;
        }
        if ($this->title !== false) {
            $style[] = '@page { @top-left {	content: "'.$this->title.'" } }';
        }
        $style[] = '@page { @bottom { font-size: 8pt; content: "Page " counter(page) "/" counter(pages) } }';
        $pagefile = $this->writeFile($html);
        $pageslist = '"file:///'.$pagefile.'"';
        $stylefile = tempnam(sys_get_temp_dir(), 'PdfExport').".css";
        file_put_contents($stylefile, implode("\n", $style));
        $pdfname = tempnam(sys_get_temp_dir(), 'PdfExport').".pdf";
        $options = array(
            '--silent', 
            '--input=html',
            '--javascript',
            '--style "'.dirname(__FILE__).'/toc.css"',
            '--style "'.$stylefile.'"'
        );
        if ($this->baseurl !== false) {
            $options[] = '--baseurl "'.$this->baseurl.'"';
        }
        if ($this->base !== false) {
            $options[] = '--fileroot "'.$this->base.'"';
        }
        foreach($this->stylesheets as $stylesheet) {
            $options[] = '-s "'.$stylesheet.'"';
        }
        if ($this->permissions !== false && !empty($this->permissions)) {
            $options[] = implode(" ", $this->permissions).' --encrypt';
        }
        if ($this->owner_pass !== false) {
            $options[] = '--owner-password "'.$this->owner_pass.'"';
        }
        if ($this->user_pass !== false) {
            $options[] = '--user-password "'.$this->user_pass.'"';
        }
        $returnStatus = 0;
        system(
            self::$cmd." ".implode(" ", $options).' '.$pageslist.' -o "'.$pdfname.'"', 
            $returnStatus
        );
        if ($returnStatus == 1) {
            throw new PRINCE_Exception("Generating PDF failed. Return status was:" . $returnStatus, 0);
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
                throw new PRINCE_Exception('prince invalid mode "'.htmlspecialchars($mode, ENT_QUOTES).'".');
        }
        unlink($pdfname);
        unlink($stylefile);
        //unlink($pagefile);
    }

    private function createTOC($content)
    {
        $this->sections = array();
        $this->sectionnumber = 1;
        $this->mintoclevel = 6;
        $content = preg_replace_callback("/<h([1-6])(\s?[^<>]*)>([^<]+)<\/h[1-6]>/i", array(&$this, 'tocitem'), $content);
        $prevlevel = 0;
        $number = array(0, 0, 0, 0, 0, 0);
        $toc = '<ol id="toc">'."\n";
        $depth = 0;
        foreach($this->sections as $k => $section) {
            $level = $section[0];
            $id = $section[1];
            $heading = $section[2];
            if ($level > $prevlevel) {
                if ($k > 0) {
                    $toc .= '<ol>'."\n";
                    $depth++;
                }
            } elseif ($level < $prevlevel) {
                $toc .= '</li>'."\n";
                $toc .= '</ol>'."\n";
                $toc .= '</li>'."\n";
                $depth--;
            } else {
                $toc .= '</li>'."\n";
            }
            $toc .= '<li><a href="#'.$id.'">'.$this->tocnumber($number, $level).' '.$heading.'</a>';
            $prevlevel = $level;
        }
        $toc .=  str_repeat("</li>\n</ol>\n", ($depth + 1));
        return array($toc, $content);
    }

    private function tocitem($matches)
    {
        $id = 'tocsection'.$this->sectionnumber++;
        if ($matches[1] < $this->mintoclevel) $this->mintoclevel = $matches[1];
        $this->sections[] = array($matches[1], $id, $matches[3]);
        $item = '<h'.$matches[1].' id="'.$id.'"'.$matches[2].'>'.$matches[3].'</h'.$matches[1].'>';
        return $item;
   }

    private function tocnumber(&$number, $level)
    {
        $output = array();
        $level -= $this->mintoclevel;
        $number[$level]++;
        for ($i = 0; $i < 6; $i++) {
            if ($i <= $level) {
                $output[] = $number[$i];
            } elseif ($i > $level) {
                $number[$i] = 0;
            }
        }
        return implode(".", $output);
    }
    
    private function absolutizeHref($matches)
    {
        $url = $matches[1];
        if (!preg_match("|^https?://|", $url)) {
          $url = $this->baseurl.$url;
        }
        return 'href="'.$url.'"';
    }
    
    private function absolutizeSrc($matches)
    {
        $url = $matches[1];
        if (!preg_match("|^https?://|", $url)) {
          $url = $this->baseurl.$url;
        }
        return 'src="'.$url.'"';
    }
} 

?>