<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

##
## Documentation module
## (c) Copyright 2011 Jacques Archimède (Eureka)
##

$wikipage_id = (int) w2PgetParam($_REQUEST, 'wikipage_id', 0);
$page = w2PgetParam($_REQUEST, 'page', "");
$wikipage_namespace = "";

global $AppUI;
// check permissions for this module
$canView = canView('documentation');
$canAdd = canAdd('documentation');
$canEditWiki = canEdit('documentation', $wikipage_id);
$canDeleteWiki = canDelete('documentation', $wikipage_id);

if (!$canView) {
    $AppUI->redirect('m=public&a=access_denied');
}

$q = new w2p_Database_Query;
if (!$page && $wikipage_id == 0 && isset($_GET['project_id'])) {
    $q->addTable("projects");
    $q->addQuery("project_short_name");
    $q->addWhere('project_id =' . $_GET['project_id']);
    $project = $q->loadHash();
    $q->clear();
    $wikipage_namespace = 
        isset($project['project_short_name']) ? 
            $project['project_short_name'] : 
            $AppUI->_('Project').$_GET['project_id'];
    $wikipage_namespace = str_replace(
        array(' ', '<', '[', '(', '"', ';'), 
        array(' ', ' ', ' ', ' ', ' ', ' '), 
        $wikipage_namespace
    );
}
if (!$page && !$wikipage_namespace && $wikipage_id == 0) {
    $project = new CProject();
    $extra = array('where' => 'project_active = 1');
    $allowedprojects = $project->getAllowedRecords(
        $AppUI->user_id,
        'projects.project_id,project_short_name, project_name',
        'project_name',
        null,
        $extra,
        'projects'
    );
    $q->addTable("projects");
    $q->addQuery("projects.project_id, CONCAT(company_name, ': ', project_name)");
    $q->addJoin("companies", "co", "co.company_id = project_company");
    $projectslist = $q->loadHashList();
    $q->clear();
    $projects = array();
    foreach ($allowedprojects as $prj_id => $prj_name) {
        $wikipage_namespace = $prj_name ? $prj_name : $AppUI->_('Project').$prj_id;
        $wikipage_namespace = str_replace(
            array(' ', '<', '[', '(', '"', ';'),
            array('_', '_', '_', '_', '_', '_'),
            $wikipage_namespace
        );
        $projects[$wikipage_namespace.":"] = $projectslist[$prj_id];
    }
    asort($projects);
    $projects = arrayMerge(array('0' => $AppUI->_('(None)', UI_OUTPUT_RAW)), $projects);
    include_once (W2P_BASE_DIR . "/modules/documentation/views/projects.php");
} else {
    if ($wikipage_id > 0) {
        $wiki = new CWiki();
        $wikipage = $wiki->getPage($wikipage_id);
        $wikipage_namespace = $wiki->getNamespace();
        $page = $wiki->pagename();
    } else {
        if ($page) {
            list($wikipage_namespace, $page) = explode(":", $page);
        }
        if (!$page || $wikipage_namespace != "Special") {
            $wiki = new CWiki($wikipage_namespace);
            if ($page) {
                list($page, $view) = explode("/", $page);
                $wikipage = $wiki->loadPage($wikipage_namespace.':'.$page);
                $wikipage_id = $wikipage->getWikipageId();
            } else {
                $wikipage = $wiki->loadStartPage();
                $page = $wiki->pagename();
                $wikipage_id = $wikipage->getWikipageId();
            }
        }
    }
    if ($wikipage_namespace == "Special") {
        $page = strtolower($page);
        if (file_exists(W2P_BASE_DIR . "/modules/documentation/special/".$page.".php")) {
            include_once (W2P_BASE_DIR . "/modules/documentation/special/".$page.".php");
        } else {
            include_once (W2P_BASE_DIR . "/modules/documentation/special/unknown.php");
        }
    } elseif ($wikipage_namespace == "Image") {
        include_once (W2P_BASE_DIR . "/modules/documentation/views/image.php");
    } elseif ($wikipage_id == 0 && $canAdd) { // No start page for this project
        require_once W2P_BASE_DIR . "/modules/documentation/config.php";
        $canDelete = $canDeleteWiki;
        $wikipage = array(
            'wikipage_parser' => $WIKI_CONFIG['default_parser'],
            'wikipage_lang' => $WIKI_CONFIG['default_lang'],
            'wikipage_start' => 1,
            'wikipage_namespace' => $wikipage_namespace,
            'wikipage_name' => $wiki->pagename(),
            'wikipage_title' => str_replace("_", " ", $wiki->pagename()),
            'wikipage_content' => "",
        );
        include_once (W2P_BASE_DIR . "/modules/documentation/views/addedit.form.php");
    }  else {
        if (isset($_GET['suppressHeaders'])) {
            if ($view == "PDF" ) {
                include_once (W2P_BASE_DIR . "/modules/documentation/views/pdf.php");
            } else {
                include_once (W2P_BASE_DIR . "/modules/documentation/views/fullpage.php");
            }
        } else {
            include_once (W2P_BASE_DIR . "/modules/documentation/views/page.php");
        }
    }
}