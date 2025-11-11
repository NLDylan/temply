<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
</script>

<template>
    <div
        v-if="variant === 'header'"
        class="bg-background flex min-h-screen w-full flex-col"
    >
        <slot />
    </div>
    <SidebarProvider
        v-else
        class="bg-background"
        :default-open="isOpen"
    >
        <slot />
    </SidebarProvider>
</template>
