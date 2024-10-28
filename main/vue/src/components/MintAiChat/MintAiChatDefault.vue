<template>
    <div>
        <div class="chat-header">
            <MintSearch
                v-model="aiChat.conversationsSearchQuery"
                :label="languages.label('LBL_MINT4_CHAT_SEARCH_CONVERSATION')"
                @clear="aiChat.conversationsSearchQuery = ''"
            />
            <MintButton variant="nav" icon="mdi-square-edit-outline" @click="aiChat.view = 'list'" />
            <MintButton variant="primary" icon="mdi-plus" @click="createNewChat" />
        </div>
        <div class="mint-ai-chat-conversations">
            <div
                :class="{
                    'mint-ai-chat-conversation': true,
                    'mint-ai-chat-conversation-unread':
                        conv.messages?.at(-1)?.author_id !== auth.user?.id &&
                        (!conv.date_read || conv.date_read < (conv.messages?.at(-1)?.date_entered || conv.date_active)),
                }"
                v-for="conv in aiChat.conversationsList"
                :key="conv.id"
                v-ripple="{ class: 'text-primary' }"
                @click="aiChat.openDetailView(conv.id)"
            >
                <div class="mint-ai-chat-conversation-content">
                    <div class="mint-ai-chat-conversation-header">
                        <div class="mint-ai-chat-conversation-name" v-text="conv.name" />
                        <div class="mint-ai-chat-conversation-status">
                            <div v-text="toRelativeDate(conv.messages?.at(-1)?.date_entered || conv.date_active)" />
                            <div
                                v-if="
                                    conv.messages?.at(-1)?.author_id !== auth.user?.id &&
                                    (!conv.date_read ||
                                        conv.date_read < (conv.messages?.at(-1)?.date_entered || conv.date_active))
                                "
                                class="mint-ai-chat-conversation-unread-dot"
                            />
                        </div>
                    </div>
                    <div
                        class="mint-ai-chat-conversation-message"
                        v-if="conv.messages?.at(-1)"
                        v-text="conv.messages.at(-1)?.text"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { DateTime } from 'luxon'
import { useMintAiChatStore } from './MintAiChatStore'
import MintButton from '@/components/MintButtons/MintButton.vue'
import MintSearch from '@/components/MintSearch.vue'
import { useLanguagesStore } from '@/store/languages'
import { useAuthStore } from '@/store/auth'
import { usePreferencesStore } from '@/store/preferences'

const aiChat = useMintAiChatStore()
const languages = useLanguagesStore()
const auth = useAuthStore()
const preferences = usePreferencesStore()

function toRelativeDate(date: string) {
    const dt = DateTime.fromSQL(date, { zone: 'utc' })
    if (dt.diffNow('days').days >= -5) {
        return dt.toRelativeCalendar()
    }
    return dt.toFormat(preferences.user?.date_format || 'dd.MM.yyyy')
}

async function createNewChat() {
    await aiChat.createNewConversation()
}

aiChat.fetchConversations()
</script>

<style scoped lang="scss">
.chat-header {
    display: flex;
    gap: 8px;
    padding: 8px 16px 8px 8px;
    align-items: center;
    border-bottom: thin solid #0002;
}
.mint-ai-chat-conversations {
    display: flex;
    flex-direction: column;

    .mint-ai-chat-conversation {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px;
        transition: all 150ms ease-in-out;
        cursor: pointer;
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

        &:hover {
            background: rgb(var(--v-theme-primary-light));
        }

        &-unread {
            background: rgb(var(--v-theme-primary-lighter));
        }

        .mint-ai-chat-conversation-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            min-width: 40px;
            width: 40px;
            height: 40px;

            img {
                object-fit: cover;
                width: 40px;
                height: 40px;
                border-radius: 50%;
            }
        }

        .mint-ai-chat-conversation-content {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 4px;
            overflow: hidden;

            .mint-ai-chat-conversation-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 16px;

                .mint-ai-chat-conversation-name {
                    font-weight: 600;
                    color: rgb(var(--v-theme-secondary));
                    font-size: 14px;
                    letter-spacing: 0.43px;
                }

                .mint-ai-chat-conversation-status {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                    font-size: 12px;
                    color: rgb(var(--v-theme-primary));
                    font-weight: 600;
                    letter-spacing: 0.4px;
                    white-space: nowrap;

                    .mint-ai-chat-conversation-unread-dot {
                        background: rgb(var(--v-theme-error));
                        border-radius: 50%;
                        width: 12px;
                        height: 12px;
                    }
                }
            }

            .mint-ai-chat-conversation-message {
                font-size: 12px;
                letter-spacing: 0.4px;
                white-space: nowrap;
                overflow-x: hidden;
                text-overflow: ellipsis;
            }
        }
    }
}
</style>
