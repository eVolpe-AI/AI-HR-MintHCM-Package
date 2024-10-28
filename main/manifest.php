<?php

$manifest = array(
    array('acceptable_sugar_versions' => array('')),
    array('acceptable_sugar_flavors' => array('CE')),
    'readme' => '',
    'key' => 'ev',
    'author' => 'eVolpe',
    'description' => 'AI Agent - MintHCM',
    'icon' => '',
    'is_uninstallable' => true,
    'name' => 'MintHCM - AI Agent',
    'published_date' => '2024-09-30 12:00:00',
    'type' => 'module',
    'version' => '4.1.0-1',
    'remove_tables' => 'prompt'
);
$installdefs = array(
    'id' => 'ai_agent',
    'beans' => array(
        array(
            'module' => 'ev_AiConversations',
            'class' => 'ev_AiConversations',
            'path' => 'modules/ev_AiConversations/ev_AiConversations.php',
            'tab' => true,
        ),
        array(
            'module' => 'ev_AiMessages',
            'class' => 'ev_AiMessages',
            'path' => 'modules/ev_AiMessages/ev_AiMessages.php',
            'tab' => false,
        ),
    ),
    'copy' => array(
        array(
            'from' => '<basepath>/api/app/Controllers/AiChatController.php',
            'to' => '../api/app/Controllers/AiChatController.php'
        ),
        array(
            'from' => '<basepath>/api/app/Routes/routes/aiChat.php',
            'to' => '../api/app/Routes/routes/aiChat.php'
        ),
        array(
            'from' => '<basepath>/api/constants/AiChat.php',
            'to' => '../api/constants/AiChat.php'
        ),
        array(
            'from' => '<basepath>/modules/ev_AiConversations',
            'to' => 'modules/ev_AiConversations',
        ),
        array(
            'from' => '<basepath>/modules/ev_AiMessages',
            'to' => 'modules/ev_AiMessages',
        ),
        array(
            'from' => '<basepath>/vue/src/components/MintAiChat',
            'to' => '../vue/src/custom/components/MintAiChat'
        ),
        array(
            'from' => '<basepath>/vue/src/drawers',
            'to' => '../vue/src/custom/drawers'
        ),
        array(
            'from' => '<basepath>/not_upgrade_safe/vue/package.json',
            'to' => '../vue/package.json'
        ),
    ),
    'language' => array(
        array(
            'from' => '<basepath>/ext/language/application/en_us.lang.php',
            'to_module' => 'application',
            'language' => 'en_us',
        ),
    )
);
