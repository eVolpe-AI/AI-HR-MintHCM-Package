<?php

$dictionary['ev_AiMessages'] = array(
    'table' => 'ev_aimessages',
    'audited' => true,
    'inline_edit' => true,
    'duplicate_merge' => true,
    'fields' => array(
        'conversation_id' => array(
            'name' => 'conversation_id',
            'vname' => 'LBL_CONVERSATION_ID',
            'type' => 'id',
            'importable' => 'true',
            'reportable' => true,
            'audited' => true,
        ),
        'text' => array(
            'name' => 'text',
            'vname' => 'LBL_TEXT',
            'type' => 'text',
            'importable' => 'true',
            'audited' => true,
            'reportable' => true,
        ),
        'author_id' => array(
            'name' => 'author_id',
            'vname' => 'LBL_AUTHOR_ID',
            'type' => 'id',
            'importable' => 'true',
            'reportable' => true,
            'audited' => true,
        ),
        'conversation' => array(
            'name' => 'conversation',
            'type' => 'link',
            'relationship' => 'ai_conversations_ai_messages',
            'source' => 'non-db',
            'module' => 'ev_AiConversations',
            'bean_name' => 'ev_AiConversations',
            'vname' => 'LBL_CONVERSATION',
            'id_name' => 'conversation_id',
            'link-type' => 'one',
        ),
        'conversation_name' => array(
            'required' => true,
            'name' => 'conversation_name',
            'type' => 'relate',
            'source' => 'non-db',
            'vname' => 'LBL_CONVERSATION_NAME',
            'id_name' => 'conversation_id',
            'link' => 'conversation',
            'table' => 'ev_aiconversations',
            'module' => 'ev_AiConversations',
            'rname' => 'name',
        ),
    ),
    'relationships' => array(
        'ai_conversations_ai_messages' => array(
            'lhs_module' => 'ev_AiConversations',
            'lhs_table' => 'ev_aiconversations',
            'lhs_key' => 'id',
            'rhs_module' => 'ev_AiMessages',
            'rhs_table' => 'ev_aimessages',
            'rhs_key' => 'conversation_id',
            'relationship_type' => 'one-to-many',
        ),
    ),
    'optimistic_locking' => true,
    'unified_search' => true,
);

if (!class_exists('VardefManager')) {
    require_once 'include/SugarObjects/VardefManager.php';
}
VardefManager::createVardef('ev_AiMessages', 'ev_AiMessages', array('basic', 'assignable', 'security_groups'));

