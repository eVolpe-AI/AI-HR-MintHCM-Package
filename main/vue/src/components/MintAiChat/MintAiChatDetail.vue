<template>
    <div class="mint-ai-chat-detail">
        <div class="mint-ai-chat-detail-header">
            <MintButton variant="nav" icon="mdi-arrow-left" @click="goBackToDefaultView" />
            <v-icon icon="mdi-robot" />
            <div class="mint-ai-chat-detail-header-name">{{ chat.activeConversation?.name || 'AI Chat' }}</div>
            <v-icon icon="mdi-dots-vertical" />
        </div>
        <div class="mint-ai-chat-detail-content" ref="messagesContent">
            <template v-if="chat.activeConversation">
                <template v-if="chat.activeConversation.messages && chat.activeConversation.messages.length">
                    <div
                        v-for="message in chat.activeConversation.messages"
                        :key="message.id"
                        :class="{
                            'mint-ai-chat-message': true,
                            'mint-ai-chat-message-owner': message.author_id === auth.user?.id,
                        }"
                    >
                        <div class="mint-ai-chat-message-content">
                            <p>{{ message.text }}</p>
                        </div>
                        <div class="mint-ai-chat-message-footer">
                            <span>{{ toRelativeDate(message.date_entered) }}</span>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <p>{{ languages.label('LBL_NO_MESSAGES_TO_DISPLAY') }}</p>
                </template>
            </template>
            <template v-else>
                <p>{{ languages.label('LBL_LOADING') }}</p>
            </template>
            <MintAiChatConfirmationModal v-if="webSocketStore.isAcceptRejectVisible" />
        </div>
        <div class="mint-ai-chat-detail-footer">
            <v-textarea
                class="mint-ai-chat-detail-input"
                v-model="messageText"
                variant="plain"
                hide-details
                placeholder="Message"
                auto-grow
                rows="1"
                max-rows="3"
                @keydown.enter.prevent="sendMessage"
            />
            <MintButton variant="nav" icon="mdi-send" @click="sendMessage" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, nextTick, onMounted, watch, onUnmounted } from 'vue'
import { DateTime } from 'luxon'
import { useMintAiChatStore } from './MintAiChatStore'
import MintButton from '@/components/MintButtons/MintButton.vue'
import { useAuthStore } from '@/store/auth'
import { useWebSocketServiceStore } from './websocketServiceStore'
import { useLanguagesStore } from '@/store/languages'
import MintAiChatConfirmationModal from './MintAiChatConfirmationModal.vue'

const chat = useMintAiChatStore()
const auth = useAuthStore()
const messageText = ref('')
const messagesContent = ref<HTMLDivElement | null>(null)
const webSocketStore = useWebSocketServiceStore()
const languages = useLanguagesStore()

async function sendMessage() {
    if (!messageText.value.trim() || !chat.activeConversation || !auth.user) {
        return
    }

    const text = messageText.value.trim()
    const conversationId = chat.activeConversation.id
    const messageId = Date.now().toString()

    try {
        await chat.sendMessage(conversationId, text, messageId)
        messageText.value = ''
        scrollToBottom()
    } catch (error) {
        console.error('Error sending message:', error)
    }
}

function scrollToBottom() {
    nextTick(() => {
        if (messagesContent.value) {
            messagesContent.value.scrollTop = messagesContent.value.scrollHeight
        }
    })
}


function toRelativeDate(date: string) {
    return DateTime.fromSQL(date, { zone: 'utc' }).toRelative()
}

function goBackToDefaultView() {
    chat.view = 'default'
}

watch(
    () => chat.activeConversation?.messages,
    () => {
        scrollToBottom()
    },
    { deep: true },
)

onMounted(() => {
    if (chat.activeConversationId) {
        chat.startWebSocket(chat.activeConversationId)
    }
})

onUnmounted(() => {
    chat.stopWebSocket()
})
</script>

<style scoped lang="scss">
.mint-ai-chat-detail {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.mint-ai-chat-detail-header {
    display: flex;
    gap: 16px;
    align-items: center;
    padding: 8px 16px 8px 16px;
    border-bottom: thin solid #0002;
    font-weight: 600;
    color: rgb(var(--v-theme-secondary));
    font-size: 14px;

    .mint-ai-chat-detail-header-name {
        flex-grow: 1;
    }

    img {
        object-fit: cover;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
}

.mint-ai-chat-detail-content {
    flex-grow: 1;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    overflow: scroll;
    scroll-behavior: smooth;

    .mint-ai-chat-detail-date-title {
        font-weight: 600;
        text-align: center;
        padding: 8px 0px;
    }

    .mint-ai-chat-message {
        display: flex;
        flex-direction: column;
        gap: 2px;
        .mint-ai-chat-message-content {
            max-width: 75%;
            background: rgb(var(--v-theme-primary-light));
            color: #000d;
            font-size: 16px;
            width: fit-content;
            padding: 8px 16px;
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
            border-radius: 16px 16px 16px 16px;
            letter-spacing: 0.5px;
        }
        .mint-ai-chat-message-footer {
            font-size: 10px;
            padding: 0px 12px;
            color: #00000099;
            letter-spacing: 0.33px;
        }
    }

    .mint-ai-chat-message-owner {
        align-items: flex-end;
        .mint-ai-chat-message-content {
            background: #00654ec9;
            color: white;
            border-radius: 16px 16px 16px 16px;
        }
        .mint-ai-chat-message-footer {
            text-align: end;
        }
    }
}

.confirmation-modal {
    background-color: #e8f4ec;
    padding: 16px;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    margin-top: 16px;
}

.confirmation-text {
    font-weight: 600;
    font-size: 16px;
    color: black;
}

.buttonContainer {
    display: flex;
    justify-content: center;
    gap: 16px;
    width: 100%;
}

.acceptButton,
.rejectButton {
    max-width: 45%;
    color: white;
    padding: 8px 16px;
    border-radius: 16px;
    text-align: center;
    letter-spacing: 0.5px;
    transition: background-color 0.3s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.acceptButton {
    background: green;
}
.acceptButton:hover {
    background: darkgreen;
}

.rejectButton {
    background: red;
}
.rejectButton:hover {
    background: darkred;
}

.acceptButton:active {
    background: lightgreen;
    box-shadow: 0 0 10px lightgreen;
}

.rejectButton:active {
    background: lightcoral;
    box-shadow: 0 0 10px lightcoral;
}
.mint-ai-chat-detail-footer {
    display: flex;
    gap: 8px;
    padding: 16px;
    border-top: thin solid #0002;
    background: #ffffff;
}
</style>
