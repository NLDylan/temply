<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
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
  AchievementsSection,
  BasicInformationSection,
  EducationSection,
  LanguagesSection,
  ProfessionalSummarySection,
  SkillsSection,
  VolunteeringSection,
  WorkExperienceSection,
} from '@/components/ResumeBuilder/Sections'
import type { Component } from 'vue'
import { computed, ref, watch, watchEffect } from 'vue'
import { Download, Link2, Save, Share2, ShieldCheck } from 'lucide-vue-next'
import { update as updateResume } from '@/routes/resumes'
import type {
  ResumeBasicInformationFormData,
  ResumeResource,
} from '@/types/resume'

interface ResumeSectionEntry {
  group: ResumeSectionGroup
  section: ResumeSection
}

const props = defineProps<{
  resume: ResumeResource
}>()

const activeSection = ref('basic-info')
const completedSections = ref(['basic-info', 'professional-summary'])
const pinnedSections = ref(['work-experience'])

function generateLinkId(): string {
  if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
    return crypto.randomUUID()
  }

  return Math.random().toString(36).slice(2)
}

function resolveFormData(resume: ResumeResource): ResumeBasicInformationFormData {
  return {
    headline: resume.headline ?? '',
    location: resume.location ?? '',
    profile: {
      full_name: resume.profile?.full_name ?? '',
      email: resume.profile?.email ?? '',
      phone: resume.profile?.phone ?? '',
      working_rights: resume.profile?.working_rights ?? '',
      contact_links: (resume.profile?.contact_links ?? []).map((link) => ({
        id: link.id ?? generateLinkId(),
        label: link.label ?? '',
        url: link.url ?? '',
      })),
    },
  }
}

const form = useForm<ResumeBasicInformationFormData>(
  `ResumeBasicInfo:${props.resume.id}`,
  resolveFormData(props.resume),
)

const sectionComponents: Record<string, Component> = {
  'basic-info': BasicInformationSection,
  'professional-summary': ProfessionalSummarySection,
  'focus-roles': ProfessionalSummarySection,
  'work-experience': WorkExperienceSection,
  projects: WorkExperienceSection,
  achievements: AchievementsSection,
  education: EducationSection,
  skills: SkillsSection,
  languages: LanguagesSection,
  certifications: EducationSection,
  volunteering: VolunteeringSection,
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

const sectionProps = computed<Record<string, Record<string, unknown>>>(() => ({
  'basic-info': {
    form,
  },
  volunteering: {
    volunteering: props.resume.volunteering,
  },
  achievements: {
    achievements: props.resume.achievements,
  },
}))

watchEffect(() => {
  if (!firstSectionId.value) {
    return
  }

  if (!sectionsMap.value.has(activeSection.value)) {
    activeSection.value = firstSectionId.value
  }
})

const resumeTitle = computed(() => props.resume?.title ?? 'Resume builder')
const lastSavedAt = ref(
  props.resume?.updated_at ? new Date(props.resume.updated_at) : new Date(),
)

watch(
  () => props.resume.updated_at,
  (updatedAt) => {
    if (!updatedAt) {
      return
    }

    lastSavedAt.value = new Date(updatedAt)
  },
)

watch(
  () => props.resume,
  (next) => {
    const data = resolveFormData(next)
    form.defaults(data)
    form.headline = data.headline
    form.location = data.location
    form.profile = data.profile
  },
  { deep: true },
)

function handleSectionSelect(sectionId: string) {
  if (!sectionsMap.value.has(sectionId)) {
    return
  }

  activeSection.value = sectionId
}

function saveBasicInformation() {
  const contactLinks = form.profile?.contact_links ?? []

  form.profile = {
    ...form.profile,
    contact_links: contactLinks.map((link) => ({
      id: link.id ?? generateLinkId(),
      label: link.label ?? '',
      url: link.url ?? '',
    })),
  }

  form.submit('patch', updateResume.url({ resume: props.resume.id }), {
    preserveScroll: true,
    onSuccess: () => {
      lastSavedAt.value = new Date()
    },
  })
}

function markSaved() {
  lastSavedAt.value = new Date()
}
</script>

<template>

  <Head :title="resumeTitle" />

  <ResumeBuilderLayout :title="resumeTitle">
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
      <AppResumeSidebar :active-section="activeSection" :completed-sections="completedSections"
        :pinned-sections="pinnedSections" @select="handleSectionSelect" />
    </template>

    <template #actions>
      <Button size="sm" class="gap-2" :disabled="form.processing" @click="saveBasicInformation">
        <Save class="size-4" />
        Save changes
      </Button>
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
        <div class="flex-1">
          <KeepAlive v-if="activeSectionComponent">
            <component :is="activeSectionComponent" :key="activeSection" v-bind="sectionProps[activeSection] ?? {}" />
          </KeepAlive>
          <div v-else
            class="flex h-full items-center justify-center rounded-2xl border border-dashed border-border/70 bg-background/60 p-8 text-sm text-muted-foreground">
            Select a section from the sidebar to start editing.
          </div>
        </div>
      </div>
    </template>

    <template #preview>
      <Card class="resume-card border border-border/70 bg-muted/30 p-0 text-xs text-muted-foreground">
        <CardContent class="px-4 py-4">
          <p class="mb-2 text-sm font-semibold text-foreground">
            Layout controls
          </p>
          <p>
            Experiment with color accents, typography presets, and spacing. Preview updates instantly and export when
            you are ready.
          </p>
          <div class="mt-3 grid gap-2">
            <div
              class="flex items-center justify-between rounded-xl border border-border/50 bg-background/80 px-3 py-2 text-xs">
              <span>Primary accent</span>
              <span class="font-medium text-foreground">Brand</span>
            </div>
            <div
              class="flex items-center justify-between rounded-xl border border-border/50 bg-background/80 px-3 py-2 text-xs">
              <span>Typography</span>
              <span class="font-medium text-foreground">Grotesk · 12/16</span>
            </div>
            <div
              class="flex items-center justify-between rounded-xl border border-border/50 bg-background/80 px-3 py-2 text-xs">
              <span>Spacing</span>
              <span class="font-medium text-foreground">Compact</span>
            </div>
          </div>
        </CardContent>
      </Card>
    </template>
  </ResumeBuilderLayout>
</template>
