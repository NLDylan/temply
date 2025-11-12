<script setup lang="ts">
import NavUser from '@/components/NavUser.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarRail,
} from '@/components/ui/sidebar'
import { cn } from '@/lib/utils'
import { Link } from '@inertiajs/vue3'
import AppLogo from '@/components/AppLogo.vue'
import { dashboard } from '@/routes'
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
            as-child
          >
            <Link :href="dashboard()">
              <AppLogo />
            </Link>
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
        <SidebarGroupLabel class="px-2 text-xs font-semibold uppercase tracking-wide text-primary">
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

    <SidebarFooter class="mt-auto p-2">
      <NavUser :show-dashboard-link="true" />
    </SidebarFooter>

    <SidebarRail />
  </Sidebar>
</template>

