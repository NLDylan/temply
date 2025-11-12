<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
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
  <Card
    :class="cn(
      'resume-card group/section relative gap-0 p-0 transition-colors duration-200 hover:border-brand/35',
      loading && 'pointer-events-none opacity-75',
    )"
  >
    <CardHeader class="gap-4 py-6">
      <div class="space-y-2">
        <div class="flex items-center gap-3">
          <CardTitle class="text-lg font-semibold tracking-tight sm:text-xl">
            {{ title }}
          </CardTitle>
          <Badge
            v-if="statusBadge"
            :variant="statusBadge.variant"
            class="hidden border-dashed px-2 py-0.5 text-[11px] uppercase tracking-wide sm:inline-flex"
          >
            {{ statusBadge.label }}
          </Badge>
        </div>

        <CardDescription v-if="description" class="max-w-2xl text-sm sm:text-[15px]">
          {{ description }}
        </CardDescription>

        <slot name="metadata" />
      </div>

      <CardAction
        v-if="props.actionable"
        class="flex flex-wrap items-center gap-2"
      >
        <slot name="actions" />
      </CardAction>
    </CardHeader>

    <CardContent class="space-y-6 pb-6 pt-0">
      <div
        v-if="hint"
        class="rounded-xl border border-dashed border-border/70 bg-muted/40 px-4 py-3 text-xs text-muted-foreground"
      >
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
    </CardContent>

    <CardFooter class="border-t border-dashed border-border/70 pt-6 text-xs text-muted-foreground/90">
      <slot name="footer">
        <span>Remember to keep this section concise and outcome-focused.</span>
      </slot>
    </CardFooter>
  </Card>
</template>

