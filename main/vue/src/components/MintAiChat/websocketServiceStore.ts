import { ref } from 'vue'
import axios from 'axios'
import { defineStore } from 'pinia'
import { useLanguagesStore } from '@/store/languages'

export interface AiChatMessage {
    id: string
    text: string
    date_entered: string
    author_id: string
    conversation_id?: string | null
}

interface LlmStartMessage {
    type: 'llm_start'
    messageId: string
    conversationId: string
}

interface LlmTextMessage {
    type: 'llm_text'
    content: string
    messageId: string
    conversationId: string
}

interface LlmEndMessage {
    type: 'llm_end'
    messageId: string
    conversationId: string
}

interface AgentStartMessage {
    type: 'agent_start'
}

interface AgentEndMessage {
    type: 'agent_end'
}

interface ToolStartMessage {
    type: 'tool_start'
    tool_input: string
    tool_name: string
}

interface ToolEndMessage {
    type: 'tool_end'
    tool_name: string
}

interface AcceptRequestMessage {
    type: 'accept_request'
    tool_name: string
    tool_input: string
}

interface ToolRejectMessage {
    type: 'tool_reject'
    content?: string
}

interface ToolConfirmMessage {
    type: 'tool_confirm'
}

type WebSocketMessage =
    | LlmStartMessage
    | LlmTextMessage
    | LlmEndMessage
    | AgentStartMessage
    | AgentEndMessage
    | ToolStartMessage
    | ToolEndMessage
    | AcceptRequestMessage
    | ToolRejectMessage
    | ToolConfirmMessage

export const useWebSocketServiceStore = defineStore('aiChatWebSocket', () => {
    const languages = useLanguagesStore()
    const userId = ref<string | null>(null)
    const userName = ref<string | null>(null)
    const serviceUrl = ref<string | null>(null)

    const socket = ref<WebSocket | null>(null)
    const isAcceptRejectVisible = ref(false)
    const acceptRequestMessage = ref('')
    const messages = ref<AiChatMessage[]>([])
    const tempMessageChunks = new Map<string, string[]>()
    const activeMessageId = ref<string | null>(null)
    const currentConversationId = ref<string | null>(null)

    async function initConfig() {
        try {
            const response = await axios.get('api/get-agent-config')
            const data = response.data
            serviceUrl.value = data?.service_domain
            userId.value = data?.user_id
            userName.value = data?.user_name
            if (!serviceUrl.value) {
                console.error('Agent Service URL is not set.')
            }
        } catch (error) {
            console.error('Error fetching Ai Agent config:', error)
        }
    }

    async function initWebSocket(conversationId: string) {
        if (!userId.value) {
            console.error('User ID is not set.')
            return
        }
        if (socket.value) {
            socket.value.close()
        }
        socket.value = new WebSocket(
            `wss://${serviceUrl.value}/${userName.value}/${conversationId}/${userId.value}?advanced=true`,
        )

        socket.value.onmessage = (event) => {
            try {
                const data: WebSocketMessage = JSON.parse(event.data)

                switch (data.type) {
                    case 'llm_text':
                        handleLlmText(data)
                        break
                    case 'llm_start':
                        handleLlmStart(data)
                        break
                    case 'llm_end':
                        handleLlmEnd(data)
                        break
                    case 'accept_request':
                        handleAcceptRequest(data)
                        break
                    default:
                        console.warn('Unknown message type:', data.type)
                }
            } catch (error) {
                console.error('Error parsing WebSocket message:', error)
            }
        }

        socket.value.onclose = () => {
            socket.value = null
        }

        currentConversationId.value = conversationId
    }

    function generateMessageId(): string {
        return `msg-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
    }

    function handleAcceptRequest(data: AcceptRequestMessage) {
        isAcceptRejectVisible.value = true
        acceptRequestMessage.value = languages.label('LBL_TOOL_ACCEPT', '', { tool_name: data.tool_name })
    }

    function sendMessage(conversationId: string, text: string, messageIdAxios: string, messageType = 'input') {
        if (socket.value && socket.value.readyState === WebSocket.OPEN) {
            const message = {
                type: messageType,
                content: text,
                conversationId,
                messageId: messageIdAxios,
            }

            socket.value.send(JSON.stringify(message))
            currentConversationId.value = conversationId
        } else {
            console.warn('WebSocket is not open. Message not sent.')
        }
    }

    async function handleLlmStart(data: LlmStartMessage) {
        const localMessageId = generateMessageId()
        activeMessageId.value = localMessageId
        tempMessageChunks.set(localMessageId, [])

        const conversationId = currentConversationId.value

        if (!conversationId) {
            console.error('Conversation ID is missing.')
            return
        }
    }

    function handleLlmText(data: LlmTextMessage) {
        if (!activeMessageId.value) {
            console.error('No active message ID.')
            return
        }

        const conversationId = currentConversationId.value
        if (!conversationId) {
            console.error('Conversation ID is missing.')
            return
        }

        const messageContent = data.content

        const currentMessageId = activeMessageId.value
        if (!tempMessageChunks.has(currentMessageId)) {
            tempMessageChunks.set(currentMessageId, [])
        }

        tempMessageChunks.get(currentMessageId)?.push(messageContent)

        const completeContent = (tempMessageChunks.get(currentMessageId) || []).join('')
        const existingMessageIndex = messages.value.findIndex((msg) => msg.id === currentMessageId)

        if (existingMessageIndex >= 0) {
            messages.value[existingMessageIndex].text = completeContent
        } else {
            messages.value.push({
                id: currentMessageId,
                text: completeContent,
                date_entered: new Date().toISOString(),
                author_id: 'ai',
                conversation_id: conversationId,
            })
        }
    }

    async function handleLlmEnd(data: LlmEndMessage) {
        if (!activeMessageId.value) {
            console.error('No active message ID.')
            return
        }

        const currentMessageId = activeMessageId.value
        const conversationId = currentConversationId.value

        if (!conversationId) {
            console.error('Conversation ID is missing.')
            return
        }

        const chunks = tempMessageChunks.get(currentMessageId)
        if (!chunks) {
            console.warn('Chunks not found for messageId:', currentMessageId)
            return
        }

        const completeContent = chunks.join('').trim()

        if (!completeContent) {
            console.warn('Complete message is empty, not saving it.')
            return
        }

        try {
            await axios.post(`api/ai-chats/${conversationId}/messages`, {
                text: completeContent,
                author_id: 'ai',
            })
        } catch (error) {
            console.error('Error updating message:', error)
        }

        activeMessageId.value = null
    }

    function startWebSocket(conversationId: string) {
        if (!socket.value) {
            initWebSocket(conversationId)
        }
    }

    function stopWebSocket() {
        if (socket.value) {
            socket.value.close()
            socket.value = null
        }
    }

    return {
        initConfig,
        messages,
        startWebSocket,
        stopWebSocket,
        sendMessage,
        isAcceptRejectVisible,
        acceptRequestMessage,
        handleAcceptRequest,
    }
})
