<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

if (ACLController::checkAccess('ev_AiConversations', 'edit', true)) {
    $module_menu[] = array('index.php?module=ev_AiConversations&action=EditView&return_module=ev_AiConversations&return_action=DetailView', $mod_strings['LNK_NEW_RECORD'], 'Add', 'ev_AiConversations');
}
if (ACLController::checkAccess('ev_AiConversations', 'list', true)) {
    $module_menu[] = array('index.php?module=ev_AiConversations&action=index&return_module=ev_AiConversations&return_action=DetailView', $mod_strings['LNK_LIST'], 'View', 'ev_AiConversations');
}
if (ACLController::checkAccess('ev_AiConversations', 'import', true)) {
    $module_menu[] = array('index.php?module=Import&action=Step1&import_module=ev_AiConversations&return_module=ev_AiConversations&return_action=index', $app_strings['LBL_IMPORT'], 'Import', 'ev_AiConversations');
}
