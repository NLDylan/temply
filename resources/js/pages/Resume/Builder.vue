<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import ResumeBuilderLayout from '@/layouts/resume/ResumeBuilderLayout.vue'
import { AppResumeSidebar, resumeSectionGroups } from '@/components/ResumeBuilder/Sidebar'
import {
  BasicInformationSection,
  EducationSection,
  LanguagesSection,
  ProfessionalSummarySection,
  SkillsSection,
  WorkExperienceSection,
} from '@/components/ResumeBuilder/Sections'
import { useMagicKeys } from '@vueuse/core'
import type { Component } from 'vue'
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Download, Link2, Share2, ShieldCheck } from 'lucide-vue-next'

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

const flatSections = computed(() =>
  resumeSectionGroups.flatMap((group) => group.items),
)

const lastSavedAt = ref(new Date())
const sectionElements = new Map<string, HTMLElement>()
let observer: IntersectionObserver | null = null
const visibleSections = new Map<string, number>()

function assignSectionRef(sectionId: string, el: HTMLElement | null) {
  const existing = sectionElements.get(sectionId)
  if (existing && observer) {
    observer.unobserve(existing)
    sectionElements.delete(sectionId)
  }

  if (el) {
    el.id = `resume-section-${sectionId}`
    sectionElements.set(sectionId, el)
    observer?.observe(el)
  }
}

function setActiveSectionFromObserver() {
  if (!visibleSections.size) {
    return
  }

  const [topSection] = Array.from(visibleSections.entries())
    .sort((a, b) => b[1] - a[1])

  if (!topSection) {
    return
  }

  const sectionId = topSection[0].replace('resume-section-', '')
  if (sectionsMap.value.has(sectionId)) {
    activeSection.value = sectionId
  }
}

function handleSectionSelect(sectionId: string) {
  activeSection.value = sectionId
  const element = document.getElementById(`resume-section-${sectionId}`)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

function markSaved() {
  lastSavedAt.value = new Date()
}

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          visibleSections.set(
            entry.target.id,
            entry.intersectionRatio,
          )
        } else {
          visibleSections.delete(entry.target.id)
        }
      }

      setActiveSectionFromObserver()
    },
    {
      rootMargin: '-20% 0px -60% 0px',
      threshold: [0.25, 0.5, 0.75, 1],
    },
  )

  sectionElements.forEach((element) => observer?.observe(element))
})

onBeforeUnmount(() => {
  observer?.disconnect()
  observer = null
  visibleSections.clear()
  sectionElements.clear()
})

const { meta_b } = useMagicKeys({ passive: false })

watch(meta_b, (pressed) => {
  if (pressed) {
    handleSectionSelect(activeSection.value)
  }
})
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
      <div class="grid gap-8">
        <div
          v-for="section in flatSections"
          :key="section.id"
          :ref="(el) => assignSectionRef(section.id, el as HTMLElement | null)"
          :class="[
            'scroll-mt-28 transition',
            activeSection === section.id ? 'ring-2 ring-brand/60 ring-offset-1 ring-offset-background' : 'opacity-95',
          ]"
        >
          <component :is="sectionComponents[section.id]" />
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

