<template>
    <div>
        <div class="chat-header">
            <MintButton variant="nav" icon="mdi-arrow-left" @click="chat.view = 'default'" />
            <MintSearch
                v-model="chat.conversationsSearchQuery"
                :label="languages.label('LBL_MINT4_CHAT_SEARCH_USER')"
                @clear="chat.conversationsSearchQuery = ''"
            />
        </div>
        <div>
            <div
                class="mint-ai-chat-user mint-ai-chat-list-item"
                v-for="conversation in chat.conversationsList"
                :key="conversation.id"
                v-ripple="{ class: 'text-primary' }"
            >
                <v-icon icon="mdi-account" @click="chat.openPrivateConversation(conversation)" />
                <div v-text="conversation.name" @click="chat.openPrivateConversation(conversation)"></div>
                <MintButton icon="mdi-delete" class="delete-icon" @click="chat.deleteConversation(conversation.id)" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useMintAiChatStore } from './MintAiChatStore'
import MintButton from '@/components/MintButtons/MintButton.vue'
import MintSearch from '@/components/MintSearch.vue'
import { useLanguagesStore } from '@/store/languages'

const chat = useMintAiChatStore()
const languages = useLanguagesStore()
</script>

<style scoped lang="scss">
.chat-header {
    display: flex;
    gap: 8px;
    padding: 8px 16px 8px 8px;
    align-items: center;
    border-bottom: thin solid #0002;
}

.mint-ai-chat-create-group {
    border-bottom: thin solid #0002;
}

.mint-ai-chat-list-item {
    display: flex;
    gap: 16px;
    align-items: center;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 150ms ease-in-out;
    color: rgb(var(--v-theme-secondary));
    font-weight: 600;
    font-size: 14px;

    &:hover {
        background: rgb(var(--v-theme-primary-light));
    }
    :first-child {
        object-fit: cover;
        width: 32px;
        height: 32px;
        font-size: 24px;
        border-radius: 50%;
    }
    :last-child {
    }
}

.mint-ai-chat-user {
    position: relative;
    &::after {
        position: absolute;
        content: '';
        display: block;
        bottom: 0px;
        right: 12px;
        left: 12px;
        border-bottom: thin solid #0002;
    }
    .delete-icon {
        margin-left: auto;
        cursor: pointer;
        color: rgb(128, 1, 1);
        &:hover {
            color: rgb(84, 0, 0);
        }
    }
}
</style>
