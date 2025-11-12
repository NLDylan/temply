<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import ResumeBuilderLayout from '@/layouts/resume/ResumeBuilderLayout.vue'
import {
  AppResumeSidebar,
  resumeSectionGroups,
} from '@/components/ResumeBuilder/Sidebar'
import type {
  ResumeSection,
  ResumeSectionGroup,
} from '@/components/ResumeBuilder/Sidebar'
import {
  BasicInformationSection,
  EducationSection,
  LanguagesSection,
  ProfessionalSummarySection,
  SkillsSection,
  WorkExperienceSection,
} from '@/components/ResumeBuilder/Sections'
import type { Component } from 'vue'
import { computed, ref, watchEffect } from 'vue'
import { Download, Link2, Share2, ShieldCheck } from 'lucide-vue-next'

interface ResumeSectionEntry {
  group: ResumeSectionGroup
  section: ResumeSection
}

const activeSection = ref('basic-info')
const completedSections = ref(['basic-info', 'professional-summary'])
const pinnedSections = ref(['work-experience'])

const sectionComponents: Record<string, Component> = {
  'basic-info': BasicInformationSection,
  'professional-summary': ProfessionalSummarySection,
  'focus-roles': ProfessionalSummarySection,
  'work-experience': WorkExperienceSection,
  projects: WorkExperienceSection,
  achievements: WorkExperienceSection,
  education: EducationSection,
  skills: SkillsSection,
  languages: LanguagesSection,
  certifications: EducationSection,
  volunteering: WorkExperienceSection,
  'custom-sections': ProfessionalSummarySection,
} as const

const sectionEntries = computed((): ResumeSectionEntry[] =>
  resumeSectionGroups.flatMap((group) =>
    group.items.map((section) => ({
      group,
      section,
    })),
  ),
)

const sectionsMap = computed(() => {
  const map = new Map<string, ResumeSectionEntry>()
  sectionEntries.value.forEach((entry) => {
    map.set(entry.section.id, entry)
  })

  return map
})

const firstSectionId = computed(() => sectionEntries.value[0]?.section.id ?? null)
const activeSectionEntry = computed(() => sectionsMap.value.get(activeSection.value))
const activeSectionComponent = computed<Component | null>(
  () => sectionComponents[activeSection.value] ?? null,
)

watchEffect(() => {
  if (!firstSectionId.value) {
    return
  }

  if (!sectionsMap.value.has(activeSection.value)) {
    activeSection.value = firstSectionId.value
  }
})

const lastSavedAt = ref(new Date())

function handleSectionSelect(sectionId: string) {
  if (!sectionsMap.value.has(sectionId)) {
    return
  }

  activeSection.value = sectionId
}

function markSaved() {
  lastSavedAt.value = new Date()
}
</script>

<template>
  <Head title="Resume Builder" />

  <ResumeBuilderLayout
    title="Resume builder"
  >
    <template #title-addon>
      <div class="hidden flex-wrap items-center gap-3 text-xs text-muted-foreground sm:flex">
        <div class="flex items-center gap-1 text-emerald-500">
          <ShieldCheck class="size-3.5" />
          <span class="font-medium text-emerald-600 dark:text-emerald-400">
            Autosave on
          </span>
        </div>
        <Badge variant="outline" class="border-dashed px-2 py-0.5 uppercase tracking-wide">
          Draft
        </Badge>
        <span class="text-muted-foreground/80">
          Saved {{ lastSavedAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
        </span>
      </div>
    </template>

    <template #sidebar>
      <AppResumeSidebar
        :active-section="activeSection"
        :completed-sections="completedSections"
        :pinned-sections="pinnedSections"
        @select="handleSectionSelect"
      />
    </template>

    <template #actions>
      <Button variant="ghost" size="sm" class="gap-2 text-xs">
        <Share2 class="size-4" />
        Invite reviewer
      </Button>
      <Button variant="outline" size="sm" class="gap-2">
        <Link2 class="size-4" />
        Share draft
      </Button>
      <Button size="sm" class="gap-2" @click="markSaved">
        <Download class="size-4" />
        Export PDF
      </Button>
    </template>

      <template #form>
        <div class="flex h-full flex-col gap-6">
          <div class="rounded-2xl border border-border/60 bg-background/90 p-4 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div class="space-y-1">
                <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground/80">
                  Currently editing
                </p>
                <h2 class="text-lg font-semibold text-foreground">
                  {{ activeSectionEntry?.section.label ?? 'Select a section' }}
                </h2>
                <p
                  v-if="activeSectionEntry?.section.description"
                  class="max-w-prose text-sm text-muted-foreground"
                >
                  {{ activeSectionEntry.section.description }}
                </p>
              </div>

              <Badge
                v-if="activeSectionEntry?.section.badge"
                variant="secondary"
                class="rounded-full px-3 py-1 text-[0.625rem] uppercase tracking-wide"
              >
                {{ activeSectionEntry.section.badge }}
              </Badge>
            </div>
          </div>

          <div class="flex-1">
            <KeepAlive>
              <component
                v-if="activeSectionComponent"
                :is="activeSectionComponent"
                :key="activeSection"
              />
            </KeepAlive>

            <div
              v-else
              class="flex h-full items-center justify-center rounded-2xl border border-dashed border-border/70 bg-background/60 p-8 text-sm text-muted-foreground"
            >
              Select a section from the sidebar to start editing.
            </div>
          </div>
        </div>
      </template>

    <template #preview>
      <div class="rounded-2xl border border-border/70 bg-gradient-to-br from-brand/10 via-background to-background p-4 text-xs text-muted-foreground">
        <p class="mb-2 text-sm font-semibold text-foreground">
          Layout controls
        </p>
        <p>
          Experiment with color accents, typography presets, and spacing. Preview updates instantly and export when you are ready.
        </p>
        <div class="mt-3 grid gap-2">
          <div class="flex items-center justify-between rounded-xl border border-border/50 bg-background/80 px-3 py-2 text-xs">
            <span>Primary accent</span>
            <span class="font-medium text-foreground">Brand</span>
          </div>
          <div class="flex items-center justify-between rounded-xl border border-border/50 bg-background/80 px-3 py-2 text-xs">
            <span>Typography</span>
            <span class="font-medium text-foreground">Grotesk · 12/16</span>
          </div>
          <div class="flex items-center justify-between rounded-xl border border-border/50 bg-background/80 px-3 py-2 text-xs">
            <span>Spacing</span>
            <span class="font-medium text-foreground">Compact</span>
          </div>
        </div>
      </div>
    </template>
  </ResumeBuilderLayout>
</template>

