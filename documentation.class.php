<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
 *  @package web2Project
 *  @subpackage modules
 *  @version 
 */
require_once W2P_BASE_DIR . "/modules/documentation/config.php";

/**
 *  CDocumentation Class
 */
class CDocumentation extends w2p_Core_BaseObject
{

    /**
    @var int Primary Key */
    public $wikipage_id = 0;
    /**
    @var datetime */
    public $wikipage_date = null;
    /**
    @var int */
    public $wikipage_user = 0;
    /**
    @var int */
    public $wikipage_start = 0;
    /**
    @var int */
    public $wikipage_locked = 0;
    /**
    @var string */
    public $wikipage_parser = null;
    /**
    @var string */
    public $wikipage_namespace = null;
    /**
    @var string */
    public $wikipage_name = null;
    /**
    @var string */
    public $wikipage_lang = null;
    /**
    @var string */
    public $wikipage_title = null;
    /**
    @var string */
    public $wikipage_content = null;
    /**
    @var string */
    public $wikipage_categorylinks = null;

    public function __construct() {
        parent::__construct('wikipages', 'wikipage_id', "documentation");
    }

    // overload check
    public function check() {
        if ($this->wikipage_id === null) {
            return 'wikipage id is NULL';
        }
        $this->wikipage_id = intval($this->wikipage_id);
        return null; // object is ok
    }

    public function bind($hash, $prefix = null, $checkSlashes = true, $bindAll = false) {
        if (!is_array($hash)) {
            $this->_error = get_class($this) . '::bind failed.';
            return false;
        } else {
            /*
             * We need to filter out any object values from the array/hash 
             * so the bindHashToObject() doesn't die.
             * We also avoid issues such as passing objects to non-object functions
             * and copying object references instead of cloning objects.
             * Object cloning (if needed) should be handled seperatly anyway.
             */
            foreach ($hash as $k => $v) {
                if (!(is_object($hash[$k]))) {
                    $filtered_hash[$k] = 
                        (is_string($v) && $k != 'wikipage_content') ? 
                        strip_tags($v) : 
                        $v;
                }
            }
            $this->_query->bindHashToObject(
                $filtered_hash, 
                $this, 
                $prefix, 
                $checkSlashes, 
                $bindAll
            );
            $this->_query->clear();
            return true;
        }
    }
 
	/**
	 *	Returns an array, keyed by the key field, of all elements that meet
	 *	the where clause provided. Ordered by $order key.
	 */
	public function loadAll($order = null, $where = null, $limit = null, $offset =- 1)
	{
		$this->_query->clear();
		$this->_query->addTable($this->_tbl);
		if ($order) {
			$this->_query->addOrder($order);
		}
		if ($where) {
			$this->_query->addWhere($where);
		}
		if ($limit !== null) {
			$this->_query->setLimit($limit, $offset);
		}
		$result = $this->_query->loadHashList($this->_tbl_key);
		$this->_query->clear();
		return $result;
	}
   
    private function _delete() {
       return parent::delete();
    }

    public function delete(CAppUI $AppUI) {
        $perms = $AppUI->acl();
        if (!$perms->checkModuleItem('documentation', 'delete', $this->wikipage_id)) {
            return false;
        }
        $this->load($this->wikipage_id);
        if ($this->wikipage_namespace == 'Category' && $this->wikipage_categorylinks) {
            $this->wikipage_content = "";
            $msg = parent::store();
        } else {
            if ($this->wikipage_namespace != 'Category' && $this->wikipage_categorylinks) {
                $wiki = new CWiki($this->wikipage_namespace);
                $categorylinks = explode("|", $this->wikipage_categorylinks);
                foreach($categorylinks as $categorylink) {
                    $category = ucfirst(str_replace(' ', '_', trim($categorylink)));
                    $cat = $wiki->loadPage("Category:".$category);
                    if ($cat->wikipage_id > 0) { 
                        $catlinks = explode("|", $cat->wikipage_categorylinks);
                        foreach($catlinks as $c => $catlink) {
                            list($namespace, $title) = explode(":", $catlink);
                            if ($namespace == $this->wikipage_namespace && $title == $this->wikipage_title) {
                                array_splice($catlinks, $c, 1);
                                break;
                            }
                        }
                        $cat->wikipage_categorylinks = implode("|", $catlinks);
                        if ($cat->wikipage_categorylinks || $cat->wikipage_content) {
                            if ($msg = $cat->_store()) {
                                return $msg;
                            }
                        } elseif ($msg = $cat->_delete()) {
                            return $msg;
                        }
                    } 
                }
            }
            $msg = parent::delete();
        }
        return $msg ? $msg : true;
    }
    
    private function _store() {
       return parent::store();
    }

    public function store(CAppUI $AppUI) {
        global $db, $WIKI_CONFIG;

        require_once W2P_BASE_DIR . "/modules/documentation/config.php";
        $perms = $AppUI->acl();
        $isNotNew = $this->wikipage_id > 0;
        if ($isNotNew) {
            if (!$perms->checkModuleItem('documentation', 'edit', $this->wikipage_id)) {
                return false;
            }
        } else {
            if (!$perms->checkModuleItem('documentation', 'add')) {
                return false;
            }
        }
        $this->wikipage_user = $AppUI->user_id;
        $this->wikipage_date = str_replace("'", '', $db->DBTimeStamp(time()));
        if (!$this->wikipage_parser) {
            $this->wikipage_parser = $WIKI_CONFIG['default_parser'];
        }
        if (!$this->wikipage_lang) {
            $this->wikipage_lang = $WIKI_CONFIG['default_lang'];
        }
        $oldcategories = array();
        $categorylinks = array();
        if ($this->wikipage_namespace == $AppUI->_('Category')) {
            $this->wikipage_namespace = 'Category';
        } elseif ($this->wikipage_namespace == $AppUI->_('Template')) {
            $this->wikipage_namespace = 'Template';
        } elseif ($this->wikipage_namespace == $AppUI->_('Image')) {
            $this->wikipage_namespace = 'Image';
        } elseif ($this->wikipage_namespace == $AppUI->_('File')) {
            $this->wikipage_namespace = 'File';
        }
        if ($this->wikipage_namespace != 'Category') {
            $wiki = new CWiki($this->wikipage_namespace);
            require ("engines/".$this->wikipage_parser."/parser.php");
            $parser = new $this->wikipage_parser($wiki);
            if (method_exists($parser, 'getCategories')) {
                $parser->parse(
                    $this->wikipage_content, 
                    $this->wikipage_title,
                    strtotime($this->wikipage_date)
                );
                $oldcategorylinks = explode("|", $this->wikipage_categorylinks);
                foreach($oldcategorylinks as $ $oldcategorylink) {
                    $oldcategories[$oldcategorylink] = 1;
                }
                $newcategories = $parser->getCategories();
                foreach($newcategories as $r => $newcategory) {
                    $categorylinks[] = $newcategory[1];
                }
                $this->wikipage_categorylinks = implode("|", $categorylinks);
            }
        }
        list($namespace, $name) = explode(":", $this->wikipage_title);
        if ($name) {
            $this->wikipage_namespace = $namespace;
            $this->wikipage_title = $name;
        }
        if (!$this->wikipage_name) {
            $this->wikipage_name = ucfirst(str_replace(' ', '_', trim($this->wikipage_title)));
        }
        $this->wikipage_content = preg_replace_callback(
            "/(\{\{subst:([^\}]*?)\}\})/",
            array(&$this,"substitute_template"), 
            $this->wikipage_content
        );
        if (($msg = parent::store())) {
            return $msg;
        }
        if ($this->wikipage_namespace != 'Category') {
            foreach($categorylinks as $category) {
                if (!isset($oldcategories[$category])) {
                    $cat = $wiki->loadPage(
                        "Category:".ucfirst(str_replace(' ', '_', trim($category)))
                    );
                    if ($cat->wikipage_id > 0) { 
                        $catlinks = explode("|", $cat->wikipage_categorylinks);
                    } else {
                        $cat->wikipage_user = $AppUI->user_id;
                        $cat->wikipage_date = str_replace("'", '', $db->DBTimeStamp(time()));
                        $cat->wikipage_parser = $this->wikipage_parser;
                        $cat->wikipage_title = $category;
                        $cat->wikipage_namespace = 'Category';
                        $cat->wikipage_name = ucfirst(str_replace(' ', '_', trim($cat->wikipage_title)));
                        $cat->wikipage_lang = $this->wikipage_lang;
                        $catlinks = array();
                    }
                    $catlinks[] = $this->wikipage_namespace.":".$this->wikipage_title;
                    $cat->wikipage_categorylinks = implode("|", $catlinks);
                    if (($msg = $cat->_store())) {
                        return $msg;
                    }
                }
            }     
        }     
        return true;
    }

    private function substitute_template(&$matches) {
        global $AppUI;
        $template = new CDocumentation();
        $result = $template->loadAll(
            null,
            "wikipage_namespace = 'Template' and wikipage_name = '".$matches[2]."'"
        );
        if (count($result) == 0)
            return "";
        $keys = array_keys($result);
        $key = $keys[0];
        $template->bind($result[$key]);
        return $template->content();
    }

    public function content() {
        return $this->wikipage_content;
    }

    public function getWikipageId() {
        return $this->wikipage_id;
    }

}

/**
 *  Wiki Class
 */
class CWiki
{

  /**
  @var string */
  private $default_namespace;
  
  private $current_page;
  
  private $preview_mode;

  public function __construct($default_namespace = "", $preview_mode = false) {
    $this->setNamespace($default_namespace);
    $this->preview_mode = $preview_mode;
    $this->current_page = null;
  }

  public function setNamespace($default_namespace) {
    global $AppUI;
    $this->default_namespace = 
      $default_namespace == $AppUI->_('Category') ?
      'Category' :
      $default_namespace == $AppUI->_('Template') ?
      'Template' :
      $default_namespace == $AppUI->_('File') ?
      'File' :
      $default_namespace == $AppUI->_('Special') ?
      'Special' :
      $default_namespace;
  }

  public function getNamespace() {
    return is_null($this->current_page) ?
      $this->default_namespace : 
      $this->current_page->wikipage_namespace;
  }

  public function pagename() {
    return is_null($this->current_page) ?
      "" : 
      $this->current_page->wikipage_name;
  }

  public function pagedate() {
    return is_null($this->current_page) ?
      null :
      $this->current_page->wikipage_date;
  }

  public function getPage($wikipage_id) {
    $this->current_page = new CDocumentation();
    $this->current_page->load($wikipage_id);
    return $this->current_page;
  }

  public function loadStartPage() {
    global $AppUI;
    $this->current_page = new CDocumentation();
    $result = $this->current_page->loadAll(
      null,
      "wikipage_namespace = '".$this->default_namespace."' and wikipage_start = 1"
    );
    if (count($result) > 0) {
      $keys = array_keys($result);
      $key = $keys[0];
      $this->current_page->bind($result[$key]);
    }
    return $this->current_page;
  }

  public function loadPage($page) {
    list($wikipage_namespace, $wikipage_name) = explode(":", $page);
    if (!$wikipage_name) {
      $wikipage_name = $wikipage_namespace;
      $wikipage_namespace = $this->default_namespace;
    }
    $this->current_page = new CDocumentation();
    $q = new w2p_Database_Query;
    $result = $this->current_page->loadAll(
      null, 
      "wikipage_namespace = '".$wikipage_namespace."' and wikipage_name = ".$q->quote($wikipage_name)
    );
    if (count($result) > 0) {
      $keys = array_keys($result);
      $key = $keys[0];
      $this->current_page->bind($result[$key]);
    }
    return $this->current_page;
  }

  public function exists($page) {
    list($wikipage_namespace, $wikipage_name) = explode(":", $page);
    if (!$wikipage_name) {
      $wikipage_name = $wikipage_namespace;
      $wikipage_namespace = $this->default_namespace;
    }
    if ($wikipage_namespace = "Special") {
        return true;
    }
    $wikipage = new CDocumentation();
    $q = new w2p_Database_Query;
    $result = $wikipage->loadAll(
      null,
      "wikipage_namespace = '".$wikipage_namespace."' and wikipage_name = ".$q->quote($wikipage_name)
    );
    if (count($result) > 0) {
      $keys = array_keys($result);
      $key = $keys[0];
      $wikipage->bind($result[$key]);
      return $wikipage->wikipage_id;
    }
    return false;
  }

  public function loadTemplate($wikipage_name) {
    global $AppUI;
    $wikipage = new CDocumentation();
    $q = new w2p_Database_Query;
    $result = $wikipage->loadAll(
      null,
      "wikipage_namespace = 'Template' and wikipage_name = ".$q->quote($wikipage_name)
    );
    if (count($result) > 0) {
      $keys = array_keys($result);
      $key = $keys[0];
      $wikipage->bind($result[$key]);
    }
    return $wikipage;
  }

  public function loadIndex($namespace = "", $dir = "next", $limit = null, $offset =- 1) {
    if ($namespace) {
      $this->default_namespace = $namespace;
    }
    $wikipages = array();
    $wikipage = new CDocumentation();
    $rows = $wikipage->loadAll(
      $dir == "prev" ? "wikipage_title DESC" : "wikipage_title ASC" ,
      "wikipage_namespace = '".$this->default_namespace."'",
      $limit,
      $offset
    );
    foreach ($rows as $row) {
      $wikipage = new CDocumentation();
      $wikipage->bind($row);
      $wikipages[] = $wikipage;
    }
    return $wikipages;
  }

  public function render(CDocumentation $wikipage) {
    global $AppUI;
    if ($wikipage->wikipage_id === null) {
      return false;
    }
    $this->current_page = $wikipage;
    require_once ("engines/".$wikipage->wikipage_parser."/parser.php");
    $parser = new $wikipage->wikipage_parser($this);
    $content = $parser->parse(
      $wikipage->wikipage_content, 
      $wikipage->wikipage_title, 
      strtotime($wikipage->wikipage_date)
    );
    if ($wikipage->wikipage_namespace == 'Category' && $wikipage->wikipage_categorylinks) {
      $categorylinks = explode("|", $wikipage->wikipage_categorylinks);
      usort(
        $categorylinks,
        create_function('$a,$b', '
          list($n1, $t1) = explode(":", $a);
          list($n2, $t2) = explode(":", $b);
          return strcasecmp($t1, $t2);
        ')
      );
      $titles = array();
      $letters = array();
      foreach($categorylinks as $categorylink) {
        list($namespace, $title) = explode(":", $categorylink);
        $name = ucfirst(str_replace(' ', '_', trim($title)));
        $titles[] = $this->internalLinkTag($namespace.":".$name, $title);
        $letters[] = $title{0};
      }
      $content .= '<div id="category-pages">';
      $content .= '<h2>'.$AppUI->_('Pages in category').' « '.$wikipage->wikipage_title.' »</h2>';
      $content .= '<p>'.sprintf($AppUI->_('There are %d pages in this category'), sizeof($categorylinks)).'</p>';
      $content .= $this->columnList( $titles, $letters );
      $content .= '</div>';
    }
    return '<div id="documentation">'.$content.'</div>';
  }
  
  private function columnList( $titles, $letters ) {
    global $AppUI;
    $columns = array_combine( $titles, $letters );
    # Split into three columns
    $columns = array_chunk( $columns, ceil( count( $columns ) / 3 ), true /* preserve keys */ );
    $ret = '<table width="100%"><tr valign="top"><td>';
    $prevchar = null;
    foreach ( $columns as $column ) {
      $colContents = array();
      foreach ( $column as $article => $char ) {
        if ( !isset( $colContents[$char] ) ) {
          $colContents[$char] = array();
        }
        $colContents[$char][] = $article;
      }
      $first = true;
      foreach ( $colContents as $char => $titles ) {
        $ret .= '<h3>' . htmlspecialchars( $char );
        if ( $first && $char === $prevchar ) {
          $ret .= ' ' . $AppUI->_('cont.' );
        }
        $ret .= "</h3>\n";
        $ret .= '<ul><li>';
        $ret .= implode( "</li>\n<li>", $titles );
        $ret .= '</li></ul>';
        $first = false;
        $prevchar = $char;
      }
      $ret .= "</td>\n<td>";
    }
    $ret .= '</td></tr></table>';
    return $ret;
  }

  public function getTemplates(CDocumentation $wikipage) {
    if ($wikipage->wikipage_id === null) {
      return false;
    }
    $this->current_page = $wikipage;
    require ("engines/".$wikipage->wikipage_parser."/parser.php");
    $parser = new $wikipage->wikipage_parser($this);
    return $parser->getTemplates($wikipage->wikipage_content);
  }

  public function internalLinkTag($href, $text, $title = '') {
    global $canAdd;
    $pageid = $this->exists($href);
    if ($this->preview_mode) {
      return sprintf(
        '<a href="javascript:void()"%s%s>%s</a>',
        $pageid !== false?'':' class="new"',
        $title?' title="'.$title.'"':'',
        $text
      );
    }
    if ($pageid === false && !$canAdd) return $text;
    return sprintf(
      '<a href="%s"%s%s>%s</a>',
      $pageid !== false?
        $pageid !== true?
            "index.php?m=documentation&amp;page={$href}&amp;wikipage_id={$pageid}":
            "index.php?m=documentation&amp;page={$href}":            
        "index.php?m=documentation&amp;a=addedit&amp;page={$href}",
      $pageid !== false?'':' class="new"',
      $title?' title="'.$title.'"':'',
      $text
    );
  }

  public function internalLinkEditTag($href, $title = '') {
    global $m, $perms, $AppUI;
    $pageid = $this->exists($href);
    if ($pageid === false) return "";
    if ($this->preview_mode) {
      return sprintf(
        '(<a href="javascript:void()"%s>%s</a>)',
        $title?' title="'.$title.'"':'',
        $AppUI->_('view source')
      );
    }
    $canEdit = $perms->checkModuleItem($m, 'edit', $pageid);
    return sprintf(
      '(<a href="%s"%s>%s</a>)',
      "index.php?m=documentation&amp;page={$href}&amp;wikipage_id={$pageid}&amp;a=addedit",
      $title?' title="'.$title.'"':'',
      $canEdit ? $AppUI->_('edit') : $AppUI->_('view source')
    );
  }

  public function getBasePath() {
    return "index.php?m=documentation&amp;page=";
  }

  public function imageTag($src, $alt, $attrs = '') {
    return sprintf(
      '<img src="%s" alt="%s" %s/>',
      "modules/documentation/images/upload/{$src}",
      $alt,
      $attrs
    );
  }
}

