<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings, $app_strings, $sugar_config;

if (ACLController::checkAccess('ev_AiMessages', 'edit', true)) {
    $module_menu[] = array('index.php?module=ev_AiMessages&action=EditView&return_module=ev_AiMessages&return_action=DetailView', $mod_strings['LNK_NEW_RECORD'], 'Add', 'ev_AiMessages');
}
if (ACLController::checkAccess('ev_AiMessages', 'list', true)) {
    $module_menu[] = array('index.php?module=ev_AiMessages&action=index&return_module=ev_AiMessages&return_action=DetailView', $mod_strings['LNK_LIST'], 'View', 'ev_AiMessages');
}
if (ACLController::checkAccess('ev_AiMessages', 'import', true)) {
    $module_menu[] = array('index.php?module=Import&action=Step1&import_module=ev_AiMessages&return_module=ev_AiMessages&return_action=index', $app_strings['LBL_IMPORT'], 'Import', 'ev_AiMessages');
}
