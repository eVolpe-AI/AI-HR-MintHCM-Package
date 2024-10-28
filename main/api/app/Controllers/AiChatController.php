<?php

namespace MintHCM\Api\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use MintHCM\Data\BeanFactory;

class AiChatController {

    public function getConfig(Request $request, Response $response, array $args) {
        $config = include 'constants/AiChat.php';
        $response->getBody()->write(json_encode($config));   
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUserData(Request $request, Response $response, array $args): Response {
        try {
            global $current_user;

            if ($current_user) {
                $userData = [
                    'userId' => $current_user->id,
                    'userName' => $current_user->user_name,
                ];
                $response->getBody()->write(json_encode($userData));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                return $this->notFoundResponse($response);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }


    public function getAllConversations(Request $request, Response $response, array $args): Response {
        try {
            $conversations = $this->fetchConversations();
            $response->getBody()->write(json_encode($conversations));   
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }

    public function getLastConversation(Request $request, Response $response, array $args): Response {
        try {
            $conversations = $this->fetchConversations(true);
            
            if (empty($conversations)) {
                return $this->notFoundResponse($response);
            }
        
            $response->getBody()->write(json_encode($conversations[0]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }

    public function getConversation(Request $request, Response $response, array $args): Response {
        try {
            $conversationId = $args['conversation_id'] ?? null;
            $conversation = $this->fetchConversationById($conversationId);

            if ($conversation) {
                $response->getBody()->write(json_encode($conversation));
            } else {
                return $this->notFoundResponse($response);
            }
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }

    public function getMessages(Request $request, Response $response, array $args): Response {
        try {
            $conversationId = $args['conversation_id'] ?? null;
            $messages = $this->fetchMessagesByConversationId($conversationId);

            if ($messages) {
                $response->getBody()->write(json_encode($messages));
            } else {
                return $this->notFoundResponse($response);
            }
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }

    public function sendMessage(Request $request, Response $response, array $args): Response {
        
        try {
            $conversationId = $args['conversation_id'] ?? null;
            $data = $request->getParsedBody();
            
            $text = $data['text'] ?? '';
            global $current_user;
            $authorId = $data['author_id'] ?? ($current_user && $current_user->id ? $current_user->id : 'ai');
            
            $authorId = (string) $authorId;
            
            $newMessage = $this->addMessageToConversation($conversationId, $text, $authorId);
            
            if ($newMessage) {
                $response->getBody()->write(json_encode($newMessage));
            } else {
                return $this->notFoundResponse($response);
            }
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }

    public function createNewConversation(Request $request, Response $response, array $args): Response {
        global $current_user;

        try {
            $conversation = BeanFactory::newBean('ev_AiConversations');
            $conversation->name = 'New Conversation';
            $conversation->date_active = date('Y-m-d H:i:s');
            $conversation->user_id = $current_user->id;
            $conversation->save();

            $response->getBody()->write(json_encode($conversation->toArray()));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }

    public function deleteConversation(Request $request, Response $response, array $args): Response {
        try {
            $conversationId = $args['conversation_id'] ?? null;

            if ($conversationId) {
                $conversation = BeanFactory::newBean('ev_AiConversations');
                $conversation->mark_deleted($conversationId);
                    return $response->withStatus(204);
            }
            
            return $this->notFoundResponse($response);  
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }   
    
    public function updateDateRead(Request $request, Response $response, array $args): Response {
        try {
            $conversationId = $args['conversation_id'] ?? null;
            if ($conversationId) {
                $dateRead = date('Y-m-d H:i:s');
                $this->updateConversationDateRead($conversationId, $dateRead);
                return $response->withStatus(204);
            } else {
                return $this->notFoundResponse($response);  
            }
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }

    public function updateMessage(Request $request, Response $response, array $args): Response {
        try {
            $conversationId = $args['conversation_id'] ?? null;
            $messageId = $args['message_id'] ?? null;
            $data = $request->getParsedBody();
            
            $text = $data['text'] ?? '';
            
            if ($conversationId && $messageId && $text) {
                $updatedMessage = $this->editMessage($conversationId, $messageId, $text);
                
                if ($updatedMessage) {
                    $response->getBody()->write(json_encode($updatedMessage));
                } else {
                    return $this->notFoundResponse($response);
                }
            } else {
                return $this->badRequestResponse($response);
            }
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return $this->errorResponse($response, $e);
        }
    }

    private function fetchConversations($get_newest_one = false) {
        $conversations = [];
        $conversation_beans = $get_newest_one
                                ? BeanFactory::getBean('ev_AiConversations')->get_full_list('date_entered DESC', '', '', 1)
                                : BeanFactory::getBean('ev_AiConversations')->get_full_list('date_entered DESC');
        foreach ($conversation_beans as $bean) {
            if (empty($bean->id)) {
                continue;
            }
            $conversation = $bean->toArray();
            $conversation['messages'] = $this->fetchMessagesByConversationId($bean->id);
            $conversations[] = $conversation;
        }
        return $conversations;
    }

    private function fetchConversationById($id) {
        $conversation = BeanFactory::getBean('ev_AiConversations', $id);
        if (!empty($conversation->id)) {
            $conversationArr = $conversation->toArray();
            $conversationArr['messages'] = $this->fetchMessagesByConversationId($conversation->id);
            return $conversationArr;
        }
        return null;
    }

    private function fetchMessagesByConversationId($conversationId) {
        $messages = [];
        $messageBeanList = BeanFactory::getBean('ev_AiMessages')->get_full_list('date_entered ASC', "conversation_id = '$conversationId'");
        foreach ($messageBeanList as $bean) {
            if(empty($bean->id)) {
                continue;
            }
            $messageArray = $bean->toArray();
            $messages[] = $messageArray;
        }
        return $messages;
    }
    

    private function addMessageToConversation($conversationId, $text, $authorId) {
        $message = BeanFactory::newBean('ev_AiMessages');
        $message->conversation_id = $conversationId;
        $message->text = $text;
        $message->author_id = $authorId;
        $message->save();

        $conversation = BeanFactory::getBean('ev_AiConversations', $conversationId);
        if(empty($conversation->id)) {
            return null;
        }
        $conversation->save();

        return $message->toArray();
    }

    private function updateConversationDateRead($conversationId, $dateRead) {
        $conversation = BeanFactory::getBean('ev_AiConversations', $conversationId);
        if (!empty($conversation->id)) {
            $conversation->date_read = $dateRead;
            $conversation->save();
        }
    }

    private function editMessage($conversationId, $messageId, $text) {
        $message = BeanFactory::getBean('ev_AiMessages', $messageId);
        if (!empty($message->id) && $message->conversation_id === $conversationId) {
            $message->text = $text;
            $message->save();
            return $message->toArray();
        }
        return null;
    }

    private function errorResponse(Response $response, \Exception $e): Response {
        $response->getBody()->write(json_encode(['message' => 'Internal Server Error', 'error' => $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }

    private function badRequestResponse(Response $response): Response {
        $response->getBody()->write(json_encode(['message' => 'Bad request']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    private function notFoundResponse(Response $response): Response {
        $response->getBody()->write(json_encode(['message' => 'Conversation not found']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
}
