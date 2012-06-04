<?php
/* php creole parser
 * written by Florian Blatt, 2007
 * rukh.de
 *
 * This work is licensed under a 
 * Creative Commons Attribution-Noncommercial-Share Alike 3.0 License
 * http://creativecommons.org/licenses/by-nc-sa/3.0/
*/

class Creole {
	private $text;
	private $result;
	private $inTable;
	private $listLevel;
	private $listLevels;
	private $pos;
	private $textLen;

	public function __construct($wiki) {
		$this->clearParser();
	}

	private function append($text) {
		$this->result .= $text;
	}

	private function clearParser() {
		$this->text = "";
		$this->result = "";
		$this->inTable = false;
		$this->listLevel = 0;
		$this->listLevels = array();
		$this->pos = 0;
		$this->textLen = 0;
	}

	private function closeListsAndTables() {
		while ($this->listLevel > 0) {
			$c = $this->listLevels[$this->listLevel--];
			$l = ($c == '*' ? '</li></ul>':'</li></ol>');
			$this->append($l);
		}
		$this->listLevels = array();
		if($this->inTable) {
			$this->append("</table>\n");
			$this->inTable = false;
		}
	}

	private function addHeader($pos, $hc) {
		$this->append('<h'.$hc.'>');
		$n = strpos($this->text, "\n", $pos);
		$t = trim(substr($this->text, $pos, $n-$pos));
		$t = preg_replace("#( |\t)*(={1,6})?$#", '', $t);
		$this->append($this->parseInline($t).'</h'.$hc.">\n");
		$this->pos = $n;
	}

	private function findEndOfNoWiki($start, $text) {
		$end = $start - 3;
		$len = strlen($text);
		do {
			$end = strpos($text, "}}}", $end + 3);
			if(!$end || $end > $len) return $len;
			while($end + 3 < $len && $text[$end+3] == '}')
				$end++;
		} while ($text[$end-1] == '~');
		return $end;
	}

	private function appendNoWiki($text, $inline=false) {
		$s = array('~{{{', '~}}}');
		$r = array('{{{', '}}}');
		$text = trim(str_replace($s, $r, $text));
		if($inline)
			return "<tt>$text</tt>";
		else
			return "<pre>$text</pre>";
	}

	private function parseTableRow($pos) {
		$this->append('<tr>');
		$inRow = true;
		$n = strpos($this->text, "\n", $pos);
		while($inRow) {
			$colspan = 0;
			$spaces = false;
			while($pos+$colspan < $this->textLen &&
				  $this->text[$pos+$colspan] == '|') $colspan++;
			$pos += $colspan++;
			$th = ($pos < $this->textLen && $this->text[$pos] == '=');
			$pos += ($th?1:0);
			$nextPipe = $pos - 1;
			do {
				$nextPipe = strpos($this->text, '|', $nextPipe+1);
				if(!$nextPipe) {
					$nextPipe = $n;
					break;
				}
			} while($this->text[$nextPipe-1] == '~');
			if(!$nextPipe || $nextPipe > $n || $nextPipe > $this->textLen) {
				$inRow = false;
				$npos = $n;
			} else {
				$npos = $nextPipe;
				if(preg_match("#^\s*$#", substr($this->text, $npos+1, $n-$npos))) {
					$spaces = true;
					$inRow = false;
				}
			}
			$this->append($th? '<th':'<td');
			if ($colspan > 1) $this->append(' colspan="'.$colspan.'"');
			$this->append('>');
			$t = trim(substr($this->text, $pos, $npos-$pos));
			$this->append($this->parseInline($t));
			$this->append($th? '</th>':'</td>');
			$pos = ($spaces? $n-2:$npos)+1;
		}
		$this->append("</tr>\n");
		return $pos+2;
	}

	private function parseEntry($pos) {
		$start = $pos;
		$lastNewline = strpos($this->text, "\n", $pos);
		if($lastNewline < $this->textLen || !$lastNewline) {
			$pos = $lastNewline+1;
			do {
				$c = $this->text[$pos];
				if($c == "\n") {
					if(preg_match("#^\s*$#", 
						substr($this->text, $lastNewline, $pos-$lastNewline)))
						break;
					else
						$lastNewline = $pos;
				}
				if($c != '' && false !== strpos('#*|={', $c)) {
					if(preg_match("#^\s*$#", 
						substr($this->text, $lastNewline, $pos-$lastNewline)))
						break;
				}
			} while($pos++ < $this->textLen);
		} else 
			$pos = $lastNewline;
		$t = trim(substr($this->text, $start, $pos-$start));
		$this->append($this->parseInline($t));
		return $pos;
	}

	private function parseWikiPage() {
		while($this->pos < $this->textLen) {
			$c = $this->text[$this->pos];
			if($c == "\n") {
				$this->closeListsAndTables();
				$this->pos++;
				continue;
			}
			if($this->listLevel >= 0 && 
			   ($c == '*' ||
			   $c == '#')) {
				$lc = 0;
				for(; $lc <= $this->listLevel && 
					$this->pos+$lc < $this->textLen &&
					$this->text[$this->pos+$lc] == $this->listLevels[$lc+1]; $lc++) ;
				if($lc < $this->listLevel) {
					do {
						$c = $this->listLevels[$this->listLevel--];
						$l = ($c == '*' ? '</li></ul>':'</li></ol>');
						$this->append($l);
					} while($lc < $this->listLevel);
					continue;
				} else {
					$c = $this->text[$this->pos+$lc];
					if(($c == '*' || $c == '#') &&
						$this->pos+$lc+1 < $this->textLen &&
						$this->text[$this->pos+$lc+1] != $c) {
						$l = ($c == '*' ? '<ul><li>':'<ol><li>');
						$this->append($l);
						$this->listLevels[++$this->listLevel] = $c;
						$this->pos = $this->parseEntry($this->pos+$lc+1);
						continue;
					} else if ($this->listLevel > 0) {
						$this->append("</li>\n<li>");
						$this->pos = $this->parseEntry($this->pos+$lc);
						continue;
					}
				}
			}
			if($c == '|') {
				if(!$this->inTable) {
					$this->closeListsAndTables();
					$this->append('<table>');
					$this->inTable = true;
				} 
				$this->pos = $this->parseTableRow($this->pos+1);
				continue;
			} else {
				if($this->inTable) {
					$this->append("</table>\n");
					$this->inTable = false;
				}
			}
			if($c == '=') {
				$hc = 1;
				for(;$hc < 6 && 
					$this->pos+$hc < $this->textLen &&
					$this->text[$this->pos+$hc] == '='; $hc++) ;
				$this->addHeader($this->pos+$hc, $hc);
				continue;
			}
			if($c == '-' &&
				substr($this->text, $this->pos, 4) == '----') {
				$p = $this->pos+4;
				for(; $p < $this->textLen && 
					($this->text[$p] == ' ' || $this->text[$p] == "\t"); $p++);
				if($this->text[$p] == "\n" || $p >= $this->textLen) {
					$this->append("<hr />\n");
					$this->pos = $p;
					continue;
				}
			}
			if($c == '{') {
				if($this->text[$this->pos+1] == '{' &&
				   $this->text[$this->pos+2] == '{') {
					$start = $this->pos + 3;
					$end = $this->findEndOfNoWiki($start, $this->text)+3;
				 
					$this->append($this->appendNoWiki(substr($this->text, $start, $end-$start-3)));
					$this->pos = $end;
					continue;
				}
			}
			if($c == '~') {
				if($this->pos+1 < $this->textLen) {
					$nc = $this->text[$this->pos+1];
					if($nc == '=' || $nc == '|') {
						$this->pos++;
						$c = $nc;					
					} else if(($nc == '#' || $nc == '*' || $nc == '-') &&
							  $this->pos+2 < $this->textLen)	{
						if($this->text[$this->pos+2] != $nc) {
							$this->pos++;
							$c = $nc;
						}
					}
				}
			}
			$this->append('<p>');
			$this->pos = $this->parseEntry($this->pos);
			$this->append("</p>\n");
		}
	}

	private function parseImage($str) {
		$pipe = strpos($str, '|');
		if($pipe === false) return '';
		return "<img src=\"".substr($str, 0, $pipe)."\" alt=\"".substr($str, $pipe+1)."\" />";
	}

	private function isURI($str) {
		return preg_match("#^[A-Za-z]{3,}://#", $str);
	}

	private function parseLink($str) {		
		$pipe = strpos($str, '|');
		if($pipe === false) { // no name
			$str = trim($str);
			$link = str_replace(' ', '_', $str);
		} else {
			$link = str_replace(' ', '_', trim(substr($str, 0, $pipe)));
			$str = trim(substr($str, $pipe+1));
		}
		if($this->isURI($link))
			return "<a href=\"$link\" class=\"extern\">$str</a>";
		else
			return $this->wiki->internalLinkTag($link, $str);
	}

	private function parseInline($text) {
		$s = array('<','>');
		$r = array('&lt;', '&gt');
		$text = str_replace($s, $r, $text);
		$result = '';
		$pos = 0;
		$len = strlen($text);
		$stack = array();
		while($pos<$len) {
			$c = $text[$pos];
			if($c == '{') {
				if($pos+1 < $len && $text[$pos+1] == '{') {
					if($pos+2 < $len && $text[$pos+2] == '{') {
						$start = $pos + 3;
						$end = $this->findEndOfNoWiki($start, $text)+3;
					 
						$result .= $this->appendNoWiki(substr($text, $start, $end-$start-3), true);
						$pos = $end;
						continue;
					} else {
						$end = strpos($text, '}}', $pos+2);
						if($end && $end < $len) {
							$result .= $this->parseImage(substr($text, $pos+2, $end-$pos-2));
							$pos = $end+2;
							continue;
						}
					}
				}
			} else if($c == '[') {
				if($pos+1 < $len && $text[$pos+1] == '[') {
					$start = $pos + 2;
					$end = strpos($text, ']]', $pos+2);
					if($end === false || $end > $len) $end = $len;
					$result .= $this->parseLink(substr($text, $start, $end-$start));
					$pos = $end+2;
					continue;
				}
			} else if($c == '-' && substr($text, $pos, 4) == '----') {
				$pos += 4;
				$result .= "<hr />\n";
				continue;
			} else if($c == '\\') {
				$result .= '<br />';
				$pos += 2;
				continue;
			} else if($c == '*') {
				if($pos+1<$len && $text[$pos+1] == '*') {
					if($stack[count($stack) - 1] == '</strong>') {
						$result .= array_pop($stack);
					} else {
						$result .= '<strong>';
						array_push($stack, '</strong>');
					}
					$pos += 2;
					continue;
				}
			} else if($c == '/') {
				if($pos+1<$len && $text[$pos+1] == '/') {
					if($text[$pos-1] == ':') {
						$p = $pos;
						for(;$p >= 0 && !preg_match("# |~#", $text[$p]); $p--);
						$nb = $pos;
						for(;$nb < $len && preg_match("#\S#", $text[$nb]); $nb++);
						$uri = substr($text, $p+1, $nb-$p-1);
						if($this->isURI($uri) && $text[$p] != '~') {
							if(strpos(".,:;!?'\"", $uri[strlen($uri)-1]) !== false) {
								$lastsign = $uri[strlen($uri)-1];
								$uri = substr($uri, 0, strlen($uri)-1);
							} else
								$lastsign = '';
							$l = strpos($uri, '/');
							$result = substr($result, 0, strlen($result)-$l).
								"<a href=\"$uri\" class=\"extern\">$uri</a>$lastsign";
							$pos = $nb;
						} else {
							$result = preg_replace("#~([A-Za-z]{3,}:)$#", "$1", $result);
							$pos++;
						}
					} else {
						if($stack[count($stack) - 1] == '</em>') {
							$result .= array_pop($stack);
						} else {
							$result .= '<em>';
							array_push($stack, '</em>');
						}
						$pos += 2;
					}
					continue;
				}
			} else if($c == '~') {
				if($pos+1 < $len && strpos("#*-~{[/", $text[$pos+1]) !== false) {
					$c = $text[$pos+1];
					$pos++;
				}
			}
			$result .= $c;
			$pos++;
		}
		while(count($stack) > 0) {
			$result .= array_pop($stack);
		}
		return $result;
	}

	public function parse($wikipage, $title = "", $date = null, $parameters = array()) {
		if(!is_string($wikipage) || $wikipage == '') return '';
		$this->clearParser();
		$wikipage = str_replace("\r\n", "\n", $wikipage)."\n";
		$this->text = $wikipage;
		$this->textLen = strlen($wikipage);
		$this->parseWikiPage();
		$this->closeListsAndTables();
		return $this->result;
	}

}
