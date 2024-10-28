<template>
    <component :is="aiChatStore.currentView" />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useMintAiChatStore } from './MintAiChatStore'
import { useWebSocketServiceStore } from './websocketServiceStore'

const aiChatStore = useMintAiChatStore()
const webSocketStore = useWebSocketServiceStore()

onMounted(async () => {
    await webSocketStore.initConfig()
})
</script>

<style scoped lang="scss"></style>
