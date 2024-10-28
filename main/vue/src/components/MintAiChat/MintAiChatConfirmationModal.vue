<template>
    <div class="confirmation-modal" ref="confirmationModal">
        <p class="confirmation-text">{{ webSocketStore.acceptRequestMessage }}</p>
        <div class="buttonContainer">
            <button class="acceptButton" @click="confirmToolUse">{{ languages.label('LBL_ACCEPT') }}</button>
            <button class="rejectButton" @click="rejectToolUse">{{ languages.label('LBL_REJECT') }}</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useMintAiChatStore } from './MintAiChatStore'
import { useWebSocketServiceStore } from './websocketServiceStore'
import { useLanguagesStore } from '@/store/languages'

const chat = useMintAiChatStore()
const confirmationModal = ref<HTMLDivElement | null>(null)
const webSocketStore = useWebSocketServiceStore()
const languages = useLanguagesStore()

function confirmToolUse() {
    const conversationId = chat.activeConversation?.id || ''
    const messageIdAxios = Date.now().toString()
    chat.sendMessage(conversationId, 'Tool confirmed', messageIdAxios, 'tool_confirm')
    webSocketStore.isAcceptRejectVisible = false
}

function rejectToolUse() {
    const conversationId = chat.activeConversation?.id || ''
    const rejectionMsg = webSocketStore.acceptRequestMessage.trim() || 'Tool use rejected'
    const messageIdAxios = Date.now().toString()
    chat.sendMessage(conversationId, rejectionMsg, messageIdAxios, 'tool_reject')
    webSocketStore.isAcceptRejectVisible = false
}

onMounted(() => {
    if (confirmationModal.value) {
        confirmationModal.value.scrollIntoView({ behavior: 'smooth' })
    }
})
</script>

<style scoped lang="scss">
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
</style>
