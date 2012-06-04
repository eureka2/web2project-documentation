<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

class HTMLDOC_Exception extends Exception {}

class HTMLDOC {
    private $orientation;
    private $base = false;
    private $baseurl = false;
    private $size;
    private $lang;
    private $unicode;
    private $encoding;
    private $marges;
    private $pages;
    private $iswindows;
    private $index_title = false;
    private $index_levels;
    private $mode = false;
    private $layout = false;
    private $default_font = false;
    private $title = false;
    private $subject = false;
    private $author = false;
    private $keywords = false;
    private $permissions = false;
    private $user_pass = false;
    private $owner_pass = false;
    private static $available = false;
    private static $availabilitytested = false;
    
    public function __construct(
        $orientation = "P", 
        $size = "Letter", 
        $lang = "fr", 
        $unicode = true, 
        $encoding = 'UTF-8', 
        $marges = array(5, 5, 5, 8))
    {
        $this->orientation = $orientation;
        $this->size = $size;
        $this->lang = $lang;
        $this->unicode = $unicode;
        $this->encoding = $encoding;
        $this->marges = $marges;
        $os = getenv ("SERVER_SOFTWARE");
        $this->iswindows = strstr ($os, "Win32");
    }

    public static function isAvailable ()
    {
        if (! self::$availabilitytested) {
            $version = array();
            exec("htmldoc --version", $version, $htmldoc);
            self::$available =  $htmldoc == 0;
            self::$availabilitytested = true;
        }
        return self::$available;
    }
    
    public function setDisplayMode($mode = false, $layout = false)
    {
        $this->mode = $mode;
        $this->layout = $layout;
   }
 
    public function setBase($base = false)    
    {
        $this->base = $base;
    }
    
    public function setBaseURL($baseurl = false)    
    {
        $this->baseurl = $baseurl;
    }

    public function setTitle($title)
    {
        $this->title = 
            ($this->encoding == 'UTF-8') ? 
            utf8_decode($title) :
            $title;
    }
 
    public function setSubject($subject)
    {
        $this->subject = 
            ($this->encoding == 'UTF-8') ? 
            utf8_decode($subject) :
            $subject;
    }
 
    public function setAuthor($author)
    {
        $this->author = 
            ($this->encoding == 'UTF-8') ? 
            utf8_decode($author) :
            $author;
    }
 
    public function setKeywords($keywords)
    {
        $this->keywords = 
            ($this->encoding == 'UTF-8') ? 
            utf8_decode($keywords) :
            $keywords;
    }

    public function setProtection($permissions = false, $user_pass = false, $owner_pass = false)
    {
        $this->permissions = $permissions;
        $this->user_pass = $user_pass;
        $this->owner_pass = $owner_pass;
    }
    
    public function setDefaultFont($default_font = false)    
    {
        $this->default_font = $default_font;
    }
    
    public function createIndex($index_title = 'Index', $index_levels = 6)
    {
        $this->index_title = 
            ($this->encoding == 'UTF-8') ? 
            utf8_decode($index_title) : 
            $index_title;
        $this->index_levels = $index_levels;
    }

    private function write ($content)
    {
        $tempfile = tempnam(sys_get_temp_dir(), 'PdfExport');
        $handle = fopen($tempfile,'w');
        if($handle === FALSE){
            throw new HTMLDOC_Exception("Failed opening temporary HTML file to \"$tempfile\" failed", 0);
        }
        fwrite($handle, $content);
        fseek($handle, 0);
        fclose($handle);
        return $tempfile;
    }

    private function save ($bhtml )
    {
        if ($this->encoding == 'UTF-8') {
            // Hack to thread the EUR sign correctly
            $bhtml = str_replace(chr(0xE2) . chr(0x82) . chr(0xAC), chr(0xA4), $bhtml);
            $bhtml = utf8_decode($bhtml);
        }
        $html = 
'<html>
<head>
<title>'.$this->title.'</title>   
</head>
<body>
    '.$bhtml.'
</body>
</html>';
        return $this->write($html);
    }

    private function outputPDF($name = '', $attach = false)
    {
        $returnStatus = 0;
        $pagestring = "";
        $html = implode("<!-- PAGE BREAK -->", $this->pages);
        if ($this->base !== false) {
            $html = preg_replace_callback('/src="([^"]+)"/', array($this, 'absolutizeSrc'), $html);
        }
        $pagefile = $this->save ($html);
        if ($pagefile == null)
            return;
        if ($this->iswindows) {
            $pagestring .= "\"".$pagefile."\" ";
        } else {
            $pagestring .= $pagefile . " ";
        }
        $options = array(
            '-t pdf14', 
            '--book', 
            '--numbered', 
            '--footer t./', 
            '--color',
            '--linkcolor #0000FF',
            '--quiet',
            '--jpeg'
        );
        $options[] = 
            ($this->encoding == 'UTF-8') ? 
            "--charset iso-8859-15" : 
            "--charset ".strtolower($this->encoding);
        $options[] = 
            ($this->index_title === false) ? 
            '--no-toc' : 
            '--toctitle "'.$this->index_title.'" --toclevels '.$this->index_levels;
        $options[] = '--size '.$this->size;
        $options[] = 
            ($this->orientation == 'L') ? 
            "--landscape --browserwidth 1200" :
            "--portrait";
        if ($this->mode !== false) {
            $options[] = '--pagemode '.$this->mode;
        }
        if ($this->layout !== false) {
            $options[] = '--pagelayout '.$this->layout;
        }
        if ($this->default_font !== false) {
            $options[] = '--bodyfont '.$this->default_font;
        }
        if ($this->permissions !== false && !empty($this->permissions)) {
            $options[] = '--permissions '.implode(",", $this->permissions).' --encryption';
        }
        if ($this->owner_pass !== false) {
            $options[] = '--owner-password '.$this->owner_pass;
        }
        if ($this->user_pass !== false) {
            $options[] = '--user-password '.$this->user_pass;
        }
        putenv("HTMLDOC_NOCGI=1");
        # Write the content type to the client...
        header("Content-Type: application/pdf");
        if ($attach && $name != '') {
            header(sprintf('Content-Disposition: attachment; filename="%s.pdf"', $name));
        }
        # Run HTMLDOC to provide the PDF file to the user...
        passthru("htmldoc ".implode(" ", $options)." ".$pagestring, $returnStatus);
        flush();
        unlink ($pagefile);
        if ($returnStatus == 1) {
            throw new HTMLDOC_Exception("Generating PDF failed. Return status was:" . $returnStatus, 0);
        }
    }

    public function Output($name = '', $dest = false)
    {
        // complete parameters
        if ($dest===false) {
            $dest = 'I';
        } elseif ($dest===true) {
            $dest = 'S';
        } elseif ($dest==='') {
            $dest = 'I';
        } elseif ($name=='') { 
            $name='document.pdf';
        }
        // clean up the destination
        $dest = strtoupper($dest);
        if (!in_array($dest, array('I', 'D', 'F', 'S', 'FI','FD')))
            $dest = 'I';
        // the name must be a PDF name
        if (strtolower(substr($name, -4))!='.pdf') {
            throw new HTMLDOC_Exception('The output document name "'.$name.'" is not a PDF name');
        }

        $this->outputPDF($name);
        return;
    }
    
    public function writeHTML($html)
    {
        $this->pages[] = $html;
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
          $url = $this->base.$url;
        }
        return 'src="'.$url.'"';
    }

}

?>
