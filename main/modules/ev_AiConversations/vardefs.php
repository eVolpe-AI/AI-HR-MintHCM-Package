<?php
$dictionary['ev_AiConversations'] = array(
    'table' => 'ev_aiconversations',
    'audited' => true,
    'inline_edit' => true,
    'duplicate_merge' => true,
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'len' => 255,
            'importable' => 'true',
            'audited' => true,
            'reportable' => true,
        ),
        'user_id' => array(
            'name' => 'user_id',
            'vname' => 'LBL_USER_ID',
            'type' => 'id',
            'importable' => 'true',
            'reportable' => true,
            'audited' => true,
        ),
        'date_read' => array(
            'name' => 'date_read',
            'vname' => 'LBL_DATE_READ',
            'type' => 'datetime',
            'importable' => 'true',
            'audited' => true,
            'reportable' => true,
        ),
        'date_active' => array(
            'name' => 'date_active',
            'vname' => 'LBL_DATE_ACTIVE',
            'type' => 'datetime',
            'importable' => 'true',
            'audited' => true,
            'reportable' => true,
        ),
        'messages' => array(
            'name' => 'messages',
            'type' => 'link',
            'relationship' => 'ai_conversations_ai_messages',
            'source' => 'non-db',
            'module' => 'ev_AiMessages',
            'bean_name' => 'ev_AiMessages',
            'vname' => 'LBL_MESSAGES',
            'side' => 'left',
            'link-type' => 'many',
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
VardefManager::createVardef('ev_AiConversations', 'ev_AiConversations', array('basic', 'assignable', 'security_groups'));

