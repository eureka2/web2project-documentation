<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

class MagicWords 
{

  private $parser = null;

  private $namespaces = array(
    -2 => "Media",
    -1 => "Special",
    0 => "",
    1 => "Talk",
    2 => "User",
    3 => "User talk",
    4 => "Web2Project",
    5 => "Web2Project talk",
    6 => "File",
    7 => "File talk",
    8 => "Wiki",
    9 => "Wiki talk",
    10 => "Template",
    11 => "Template talk",
    12 => "Help",
    13 => "Help talk",
    14 => "Category",
    15 => "Category talk"
  );

  private $transformat = array(
    'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j',
    'W' => '%V', 
    'F' => '%B', 'm' => '%m', 'M' => '%b',
    'o' => '%G', 'Y' => '%Y', 'y' => '%y',
    'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S',
    'O' => '%z', 'T' => '%Z',
    'U' => '%s', 'r' => '%c'
  ); 

  public function __construct($parser) {
    $this->parser = $parser;
  }

  public function handle_CURRENTMONTH() {
    return date('m');
  }

  public function handle_CURRENTMONTHNAMEGEN() {
    return date('m');
  }

  public function handle_CURRENTMONTHNAME() {
    return date('F');
  }

  public function handle_CURRENTMONTHABBREV() {
    return date('D');
  }

  public function handle_CURRENTWEEK() {
    return date('W');
  }

  public function handle_CURRENTDAY() {
    return date('j');
  }

  public function handle_CURRENTDAY2() {
    return date('d');
  }

  public function handle_CURRENTDAYNAME() {
    return date('l');
  }

  public function handle_CURRENTYEAR() {
    return date('Y');
  }

  public function handle_CURRENTDOW() {
    return date('N');
  }

  public function handle_CURRENTTIME() {
    return date('H:i');
  }

  public function handle_CURRENTHOUR() {
    return date('H');
  }

  public function handle_CURRENTTIMESTAMP() {
    return time();
  }

  private function handle_local($format) {
    $format = str_replace(array_keys($this->transformat), array_values($this->transformat), $format);
    $lc = setlocale(LC_TIME, NULL);
    setlocale(LC_TIME, $AppUI->user_locale.".UTF-8", $AppUI->user_locale.".UTF8");
    $output = strftime($format);
    setlocale(LC_TIME, $lc);
    return $output;
  }

  public function handle_LOCALMONTH() {
    return $this->handle_local('m');
  }

  public function handle_LOCALMONTHNAMEGEN() {
    return $this->handle_local('m');
  }

  public function handle_LOCALMONTHNAME() {
    return $this->handle_local('F');
  }

  public function handle_LOCALMONTHABBREV() {
    return $this->handle_local('D');
  }

  public function handle_LOCALWEEK() {
    return $this->handle_local('W');
  }

  public function handle_LOCALDAY() {
    return $this->handle_local('j');
  }

  public function handle_LOCALDAY2() {
    return $this->handle_local('d');
  }

  public function handle_LOCALDAYNAME() {
    return $this->handle_local('l');
  }

  public function handle_LOCALYEAR() {
    return $this->handle_local('Y');
  }

  public function handle_LOCALDOW() {
    return $this->handle_local('N');
  }

  public function handle_LOCALTIME() {
    return $this->handle_local('H:i');
  }

  public function handle_LOCALHOUR() {
    return $this->handle_local('H');
  }

  public function handle_LOCALTIMESTAMP() {
    return time();
  }

  public function handle_NUMBEROFARTICLES() {
    return 0;
  }

  public function handle_FULLPAGENAME() {
    return $this->parser->wiki->getNamespace().":".$this->parser->wiki->pagename();
  }

  public function handle_FULLPAGENAMEE() {
    return urlencode ($this->parser->wiki->getNamespace().":".$this->parser->wiki->pagename());
  }

  public function handle_PAGENAME() {
    return $this->parser->wiki->pagename();
  }

  public function handle_PAGENAMEE() {
    return urlencode ($this->parser->wiki->pagename());
  }

  public function handle_NAMESPACE() {
    return $this->parser->wiki->getNamespace();
  }

  public function handle_SITENAME() {
    global $_SERVER;
    return $_SERVER['HTTP_HOST'];
  }

  public function handle_CONTENTLANGUAGE() {
    global $AppUI;
    return $AppUI->user_locale;
  }

  public function handle_CONTENTLANG() {
    return $this->handle_CONTENTLANGUAGE();
  }

  public function handle_SERVERNAME() {
    global $_SERVER;
    return $_SERVER['SERVER_NAME'];
  }

  public function handle_SERVER() {
    global $_SERVER;
    return (isset($_SERVER['HTTPS'])? 'https':'http').'://'.$_SERVER["HTTP_HOST"];
  }

  public function handle_SCRIPTPATH() {
    global $_SERVER;
    return dirname($_SERVER['SCRIPT_NAME']);
  }

  public function handle_REVISIONYEAR() {
    return date('Y', strtotime($this->parser->wiki->pagedate()));
  }

  public function handle_REVISIONMONTH() {
    return date('m', strtotime($this->parser->wiki->pagedate()));
  }

  public function handle_REVISIONDAY() {
    return date('j', strtotime($this->parser->wiki->pagedate()));
  }

  public function handle_REVISIONDAY2() {
    return date('d', strtotime($this->parser->wiki->pagedate()));
  }

  public function handle_REVISIONTIMESTAMP() {
    return strtotime($this->parser->wiki->pagedate());
  }

  public function handle_if($input) {
    $values = explode("|", $input);
    $teststring = trim($values[0]);
    if ($teststring) {
      return sizeof($values) > 1 ? trim($values[1]) : "";
    }
    return sizeof($values) > 2 ? trim($values[2]) : "";
  }

  public function handle_expr($input){
    $input = 'return '.$input.';';
    $code = $input;
    $braces=0;
    $inString=0;
    foreach (token_get_all('<?php ' . $code) as $token) {
      if (is_array($token)) {
        switch ($token[0]) {
          case T_CURLY_OPEN:
          case T_DOLLAR_OPEN_CURLY_BRACES:
          case T_START_HEREDOC: ++$inString; break;
          case T_END_HEREDOC:   --$inString; break;
        }
      }
      else if ($inString & 1) {
        switch ($token) {
          case '`': case '\'':
          case '"': --$inString; break;
        }
      } 
      else {
        switch ($token) {
          case '`': case '\'':
          case '"': ++$inString; break;
          case '{': ++$braces; break;
          case '}':
          if ($inString) {
            --$inString;
          }
          else {
            --$braces;
            if ($braces < 0) break 2;
          }
          break;
        }
      }
    }
    $inString = @ini_set('log_errors', false);
    $token = @ini_set('display_errors', true);
    ob_start();
    $braces || $code = "if(0){{$code}\n}";
    if (eval($code) === false) {
      if ($braces) {
        $braces = PHP_INT_MAX;
      }
      else {
        false !== strpos($code,CR) && $code = strtr(str_replace(CRLF,LF,$code),CR,LF);
        $braces = substr_count($code,LF);
      }
      $code = ob_get_clean();
      $code = strip_tags($code);
      if (preg_match("'syntax error, (.+) in .+ on line \d+)$'s", $code, $code)) {
        $code[2] = (int) $code[2];
        $code = $code[2] <= $braces
              ? array($code[1], $code[2])
              : array('unexpected $end' . substr($code[1], 14), $braces);
      }
      else $code = array('syntax error', 0);
    }
    else {
      ob_end_clean();
      $code = false;
    }
    @ini_set('display_errors', $token);
    @ini_set('log_errors', $inString);
    return $code === false ? eval($input) : '<strong class="error">'.$code[0].' on line '.$code[1].'</strong>';
  }

  public function handle_iferror($input) {
    $values = explode("|", $input);
    if ( preg_match( '/<(?:strong|span|p|div)\s(?:[^\s>]*\s+)*?class="(?:[^"\s>]*\s+)*?error(?:\s[^">]*)?"/', $values[0]) ) {
      return sizeof($values) > 1 ? trim($values[1]) : "";
    }
    return sizeof($values) > 2 ? trim($values[2]) : "";
  }

  public function handle_ifexpr($input) {
    $values = explode("|", $input);
    $teststring = trim($values[0]);
    if ($this->handle_expr($teststring)) {
      return sizeof($values) > 1 ? trim($values[1]) : "";
    }
    return sizeof($values) > 2 ? trim($values[2]) : "";
  }

  public function handle_ifeq($input) {
    $values = explode("|", $input);
    $string1 = trim($values[0]);
    $string2 = trim($values[1]);
    if ($string1 == $string2) {
      return sizeof($values) > 2 ? trim($values[2]) : "";
    }
    return sizeof($values) > 3 ? trim($values[3]) : "";
  }

  public function handle_ifexist($input) {
    $values = explode("|", $input);
    $page = trim($values[0]);
    if ($this->parser->wiki->exists($page) !== false) {
      return sizeof($values) > 1 ? trim($values[1]) : "";
    }
    return sizeof($values) > 2 ? trim($values[2]) : "";
  }

  public function handle_rel2abs($input) {
    list($path, $basepath) = explode("|", $input);
    if (!$basepath) {
      !$basepath = $this->parser->wiki->getBasePath();
    }
    return preg_replace(array('#//#', '#/./#', '#[^/]+/../#'), array('/', '/', ''), $basepath.'/'.$path);
  }

  public function handle_switch($input) {
    $values = explode("|", $input);
    $comparisonstring = trim(array_shift($values));
    $ncase = count($values);
    $default = "";
    $lastresult = "";
    for ($i = 0; $i < $ncase; $i++) {
      list($case, $result) = explode("=", $value[$i]);
      if ($case == "#default") {
        $default = $lastresult = $result;
      }
      elseif (!$result && $i == $ncase - 1) {
        $default = $lastresult = $case;
      }
      elseif ($case == $comparisonstring) {
        return $result ? $result : $lastresult;
      }
      elseif ($result) {
        $lastresult = $result;
      }
    }
    return $default;
  }

  public function handle_time($input) {
    global $AppUI;
    $values = explode("|", $input);
    $format = trim($values[0]);
    $tz = date_default_timezone_get();
    date_default_timezone_set($AppUI->getPref('TIMEZONE'));
    $time = sizeof($values) > 1 ? strtotime(trim($values[1])) : time();
    $output = date($format, $time);
    date_default_timezone_set($tz);
    return $output;
  }

  public function handle_timel($input) {
    global $AppUI;
    $values = explode("|", $input);
    $format = str_replace(array_keys($this->transformat), array_values($this->transformat), trim($values[0]));
    $tz = date_default_timezone_get();
    date_default_timezone_set($AppUI->getPref('TIMEZONE'));
    $lc = setlocale(LC_TIME, NULL);
    setlocale(LC_TIME, $AppUI->getPref('LOCALE').".UTF-8", $AppUI->getPref('LOCALE').".UTF8");
    $time = sizeof($values) > 1 ? strtotime(trim($values[1])) : time();
    $output = strftime($format, $time);
    date_default_timezone_set($tz);
    setlocale(LC_TIME, $lc);
    return $output;
  }

  public function handle_titleparts($input) {
    $values = explode("|", $input);
    $title = trim($values[0]);
    $parts = intval(sizeof($values) > 1 ? trim($values[1]) : 0);
    $offset = intval(sizeof($values) > 2 ? trim($values[2]) : 1);
    $bits = explode( '/', $title, 25 );
    if ( count( $bits ) <= 0 ) {
       return $title;
    }
    else {
      if ($offset > 0) {
        --$offset;
      }
      if ($parts == 0) {
        return implode('/', array_slice($bits, $offset));
      }
      else {
        return implode('/', array_slice($bits, $offset, $parts));
      }
    }
  }

  public function handle_ns($input) {
    global $AppUI;
    $ns = intval(trim($input));
    return ($ns != 0 && isset($this->namespaces[$ns])) ? $AppUI->_($this->namespaces[$ns]) : "";
  }

  public function handle_nse($input) {
    return urlencode($this->handle_ns($input));
  }

  public function handle_lc($input) {
    return strtolower(trim($input));
  }

  public function handle_lcfirst($input) {
    return lcfirst(trim($input));
  }

  public function handle_uc($input) {
    return strtoupper(trim($input));
  }

  public function handle_ucfirst($input) {
    return ucfirst(trim($input));
  }

  public function handle_formatnum($input) {
    global $AppUI;
    $lc = setlocale(LC_ALL, NULL);
    setlocale(LC_ALL, $AppUI->user_locale.".UTF-8", $AppUI->user_locale.".UTF8");
    $locale_info = localeconv();
    $frac_digits = $locale_info['frac_digits'];
    $decimal_point = $locale_info['decimal_point'];
    $thousands_sep = str_replace("\xa0", " ", $locale_info['thousands_sep']);
    setlocale(LC_ALL, $lc);
    return number_format(trim($input), $frac_digits, $decimal_point, $thousands_sep);
  }

  public function handle_formatdate($input) {
    global $AppUI;
    $values = explode("|", $input);
    $format = $AppUI->getPref('FULLDATEFORMAT');
    if (!$format) {
      $format = sizeof($values) > 1 ? trim($values[1]) : "r";
    }
    else {
      $format = str_replace(array_values($this->transformat), array_keys($this->transformat), $format);
    }
    $tz = date_default_timezone_get();
    date_default_timezone_set($AppUI->getPref('TIMEZONE'));
    $time = strtotime(trim($values[0]));
    $output = date($format, $time);
    date_default_timezone_set($tz);
    return $output;
  }

  public function handle_dateformat($input) {
    return $this->handle_formatdate($input);
  }

  /**
   * Unicode-safe str_pad with the restriction that $length is forced to be <= 500
    */
  private function pad( $string, $length, $padding='0', $paddingleft=false) {
    $lengthOfPadding = mb_strlen( $padding );
    if ( $lengthOfPadding == 0 ) return $string;
    # The remaining length to add counts down to 0 as padding is added
    $length = min( $length, 500 ) - mb_strlen( $string );
    # $finalPadding is just $padding repeated enough times so that
    # mb_strlen( $string ) + mb_strlen( $finalPadding ) == $length
    $finalPadding = '';
    while ( $length > 0 ) {
      # If $length < $lengthofPadding, truncate $padding so we get the
      # exact length desired.
      $finalPadding .= mb_substr( $padding, 0, $length );
      $length -= $lengthOfPadding;
    }
    if ($paddingleft) {
      return $finalPadding . $string;
    } else {
      return $string . $finalPadding;
    }
  }

  public function handle_padleft($input) {
    list($string, $length, $padding) = explode("|", $input);
    if (!$padding) $padding = '0';
    return $this->pad($string, intval($length), $padding, true );
  }

  public function handle_padright($input) {
    list($string, $length, $padding) = explode("|", $input);
    if (!$padding) $padding = '0';
    return $this->pad($string, intval($length), $padding);
  }

  public function handle_plural($input) {
    $values = explode("|", $input);
    $teststring = trim($values[0]);
    if (intval($teststring) <= 1) {
      return sizeof($values) > 1 ? trim($values[1]) : "";
    }
    return sizeof($values) > 2 ? trim($values[2]) : "";
  }

  public function handle_urlencode($input) {
    return urlencode($input);
  }

  public function anchorencode($input) {
    $anchor = urlencode($input);
    $anchor = strtr($anchor, array( '%' => '.', '+' => '_'));
    $anchor = str_replace('.3A', ':', $anchor);
    return $anchor;
  }

  public function handle_language($input) {
    return isset($this->parser->languageNames[$input]) ? $this->parser->languageNames[$input] : "";
  }

  public function handle_int($input) {
    global $AppUI;
    return $AppUI->_(trim($input));
  }

}