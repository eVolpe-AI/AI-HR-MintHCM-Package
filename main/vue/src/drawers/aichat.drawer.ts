import { useMintAiChatStore } from '../components/MintAiChat/MintAiChatStore.ts'
import MintAiChat from '../components/MintAiChat/MintAiChat.vue'

export default {
    icon: 'mdi-robot',
    component: MintAiChat,
    badge: () => useMintAiChatStore().unreadConversationsCount,
    isAvaliable: () => true,
}
