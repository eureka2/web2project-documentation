<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

class W2PMPDF_Exception extends Exception {}
      
class W2PMPDF {
    private $base = false;
    private $baseurl = false;
    private $pages = array();
    private $orientation = 'P';
    private $margins = false;
    private $lang = 'eng';
    private $size = 'A4';
    private $encoding = false;
    private $toc = false;
    private $toclevel = 0;
    private $headinglevel = 0;
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
    private $defaultfontsize = 9;
    private $stylesheets = array();
    private $scripts = array();
    private $inlinescripts = array();
    private $inlinestyles = array();
    
    public static $PDF_DOWNLOAD = 'D';

    public static $PDF_ASSTRING = 'S';

    public static $PDF_EMBEDDED = 'I';

    public static $PDF_SAVEFILE = 'F';

    public static $PDF_PORTRAIT = 'P';

    public static $PDF_LANDSCAPE = 'L';

    public function __construct(
        $orientation = 'portrait', 
        $size = "A4", 
        $lang = "en", 
        $unicode = true, 
        $encoding = 'UTF-8', 
        $margins = array(5, 5, 8, 8)
    )
    {
        $this->orientation = $orientation;
        $this->size = $size;
        $this->lang = $lang;
        $this->encoding = $encoding;
        $this->margins = $margins;
    }

    public function setBase($base = false)    
    {
        $this->base = $base;
    }
    
    public function setBaseURL($baseurl = false)    
    {
        $this->baseurl = $baseurl;
    }

    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }
    
    public function createIndex($indextitle = 'Index', $index_levels = 6)
    {
        $this->toc = $indextitle;
    }

    public function setCopies($count)
    {
        $this->copies = $count;
    }

    public function setGrayscale($mode)
    {
        $this->grayscale = $mode;
    }

    public function setTitle($text)
    {
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
    
    public function setDefaultFont($defaultfont = false, $defaultfontsize = 9)    
    {
        $this->defaultfont = $defaultfont;
        $this->defaultfontsize = $defaultfontsize;
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

    public function Output($file, $mode)
    {
        require_once('mpdf.php');
        $pdf = new mPDF (
            $this->lang, 
            $this->size,
            0,
            '', 
            $this->margins[0],
            $this->margins[2],
            $this->margins[1],
            $this->margins[3],
            9,
            9,
            $this->orientation
        );
        $pdf->SetCreator("Web2Project documentation");
        if ($this->author !== false) {
            $pdf->SetAuthor($this->author);
        }
        if ($this->title != '') {
            $pdf->SetTitle($this->title);
        }
        if ($this->subject !== false) {
            $pdf->SetSubject($this->subject);
        }
        if ($this->keywords !== false) {
            $pdf->SetKeywords($this->keywords);
        }
        $pdf->setAutoTopMargin = $pdf->setAutoBottomMargin = 'pad';
        if ($this->defaultfont !== false) {
            $pdf->SetDefaultFont($this->defaultfont);
            $pdf->SetDefaultFontSize($this->defaultfontsize);
        }
        $html = implode('<pagebreak>', $this->pages);
        if ($this->toc !== false) {
            $html = preg_replace_callback(
                '/<h([1-6])(\s?[^<>]*)>([^<]+)<\/h([1-6])>/i',
                array(&$this, 'toc_entry'),
                $html
            );
        }
        if ($this->baseurl !== false) {
            $pdf->setBasePath($this->baseurl);
        }
        if (!empty($this->inlinestyles)) {
            $phtml = '<style type="text/css">'."\n";
            foreach($this->inlinestyles as $styles) {
                $phtml .= $styles."\n";
            }
            $phtml .= '</style>';
            $html = $phtml.$html;
        }
        if (!empty($this->stylesheets)) {
            $phtml = '';
            foreach($this->stylesheets as $stylesheet) {
                $phtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" media="all" charset="'.$this->encoding.'" />';
            }
            $html = $phtml.$html;
        }
        if ($this->toc !== false) {
            $pdf->DefHTMLHeaderByName(
                'tochead',
                '<table style="width: 100%; border-bottom:1x solid gray"> 
                    <tr> 
                        <td style="width:80%; text-align:left"> 
                            <strong>'.$this->title.'</strong>
                        </td> 
                        <td style="width: 20%; text-align: right; font-size: 0.8.em;"> 
                            {DATE j-m-Y} 
                        </td> 
                    </tr> 
                </table>
                <table style="width: 100%;"> 
                    <tr> 
                        <td style="width: 100%; text-align: center"> 
                            <strong>'.$this->toc.'</strong>
                        </td> 
                    </tr> 
                </table>'
            );
            $pdf->DefHTMLFooterByName(
                'tocfoot',
                '<div style="width: 100%; text-align:center;">
                    &nbsp;
                </div>'
            );
            $pdf->TOCpagebreakByArray(array(
                'toc_bookmarkText' => $this->toc,
                'toc_ohname' => 'html_tochead',
                'toc_ofname' => 'html_tocfoot',
                'toc_ohvalue' => 1,
                'toc_ofvalue' => 1,
                'TOCuseLinking' => 1
            ));
        }
        $pdf->SetHTMLHeader('
        <table style="width: 100%; border-bottom:1x solid gray"> 
            <tr> 
                <td style="width:80%; text-align:left"> 
                    <strong>'.$this->title.'</strong>
                </td> 
                <td style="width:20%; text-align:right; font-size:0.8.em;"> 
                    {DATE j-m-Y} 
                </td> 
            </tr> 
        </table>', '', true
        );
        $pdf->SetHTMLFooter('
        <div style="width:100%; text-align:center; border-top:1x solid gray; font-size:0.8.em;">
            Page {PAGENO}/{nbpg}
        </div>'
        );
        $pdf->WriteHTML($html); 
        if ($this->permissions !== false) {
            $pdf->SetProtection (
                $this->permissions,
                $this->user_pass ? $this->user_pass : '',
                $this->owner_pass ? $this->owner_pass : ''
            );
        }
        switch($mode){
            case self::$PDF_DOWNLOAD:
                $pdf->Output($file, self::$PDF_DOWNLOAD);
                break;
            case self::$PDF_EMBEDDED:
                $pdf->Output($file, self::$PDF_EMBEDDED);
                break;
            case self::$PDF_ASSTRING:
                return $pdf->Output($file, self::$PDF_ASSTRING);
                break;
            case self::$PDF_SAVEFILE:
                return $pdf->Output($file, self::$PDF_SAVEFILE);
                break;
            default:
                throw new W2PMPDF_Exception('TCPDF invalid mode "'.htmlspecialchars($mode, ENT_QUOTES).'".');
        }
    }

    private function toc_entry($input)
    {
        $level = $input[1];
        if ($level == $this->headinglevel) {
            $level = $this->toclevel;
        } elseif ($level > $this->toclevel + 1) {
            $level = $this->toclevel + 1;
        }
        $this->headinglevel = $input[1];
        $this->toclevel = $level;
        return '<h'.$level.$input[2].'><tocentry content="'.$input[3].'" level="'.($level - 1).'" /><bookmark content="'.$input[3].'" level="'.($level - 1).'" />'.$input[3].'</h'.$level.'>';
    }
} 

?>