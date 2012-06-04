<?php /* $Id$ $URL$ */

if (!defined('W2P_BASE_DIR')) {
    die('You should not access this file directly.');
}

/*
* Name:      Documentation
* Directory: documentation
* Version:   0.2
* Class:     CDocumentation
* UI Name:   Documentation
* UI Icon:
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Documentation';
$config['mod_version'] = '0.2';
$config['mod_directory'] = 'documentation';
$config['mod_setup_class'] = 'CSetupDocumentation';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Documentation';
$config['mod_ui_icon'] = 'documentation.png';
$config['mod_description'] = $AppUI->_('A module for documentation of project');
$config['permissions_item_table'] = 'wikipages';
$config['permissions_item_field'] = 'wikipage_id';
$config['permissions_item_label'] = 'wikipage_name';
$config['mod_main_class'] = 'CDocumentation';
$config['mod_config'] = true;

if ($a == 'setup') {
    echo w2PshowModuleConfig($config);
}

class CSetupDocumentation {

    /*
     *   Install routine
     */
    public function install() {
        global $AppUI;
        
        $q = new w2p_Database_Query();
        $q->createTable('wikipages');
        $q->createDefinition('(
            wikipage_id int(10) unsigned NOT NULL auto_increment,
            wikipage_date datetime NOT NULL default \'0000-00-00 00:00:00\',      
            wikipage_user int(10) NOT NULL default \'0\',
            wikipage_start tinyint(1) NOT NULL default \'0\',
            wikipage_locked tinyint(1) NOT NULL default \'0\',
            wikipage_parser varchar(10),
            wikipage_namespace varchar(50) NOT NULL DEFAULT \'\',
            wikipage_name varchar(255),
            wikipage_lang varchar(5) NOT NULL default \'en\',
            wikipage_title varchar(255),
            wikipage_content text,
            wikipage_categorylinks text,
            PRIMARY KEY  (wikipage_id),
            UNIQUE KEY wikipage_name_key (wikipage_namespace, wikipage_name),
            INDEX (wikipage_namespace, wikipage_start)
        ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
        ');
        $q->exec();
        $q->clear();
        if (db_error()) return false;
        $perms = $AppUI->acl();
        return $perms->registerModule('Documentation', 'documentation');
    }

    /*
     * Removal routine
     */
    public function remove() {
        global $AppUI;
        $q = new w2p_Database_Query;
        $q->dropTable('wikipages');
        $q->exec();
        $q->clear();
        if (db_error()) return false;
        $perms = $AppUI->acl();
        return $perms->unregisterModule('documentation');
    }

    /*
     * Upgrade routine
     */
    public function upgrade()
    {
        switch ($old_version) {
            case '0.1':
                $q = new w2p_Database_Query();
                $q->alterTable('wikipages');
                $q->createDefinition('wikipage_categorylinks text after wikipage_content');
                $q->exec();
                return true;
            default:
        }
        return false;
    }

    /*
     * configure routine
     */
    public function configure() {
        global $AppUI;
        $AppUI->redirect('m=documentation&a=configure');
        return true;
    }
}

