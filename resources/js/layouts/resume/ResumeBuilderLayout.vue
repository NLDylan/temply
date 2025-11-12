<script setup lang="ts">
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar'
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'

interface Props {
  title: string
  description?: string
  insetClass?: HTMLAttributes['class']
  contentClass?: HTMLAttributes['class']
  previewClass?: HTMLAttributes['class']
  formClass?: HTMLAttributes['class']
}

const props = withDefaults(defineProps<Props>(), {
  description: undefined,
  insetClass: undefined,
  contentClass: undefined,
  previewClass: undefined,
  formClass: undefined,
})
</script>

<template>
  <SidebarProvider class="bg-muted/20">
    <div class="flex min-h-svh w-full">
      <slot name="sidebar" />

      <SidebarInset :class="cn('gap-0', props.insetClass)">
        <header
          class="flex flex-col gap-3 border-b border-border bg-white px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between dark:bg-zinc-900"
        >
          <div class="flex items-start gap-3 lg:items-center">
            <SidebarTrigger class="mt-1 lg:mt-0" />

            <div class="space-y-1">
              <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-semibold text-foreground sm:text-2xl">
                  {{ title }}
                </h1>
                <slot name="title-addon" />
              </div>

              <p v-if="props.description" class="max-w-prose text-sm text-muted-foreground">
                {{ props.description }}
              </p>

              <slot name="subheader" />
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <slot name="actions" />
          </div>
        </header>

        <div
          :class="cn(
            'flex flex-1 flex-col gap-6 overflow-hidden px-4 py-6 sm:px-6',
            props.contentClass,
          )"
        >
          <slot name="meta" />

          <div class="flex flex-1 flex-col gap-6 overflow-hidden lg:flex-row">
            <section
              :class="cn(
                'flex-1 overflow-y-auto px-1 py-2 sm:px-2',
                'scrollbar-thin scrollbar-thumb-muted-foreground/20 hover:scrollbar-thumb-muted-foreground/30',
                props.formClass,
              )"
            >
              <slot name="form" />
            </section>

            <aside
              :class="cn(
                'rounded-2xl border border-border/60 bg-muted/40 p-4 shadow-inner backdrop-blur-sm',
                'lg:w-[420px] lg:min-w-[360px] lg:overflow-y-auto',
                'scrollbar-thin scrollbar-thumb-muted-foreground/20 hover:scrollbar-thumb-muted-foreground/30',
                props.previewClass,
              )"
            >
              <slot name="preview" />
            </aside>
          </div>
        </div>
      </SidebarInset>
    </div>
  </SidebarProvider>
</template>

