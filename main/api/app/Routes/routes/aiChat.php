<?php

use MintHCM\Api\Controllers\AiChatController;
use MintHCM\Api\Middlewares\Params\ParamTypes\StringType;

$routes = array(
    "get_all_conversations" => array(
        "method" => "GET",
        "path" => "/ai-chats",
        "class" => AiChatController::class,
        "function" => "getAllConversations",
        "desc" => "Get all AI conversations",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
    "get_last_conversation" =>array(
        "method" => "GET",
        "path" => "/ai-chats/last",
        "class" => AiChatController::class,
        "function" => "getLastConversation",
        "desc" => "Get the last AI conversation",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
    "create_new_conversation" => array(
        "method" => "POST",
        "path" => "/ai-chats",
        "class" => AiChatController::class,
        "function" => "createNewConversation",
        "desc" => "Create a new AI conversation",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
    "delete_conversation" => array(
        "method" => "DELETE",
        "path" => "/ai-chats/{conversation_id}",
        "class" => AiChatController::class,
        "function" => "deleteConversation",
        "desc" => "Delete a specific AI conversation",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(
            "conversation_id" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Conversation ID",
                "example" => "ai1",
            ),
        ),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
    "get_conversation" => array(
        "method" => "GET",
        "path" => "/ai-chats/{conversation_id}",
        "class" => AiChatController::class,
        "function" => "getConversation",
        "desc" => "Get a specific AI conversation",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(
            "conversation_id" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Conversation ID",
                "example" => "ai1",
            ),
        ),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
    "update_date_read" => array(
        "method" => "POST",
        "path" => "/ai-chats/{conversation_id}/date-read",
        "class" => AiChatController::class,
        "function" => "updateDateRead",
        "desc" => "Update the date read for a specific AI conversation",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(
            "conversation_id" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Conversation ID",
                "example" => "ai1",
            ),
        ),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
    "get_messages" => array(
        "method" => "GET",
        "path" => "/ai-chats/{conversation_id}/messages",
        "class" => AiChatController::class,
        "function" => "getMessages",
        "desc" => "Get messages for a specific AI conversation",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(
            "conversation_id" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Conversation ID",
                "example" => "ai1",
            ),
        ),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
    "send_message" => array(
        "method" => "POST",
        "path" => "/ai-chats/{conversation_id}/messages",
        "class" => AiChatController::class,
        "function" => "sendMessage",
        "desc" => "Send a message to a specific AI conversation",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(
            "conversation_id" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Conversation ID",
                "example" => "ai1",
            ),
        ),
        "queryParams" => array(),
        "bodyParams" => array(
            "text" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Message text",
                "example" => "Hello, how can I help you?",
            ),
            "author_id" => array(
                "type" => StringType::class,
                "required" => false,
                "desc" => "Author ID",
                "example" => "user123",
            ),
        ),
    ),
    "update_message" => array(
        "method" => "PUT",
        "path" => "/ai-chats/{conversation_id}/messages/{message_id}",
        "class" => AiChatController::class,
        "function" => "updateMessage",
        "desc" => "Update a specific message in a conversation",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(
            "conversation_id" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Conversation ID",
                "example" => "ai1",
            ),
            "message_id" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Message ID",
                "example" => "123",
            ),
        ),
        "queryParams" => array(),
        "bodyParams" => array(
            "text" => array(
                "type" => StringType::class,
                "required" => true,
                "desc" => "Updated message text",
                "example" => "Final message text",
            ),
        ),
    ),
    "get_user_data" => array(
        "method" => "GET",
        "path" => "/user-data",
        "class" => AiChatController::class,
        "function" => "getUserData",
        "desc" => "Get user data",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
    "get_config" => array(
        "method" => "GET",
        "path" => "/get-agent-config",
        "class" => AiChatController::class,
        "function" => "getConfig",
        "desc" => "Get AI Agent config",
        "options" => array(
            "auth" => true,
        ),
        "pathParams" => array(),
        "queryParams" => array(),
        "bodyParams" => array(),
    ),
);
