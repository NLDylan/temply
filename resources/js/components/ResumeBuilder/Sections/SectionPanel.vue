<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Skeleton } from '@/components/ui/skeleton'
import { cn } from '@/lib/utils'
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  title: string
  description?: string
  status?: 'draft' | 'complete' | 'attention'
  hint?: string
  loading?: boolean
  actionable?: boolean
}>(), {
  description: undefined,
  status: undefined,
  hint: undefined,
  loading: false,
  actionable: false,
})

const statusBadge = computed(() => {
  if (!props.status) {
    return null
  }

  if (props.status === 'complete') {
    return {
      label: 'Complete',
      variant: 'secondary' as const,
    }
  }

  if (props.status === 'attention') {
    return {
      label: 'Needs attention',
      variant: 'destructive' as const,
    }
  }

  return {
    label: 'Draft',
    variant: 'outline' as const,
  }
})
</script>

<template>
  <section
    :class="cn(
      'group/section relative flex flex-col gap-6 rounded-2xl border border-border/70 bg-background/95 p-6 shadow-[0_20px_45px_-40px_rgb(15,23,42)] transition duration-200',
      'hover:border-brand/50 hover:shadow-[0_22px_60px_-48px_rgb(59,130,246)]',
      loading && 'pointer-events-none opacity-75',
    )"
  >
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div class="space-y-2">
        <div class="flex items-center gap-3">
          <h2 class="text-lg font-semibold tracking-tight sm:text-xl">
            {{ title }}
          </h2>
          <Badge
            v-if="statusBadge"
            :variant="statusBadge.variant"
            class="hidden border-dashed px-2 py-0.5 text-[11px] uppercase tracking-wide sm:inline-flex"
          >
            {{ statusBadge.label }}
          </Badge>
        </div>

        <p v-if="description" class="max-w-2xl text-sm text-muted-foreground sm:text-[15px]">
          {{ description }}
        </p>

        <slot name="metadata" />
      </div>

      <div v-if="props.actionable" class="flex flex-wrap items-center justify-end gap-2">
        <slot name="actions" />
      </div>
    </div>

    <div v-if="hint" class="rounded-xl border border-dashed border-border/70 bg-muted/40 px-4 py-3 text-xs text-muted-foreground">
      {{ hint }}
    </div>

    <div v-if="loading" class="grid gap-3">
      <Skeleton class="h-9 w-3/5 rounded-xl" />
      <Skeleton class="h-9 w-full rounded-xl" />
      <Skeleton class="h-16 w-full rounded-xl" />
    </div>
    <div v-else class="grid gap-6">
      <slot />
    </div>

    <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-dashed border-border/70 pt-4 text-xs text-muted-foreground/90">
      <slot name="footer">
        <span>Remember to keep this section concise and outcome-focused.</span>
      </slot>
    </footer>
  </section>
</template>

