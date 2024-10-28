import { computed, ref, watch } from 'vue'
import { defineStore } from 'pinia'
import MintAiChatDefault from './MintAiChatDefault.vue'
import MintAiChatList from './MintAiChatList.vue'
import MintAiChatDetail from './MintAiChatDetail.vue'
import { useAuthStore } from '@/store/auth'
import axios from 'axios'
import { useWebSocketServiceStore, AiChatMessage } from './websocketServiceStore'
import { useLanguagesStore } from '@/store/languages'
import { Settings } from 'luxon'

export interface AiConversation {
    id: string
    name: string
    messages?: AiChatMessage[]
    date_active: string
    date_read?: string
}

export const useMintAiChatStore = defineStore('aiChat', () => {
    const auth = useAuthStore()
    const {
        sendMessage: sendWebSocketMessage,
        messages: wsMessages,
        startWebSocket,
        stopWebSocket,
        isAcceptRejectVisible,
        acceptRequestMessage,
    } = useWebSocketServiceStore()
    const languages = useLanguagesStore()
    const views = {
        default: MintAiChatDefault,
        list: MintAiChatList,
        detail: MintAiChatDetail,
    }
    Settings.defaultLocale = auth.user.preferences.language.split('_')[0] ?? 'en_us'
    if (auth.user?.preferences.timezone) {
        Settings.defaultZone = auth.user.preferences.timezone
    }
    const view = ref<'default' | 'edit' | 'list' | 'detail'>('default')
    const currentView = computed(() => views[view.value] || views['default'])

    const activeConversationId = ref<string | null>(null)
    const activeConversation = computed(() => conversationsList.value.find((c) => c.id === activeConversationId.value))
    const conversationsSearchQuery = ref('')
    const conversations = ref<AiConversation[]>([])
    const defaultText = languages.label('LBL_AI_THINKING')

    const conversationsList = computed(() => {
        return conversations.value.filter(
            (c) =>
                !conversationsSearchQuery.value ||
                c.name.toLowerCase().includes(conversationsSearchQuery.value.toLowerCase()),
        )
    })

    async function fetchConversations() {
        try {
            const response = await axios.get('api/ai-chats')
            conversations.value = response.data
        } catch {
            console.warn('Error fetching conversations.')
        }
    }

    async function fetchConversation(conversationId: string) {
        try {
            const response = await axios.get(`api/ai-chats/${conversationId}`)
            const conversation = response.data
            const existingConversation = conversations.value.find((c) => c.id === conversation.id)
            if (existingConversation) {
                Object.assign(existingConversation, conversation)
            } else {
                conversations.value.push(conversation)
            }
        } catch {
            console.warn('Error fetching conversation.')
        }
    }

    async function sendMessage(conversationId: string, text: string, messageIdAxios: string, messageType = 'input') {
        const trimmedText = text.trim()
        if (!trimmedText) {
            return
        }

        try {
            const response = await axios.post(`api/ai-chats/${conversationId}/messages`, { text })
            const userMessage = response.data

            const conversation = conversations.value.find((c) => c.id === conversationId)
            if (conversation) {
                conversation.messages = [
                    ...(conversation.messages?.filter((msg) => msg.text !== defaultText) || []),
                    userMessage,
                    {
                        id: Date.now().toString(),
                        text: defaultText,
                        date_entered: new Date().toISOString(),
                        author_id: 'ai',
                        conversation_id: conversationId,
                    },
                ]
                conversation.date_active = userMessage.date_entered
            }
        } catch (error) {
            console.warn('Error sending message:', error)
        }

        sendWebSocketMessage(conversationId, trimmedText, messageIdAxios, messageType)
    }

    async function updateDateRead(conversationId: string) {
        try {
            await axios.post(`api/ai-chats/${conversationId}/date-read`)
        } catch {
            console.warn('Error updating date read.')
        }
    }

    async function openDetailView(conversationId: string) {
        const conversation = conversations.value.find((c) => c.id === conversationId)
        if (conversation) {
            await updateDateRead(conversationId)
            conversation.date_read = new Date().toISOString()
        }
        activeConversationId.value = conversationId
        view.value = 'detail'
        fetchConversation(conversationId)
        startWebSocket(conversationId)
    }

    function openPrivateConversation(conversation: AiConversation) {
        const conv = conversations.value.find((c) => c.id === conversation.id)
        if (conv) {
            view.value = 'detail'
            activeConversationId.value = conv.id
        } else {
            const newConv: AiConversation = {
                id: Date.now().toString(),
                date_active: new Date().toISOString(),
                date_read: new Date().toISOString(),
                messages: [],
                name: conversation.name,
            }
            conversations.value.push(newConv)
            view.value = 'detail'
            activeConversationId.value = newConv.id
        }
    }

    async function createNewConversation() {
        try {
            const response = await axios.post('api/ai-chats')
            const newConversation = response.data
            conversations.value.push(newConversation)
            openDetailView(newConversation.id)
        } catch {
            console.warn('Error creating new conversation.')
        }
    }

    async function deleteConversation(conversationId: string) {
        try {
            await axios.delete(`api/ai-chats/${conversationId}`)
            conversations.value = conversations.value.filter((c) => c.id !== conversationId)
        } catch {
            console.warn('Error deleting conversation.')
        }
    }

    const unreadConversationsCount = computed(() => {
        return conversations.value.filter((c) => {
            const lastMessage = c.messages?.[c.messages.length - 1]
            if (!lastMessage) return false
            if (!c.date_read) return true
            return lastMessage.author_id !== auth.user?.id && lastMessage.date_entered > c.date_read
        }).length
    })

    watch(view, (newView) => {
        if (newView === 'detail' && activeConversationId.value) {
            startWebSocket(activeConversationId.value)
        } else {
            stopWebSocket()
        }
    })

    watch(
        wsMessages,
        (newMessages) => {
            if (activeConversationId.value) {
                const conversation = conversations.value.find((c) => c.id === activeConversationId.value)
                if (conversation) {
                    const filteredMessages =
                        conversation.messages?.filter((msg) => !msg.text.startsWith(defaultText)) || []
                    const uniqueMessages = newMessages.filter(
                        (msg) => !conversation.messages?.some((existingMsg) => existingMsg.id === msg.id),
                    )
                    conversation.messages = [...filteredMessages, ...uniqueMessages]
                    conversations.value = [...conversations.value]
                }
            }
        },
        { deep: true },
    )

    return {
        view,
        currentView,
        openDetailView,
        startWebSocket,
        stopWebSocket,
        activeConversationId,
        activeConversation,
        conversationsSearchQuery,
        conversations,
        conversationsList,
        unreadConversationsCount,
        openPrivateConversation,
        fetchConversations,
        fetchConversation,
        sendMessage,
        sendWebSocketMessage,
        createNewConversation,
        deleteConversation,
        isAcceptRejectVisible,
        acceptRequestMessage,
    }
})
