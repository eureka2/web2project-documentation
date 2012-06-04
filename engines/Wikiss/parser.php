<?php /* $Id$ $URL$ */

class Wikiss {

	public function __construct($wiki) {
	}

	public function __destruct() {
	}

	public function parse($text, $title = "", $date = null, $parameters = array()) {
		global $titres;
		global $matches_code;
		$text = str_replace("\r",'',$text);
		$text = preg_replace('/&(?!lt;)/','&amp;',$text);
		$text = str_replace('<','&lt;',$text);
		$text = preg_replace('/&amp;#036;/Umsi', '&#036;', $text); // ??
		$text = preg_replace('/&amp;#092;/Umsi', '&#092;', $text); // ??
		$text = preg_replace('/\^(.)/Umsie', "'&#'.ord('$1').';'", $text); // escape caractère
		$nbcode = preg_match_all('/{{(.+)}}/Ums',$text,$matches_code);
		$text = preg_replace('/{{(.+)}}/Ums','<pre><code>{{CODE}}</code></pre>',$text);
		$text = $this->parseTables($text);
		$text = preg_replace('/----*(\r\n|\r|\n)/m', '<hr />', $text);
		$text = preg_replace('/^\*\*\*(.*)(\n)/Um', "<ul><ul><ul><li>$1</li></ul></ul></ul>$2", $text);
		$text = preg_replace('/^\*\*(.*)(\n)/Um', "<ul><ul><li>$1</li></ul></ul>$2", $text);
		$text = preg_replace('/^\*(.*)(\n)/Um', "<ul><li>$1</li></ul>$2", $text);
		$text = preg_replace('/^\#\#\#(.*)(\n)/Um', "<ol><ol><ol><li>$1</li></ol></ol></ol>$2", $text);
		$text = preg_replace('/^\#\#(.*)(\n)/Um', "<ol><ol><li>$1</li></ol></ol>$2", $text);
		$text = preg_replace('/^\#(.*)(\n)/Um', "<ol><li>$1</li></ol>$2", $text);
		$text = preg_replace('/(<\/ol>\n*<ol>|<\/ul>\n*<ul>)/', '', $text);
		$text = preg_replace('/(<\/ol>\n*<ol>|<\/ul>\n*<ul>)/', '', $text);
		$text = preg_replace('/(<\/ol>\n*<ol>|<\/ul>\n*<ul>)/', '', $text);
		$text = preg_replace('#</li><ul><li>*#', '<ul><li>', $text);
		$text = preg_replace('#</ul></ul>*#', '</ul></li></ul>', $text);
		$text = preg_replace('#</ul></ul>*#', '</ul></li></ul>', $text);
		$text = preg_replace('#</li></ul><li>*#', '</li></ul></li><li>', $text);
		$text = preg_replace('#</li><ol><li>*#', '<ol><li>', $text);
		$text = preg_replace('#</ol></ol>*#', '</ol></li></ol>', $text);
		$text = preg_replace('#</ol></ol>*#', '</ol></li></ol>', $text);
		$text = preg_replace('#</li></ol><li>*#', '</li></ol></li><li>', $text);
		$text = preg_replace_callback('/^(!+?)(.*)$/Um', 'name_title', $text);
		// Paragraphes
		$text = preg_replace('/(^$\n)+([^<]+?)^$/ms',"<p>\n$2</p>",$text); // <p></p> (sans balise)      
		// balises type en ligne
		$text = str_replace('%%','<br />',$text); // %%
		$text = str_replace('&lt;-->', '&harr;', $text); // <-->
		$text = str_replace('-->', '&rarr;', $text); // -->
		$text = str_replace('&lt;--', '&larr;', $text); // <--
		$text = preg_replace('/\([cC]\)/Umsi', '&copy;', $text); // (c)
		$text = preg_replace('/\([rR]\)/Umsi', '&reg;', $text); // (r)
		$rg_url        = "[0-9a-zA-Z\.\#/~\-_%=\?\&,\+\:@;!\(\)\*\$']*"; // TODO: verif & / &amp;
		$rg_img_local  = '('.$rg_url.'\.(jpeg|jpg|gif|png))'; 
		$rg_img_http   = 'h(ttps?://'.$rg_url.'\.(jpeg|jpg|gif|png))';
		$rg_link_local = '('.$rg_url.')';
		$rg_link_http  = 'h(ttps?://'.$rg_url.')';
		// image
		$text = preg_replace('#\['.$rg_img_http.'(\|(right|left))?\]#','<img src="xx$1" alt="xx$1" style="float:$4;"/>',$text); // [http.png] / [http.png|right]
		$text = preg_replace('#\['.$rg_img_local.'(\|(right|left))?\]#','<img src="$1" alt="$1" style="float:$4"/>',$text); // [local.png] / [local.png|left]
		// image link [http://wikiss.tuxfamily.org/img/logo_100.png|http://wikiss.tuxfamily.org/img/logo_100.png]      
		$text = preg_replace('#\['.$rg_img_http.'\|'.$rg_link_http  .'(\|(right|left))?\]#U', '<a href="xx$3" class="url"><img src="xx$1" alt="xx$3" title="xx$3" style="float:$5;"/></a>', $text);  // [http|http]
		$text = preg_replace('#\['.$rg_img_http.'\|'.$rg_link_local .'(\|(right|left))?\]#U', '<a href="$3" class="url"><img src="xx$1" alt="$3" title="$3" style="float:$5;"/></a>', $text); // [http|local]
		$text = preg_replace('#\['.$rg_img_local.'\|'.$rg_link_http .'(\|(right|left))?\]#U', '<a href="xx$3" class="url"><img src="$1" alt="xx$3" title="xx$3" style="float:$5;"/></a>', $text); // [local|http]
		$text = preg_replace('#\['.$rg_img_local.'\|'.$rg_link_local.'(\|(right|left))?\]#U', '<a href="$3" class="url"><img src="$1" alt="$3" title="$3" style="float:$5;"/></a>', $text); // [local|local]
		$text = preg_replace('#\[([^\]]+)\|'.$rg_link_http.'\]#U', '<a href="xx$2" class="url">$1</a>', $text);
		$text = preg_replace('#\[([^\]]+)\|'.$rg_link_local.'\]#U', '<a href="$2" class="url">$1</a>', $text);
		$text = preg_replace('#'.$rg_link_http.'#i', '<a href="$0" class="url">xx$1</a>', $text);
		$text = preg_replace('#xxttp#', 'http', $text);
		$text = preg_replace('#\[\?(.*)\]#Ui', '<a href="http://'.$LANG.'.wikipedia.org/wiki/$1" class="url" title="Wikipedia">$1</a>', $text); // Wikipedia
		preg_match_all('/\[([^\/]+)\]/U', $text, $matches, PREG_PATTERN_ORDER);
		foreach ($matches[1] as $match)
			if (file_exists($PAGES_DIR.$match.'.txt'))
				$text = str_replace("[$match]", '<a href="./?page='.$match.'">'.$match.'</a>', $text);
			else
				$text = str_replace("[$match]", '<a href="./?page='.$match.'" class="pending" >'.$match.'</a>', $text);
		$text = preg_replace('#([0-9a-zA-Z\./~\-_]+@[0-9a-z\./~\-_]+)#i', '<a href="mailto:$0">$0</a>', $text);  
		while (preg_match('/^  /Um', $text))
			$text = preg_replace('/^( +) ([^ ])/Um', '$1&nbsp;&nbsp;&nbsp;&nbsp;$2', $text);
		$text = preg_replace('/^ /Um', '&nbsp;&nbsp;&nbsp;&nbsp;', $text);
		$text = preg_replace("/'--(.*)--'/Um", '<span style="text-decoration:line-through">$1</span>', $text); // barré
		$text = preg_replace("/'__(.*)__'/Um", '<span style="text-decoration:underline">$1</span>', $text); // souligné
		$text = preg_replace("/'''''(.*)'''''/Um", '<strong><em>$1</em></strong>', $text);
		$text = preg_replace("/'''(.*)'''/Um", '<strong>$1</strong>', $text);
		$text = preg_replace("/''(.*)''/Um", '<em>$1</em>', $text);
		// TOC
		if (strpos($text,'%TOC%') !== FALSE){
			$text = preg_replace('/%TOC%/Um','',$text,1);
			$nbAncres = count($titres);
			$toc = '<div id="toc">';
			for ($i=0;$i<$nbAncres;$i++) $toc .= '<h'.$titres[$i][0].'><a href="#'.urlencode($titres[$i][1]).'">'.preg_replace('#[\[\]]#','',$titres[$i][2]).'</a></h'.$titres[$i][0].'> ';
				$toc .= '</div>';
		}
		//-- {CODE}
		if ($nbcode > 0)
			$text = preg_replace_callback(array_fill(0,$nbcode,'/{{CODE}}/'),'matchcode',$text);
		return $text;
	}

	private function table_style($s) {
		$r = ''; $st = '';
		if (strpos($s, 'l') !== false)
			$st .= 'text-align: left; ';
		else if (strpos($s, 'r') !== false)
			$st .= 'text-align: right; ';
		if (strpos($s, 't') !== false)
			$st .= 'vertical-align: top; ';
		else if (strpos($s, 'b') !== false)
			$st .= 'vertical-align: bottom; ';   
		return $r . ($st ? ' style="' . $st . '"' : '');
	}

	private function make_table($s) {
		global $matches_links;
		// Suppression des espaces en debut et fin de ligne
		//~ $s = trim($s);
		// on enleve les liens contenants |
		$regex = "/\[([^]]+\|.+)\]/Ums";
		$nblinks = preg_match_all($regex,$s,$matches_links);
		$s = preg_replace($regex,"[LINK]",$s);
		// Doublage des |
		$s = str_replace('|', '||', $s);
		// Creation des <tr></tr> en se servant des debuts et fins de ligne
		$s = preg_replace('/^\s*\|(.*)\|\s*$/m', '<tr>$1</tr>', $s);
		$s = str_replace("\n","",$s);
		// Creation des <th></th> et des <td></td> en se servant des |
		$s=preg_replace('/\|(h){0,1}(([lrtb]* ){0,1})(\s*(\d*)\s*,(\d*)\s*){0,1}(.*?)\|/e',
		   '"<t".("$1"?"h":"d").("$5"?" colspan=\"$5\"":" ").("$6"?" rowspan=\"$6\"":" ").$this->table_style("$2").">$7</t".("$1"?"h":"d").">"',$s);
		if ($nblinks> 0)
			$s = preg_replace_callback(array_fill(0,$nblinks,"/\[LINK\]/"),
						create_function('$m',
							'global $matches_links;static $idxlink=0;return "[".$matches_links[1][$idxlink++]."]";') ,$s);
		return stripslashes($s);
	}

	private function parseTables($text) {
		global $text;
		$text = preg_replace(
			"/((^ *\|[^\n]*\| *$\n)+)/me",
			'"<table class=\"wikitable\">".stripslashes($this->make_table("$1"))."</table>\n"',
			$text);
		return $text;
	}

}

function name_title($matches) {
	global $titres;
	$titres[]=array(strlen($matches[1]),preg_replace('/[^\da-z]/i','_',$matches[2]),$matches[2]);$i=count($titres)-1;
	return '<h'.$titres[$i][0].'><a name="'.$titres[$i][1].'">'.$titres[$i][2].'</a></h'.$titres[$i][0].'>';
}

function matchcode($m){
	global $matches_code;
	static $idxcode=0;
	return $matches_code[1][$idxcode++];
}
