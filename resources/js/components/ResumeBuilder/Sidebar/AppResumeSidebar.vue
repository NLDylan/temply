<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuBadge,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarRail,
  SidebarSeparator,
} from '@/components/ui/sidebar'
import { cn } from '@/lib/utils'
import { computed } from 'vue'
import AppLogo from '@/components/AppLogo.vue'
import {
  ArrowRight,
  CheckCircle2,
  LifeBuoy,
  Pin,
} from 'lucide-vue-next'
import type { ResumeSection } from './sections'
import { resumeSectionGroups } from './sections'

const props = withDefaults(defineProps<{
  activeSection?: string
  completedSections?: string[]
  pinnedSections?: string[]
  class?: string
}>(), {
  activeSection: undefined,
  completedSections: () => [],
  pinnedSections: () => [],
  class: undefined,
})

const emit = defineEmits<{
  select: [sectionId: string]
  'open-settings': []
}>()

function handleSelect(section: ResumeSection) {
  emit('select', section.id)
}
</script>

<template>
  <Sidebar
    collapsible="icon"
    variant="sidebar"
    :class="cn('border-border/60 bg-sidebar text-sidebar-foreground', props.class)"
  >
    <SidebarHeader class="px-3 pb-3 pt-6">
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton
            size="lg"
            class="justify-start group-data-[collapsible=icon]/sidebar:justify-center group-data-[collapsible=icon]/sidebar:px-0 group-data-[collapsible=icon]/sidebar:[&>div]:mx-auto"
          >
            <AppLogo />
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent class="px-2">
      <SidebarGroup
        v-for="group in resumeSectionGroups"
        :key="group.id"
        class="group-data-[collapsible=icon]:border-b group-data-[collapsible=icon]:border-sidebar-border/60"
      >
        <SidebarGroupLabel class="px-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
          {{ group.label }}
        </SidebarGroupLabel>

        <SidebarGroupContent>
          <SidebarMenu>
            <SidebarMenuItem v-for="section in group.items" :key="section.id" class="relative">
              <SidebarMenuButton
                :is-active="props.activeSection === section.id"
                :tooltip="section.label"
                class="justify-start gap-3 group-data-[collapsible=icon]/sidebar:justify-center group-data-[collapsible=icon]/sidebar:gap-0 group-data-[collapsible=icon]/sidebar:px-0 group-data-[collapsible=icon]/sidebar:[&>*]:mx-auto"
                @click="handleSelect(section)"
              >
                <component :is="section.icon" class="size-4" />
                <span class="flex-1 truncate group-data-[collapsible=icon]/sidebar:hidden">
                  {{ section.label }}
                </span>
              </SidebarMenuButton>

            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
    </SidebarContent>

    <SidebarSeparator class="mx-3" />

    <SidebarFooter class="px-3 pb-8 pt-4 group-data-[collapsible=icon]:hidden">
      <div class="space-y-3 rounded-2xl border border-sidebar-border/70 bg-sidebar-accent/50 p-4 text-xs text-sidebar-foreground/90">
        <div class="flex items-center gap-2">
          <LifeBuoy class="size-4" />
          <span class="font-medium">Need a hand?</span>
        </div>
        <p class="leading-relaxed text-sidebar-foreground/80">
          Access resume writing tips, sample bullet points, and tone suggestions curated by our career team.
        </p>
        <Button variant="secondary" size="sm" class="w-full justify-between" @click="emit('open-settings')">
          Contact support
          <ArrowRight class="size-4" />
        </Button>
      </div>
    </SidebarFooter>

    <SidebarRail />
  </Sidebar>
</template>

